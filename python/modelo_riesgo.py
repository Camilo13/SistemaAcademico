"""
modelo_riesgo.py
Llamado desde Laravel via Symfony Process:
  python3 modelo_riesgo.py <json_args>

json_args = {
  "excel_path": "/ruta/al/archivo.xlsx",
  "periodo": 1,
  "p1": 0.33,
  "p2": 0.33,
  "p3": 0.34,
  "output_dir": "/ruta/salida"
}
Retorna JSON por stdout.
"""

import sys
import os
import json
import warnings

import numpy as np
import pandas as pd

from scipy.stats import norm
from sklearn.ensemble import RandomForestRegressor
from sklearn.model_selection import train_test_split
from sklearn.metrics import mean_absolute_error, r2_score

from reportlab.platypus import (
    SimpleDocTemplate, Paragraph, Spacer,
    Table, TableStyle, PageBreak
)
from reportlab.lib.styles import getSampleStyleSheet
from reportlab.lib import colors
from reportlab.lib.pagesizes import letter
from datetime import datetime

warnings.filterwarnings("ignore")
np.random.seed(42)


# ── Funciones idénticas al modelo original del profe ──────────────

def validar_porcentajes(p1, p2, p3):
    total = round(p1 + p2 + p3, 4)
    if total != 1.0:
        raise ValueError(f"Los porcentajes deben sumar 100%. Suma: {total*100:.2f}%")


def calcular_probabilidad(nota, std, umbral=3.0):
    std = max(std, 0.5)
    return float(1 - norm.cdf(umbral, loc=nota, scale=std))


def clasificar_riesgo(nota):
    if nota < 3.0:  return "ALTO"
    elif nota < 3.6: return "MEDIO"
    return "BAJO"


# ── Header PDF ────────────────────────────────────────────────────

def draw_header(canvas, doc):
    canvas.setFont("Helvetica", 8)
    canvas.drawString(
        40, 20,
        f"I.E.A. Akwe Uus Yat — Análisis Predictivo — {datetime.now().strftime('%d/%m/%Y')}"
    )


# ── Generar PDF ───────────────────────────────────────────────────

def generar_reporte_pdf(df, metricas, periodo, p1, p2, p3, output_path):
    styles = getSampleStyleSheet()
    normal = styles['Normal']

    doc = SimpleDocTemplate(
        output_path, pagesize=letter,
        topMargin=60, bottomMargin=40,
        leftMargin=30, rightMargin=30
    )
    content = []

    # Portada
    content.append(Paragraph('REPORTE ANALÍTICO ACADÉMICO PREDICTIVO', styles['Title']))
    content.append(Spacer(1, 12))
    content.append(Paragraph(
        f"<b>Periodo analizado:</b> {periodo} &nbsp;&nbsp; "
        f"<b>Fecha:</b> {datetime.now().strftime('%d/%m/%Y %H:%M')}",
        normal
    ))
    content.append(Spacer(1, 8))
    content.append(Paragraph(
        "Análisis integral del desempeño académico mediante analítica predictiva "
        "y aprendizaje automático (Random Forest), identificando patrones de "
        "rendimiento y riesgo académico.",
        normal
    ))
    content.append(PageBreak())

    # 1. Configuración
    content.append(Paragraph('1. Configuración del Análisis', styles['Heading1']))
    t_cfg = [
        ['Parámetro', 'Valor'],
        ['Periodo Analizado', f'Periodo {periodo}'],
        ['% Actividad 1', f'{p1*100:.1f}%'],
        ['% Actividad 2', f'{p2*100:.1f}%'],
        ['% Actividad 3', f'{p3*100:.1f}%'],
    ]
    t = Table(t_cfg, colWidths=[220, 150])
    t.setStyle(TableStyle([
        ('BACKGROUND', (0,0), (-1,0), colors.darkblue),
        ('TEXTCOLOR',  (0,0), (-1,0), colors.white),
        ('GRID',       (0,0), (-1,-1), 0.5, colors.black),
        ('ROWBACKGROUNDS', (0,1), (-1,-1), [colors.whitesmoke, colors.lightgrey]),
    ]))
    content.append(t)
    content.append(PageBreak())

    # 2. Resumen
    content.append(Paragraph('2. Resumen Ejecutivo', styles['Heading1']))
    t_met = [
        ['Indicador', 'Valor'],
        ['Total Estudiantes',  metricas['total_estudiantes']],
        ['Promedio General',   f"{metricas['promedio']:.2f}"],
        ['% Aprobación',       f"{metricas['aprobacion']:.2%}"],
        ['MAE Modelo (RF)',    f"{metricas['mae']:.3f}"],
        ['R² Modelo (RF)',     f"{metricas['r2']:.3f}"],
        ['En Riesgo Alto',     metricas['riesgo_alto']],
        ['En Riesgo Medio',    metricas['riesgo_medio']],
    ]
    t2 = Table(t_met, colWidths=[220, 150])
    t2.setStyle(TableStyle([
        ('BACKGROUND', (0,0), (-1,0), colors.darkgreen),
        ('TEXTCOLOR',  (0,0), (-1,0), colors.white),
        ('GRID',       (0,0), (-1,-1), 0.5, colors.black),
        ('ROWBACKGROUNDS', (0,1), (-1,-1), [colors.whitesmoke, colors.lightgrey]),
    ]))
    content.append(t2)
    content.append(PageBreak())

    # 3. Detalle
    content.append(Paragraph('3. Resultados Detallados por Estudiante', styles['Heading1']))
    rows = [['Estudiante', 'Materia', 'Nota Final', 'Predicción', 'Prob. Aprob.', 'Riesgo']]
    for _, row in df.iterrows():
        rows.append([
            Paragraph(str(row['Estudiante']), normal),
            Paragraph(str(row['Materia']),    normal),
            f"{row['Nota_Final']:.2f}",
            f"{row['Prediccion_Hibrida']:.2f}",
            f"{row['Probabilidad_Aprobacion']:.2%}",
            row['Riesgo'],
        ])
    t3 = Table(rows, repeatRows=1, colWidths=[130, 150, 55, 55, 65, 45])
    t3.setStyle(TableStyle([
        ('BACKGROUND', (0,0), (-1,0), colors.grey),
        ('TEXTCOLOR',  (0,0), (-1,0), colors.white),
        ('GRID',       (0,0), (-1,-1), 0.25, colors.black),
        ('FONTSIZE',   (0,0), (-1,-1), 7),
        ('VALIGN',     (0,0), (-1,-1), 'TOP'),
    ]))
    content.append(t3)

    # 4. Riesgo Alto
    content.append(PageBreak())
    content.append(Paragraph('4. Estudiantes en Riesgo Alto', styles['Heading1']))
    alto = df[df['Riesgo'] == 'ALTO']
    if not alto.empty:
        for _, row in alto.iterrows():
            content.append(Paragraph(
                f"<b>{row['Estudiante']}</b> | {row['Materia']} | "
                f"Nota: {row['Nota_Final']:.2f} | Pred.: {row['Prediccion_Hibrida']:.2f} | "
                f"Prob.: {row['Probabilidad_Aprobacion']:.2%}", normal
            ))
            content.append(Spacer(1, 4))
    else:
        content.append(Paragraph('No se identificaron estudiantes en Riesgo Alto.', normal))

    # 5. Riesgo Medio
    content.append(PageBreak())
    content.append(Paragraph('5. Estudiantes en Riesgo Medio', styles['Heading1']))
    medio = df[df['Riesgo'] == 'MEDIO']
    if not medio.empty:
        for _, row in medio.iterrows():
            content.append(Paragraph(
                f"<b>{row['Estudiante']}</b> | {row['Materia']} | "
                f"Nota: {row['Nota_Final']:.2f} | Pred.: {row['Prediccion_Hibrida']:.2f} | "
                f"Prob.: {row['Probabilidad_Aprobacion']:.2%}", normal
            ))
            content.append(Spacer(1, 4))
    else:
        content.append(Paragraph('No se identificaron estudiantes en Riesgo Medio.', normal))

    # 6. Conclusiones
    content.append(PageBreak())
    content.append(Paragraph('6. Conclusiones', styles['Heading1']))
    content.append(Paragraph(
        "El análisis evidencia el comportamiento académico de la cohorte evaluada, "
        "permitiendo identificar estudiantes en riesgo de reprobación para apoyar "
        "estrategias de alerta temprana y acompañamiento académico.",
        normal
    ))

    doc.build(content, onFirstPage=draw_header, onLaterPages=draw_header)


# ── Modelo principal ──────────────────────────────────────────────

def ejecutar(args):
    excel_path = args['excel_path']
    periodo    = args['periodo']
    p1         = float(args['p1'])
    p2         = float(args['p2'])
    p3         = float(args['p3'])
    output_dir = args['output_dir']

    validar_porcentajes(p1, p2, p3)
    os.makedirs(output_dir, exist_ok=True)

    # Leer Excel
    df = pd.read_excel(excel_path)
    df.columns = df.columns.str.strip()

    columnas_req = ['Codigo', 'Estudiante', 'Materia',
                    'Nota_Corte1', 'Nota_Corte2', 'Nota_Corte3']
    faltantes = [c for c in columnas_req if c not in df.columns]
    if faltantes:
        raise ValueError(f"Faltan columnas: {faltantes}")

    for col in ['Nota_Corte1', 'Nota_Corte2', 'Nota_Corte3']:
        df[col] = pd.to_numeric(df[col], errors='coerce')
    df = df.dropna()

    materias = df['Materia'].unique().tolist()

    # Nota final ponderada
    df['Nota_Final'] = (
        p1 * df['Nota_Corte1'] +
        p2 * df['Nota_Corte2'] +
        p3 * df['Nota_Corte3']
    )

    # Probabilidad de aprobación
    std_final = max(df['Nota_Final'].std(), 0.5)
    df['Probabilidad_Aprobacion'] = df['Nota_Final'].apply(
        lambda x: calcular_probabilidad(x, std_final)
    )

    # Random Forest
    X = df[['Nota_Corte1', 'Nota_Corte2', 'Nota_Corte3']]
    y = df['Nota_Final']

    X_train, X_test, y_train, y_test = train_test_split(
        X, y, test_size=0.2, random_state=42
    )

    modelo = RandomForestRegressor(n_estimators=300, max_depth=6, random_state=42)
    modelo.fit(X_train, y_train)

    mae = float(mean_absolute_error(y_test, modelo.predict(X_test)))
    r2  = float(r2_score(y_test,          modelo.predict(X_test)))

    # Predicción híbrida
    df['Prediccion_RF']      = modelo.predict(X)
    df['Prediccion_Hibrida'] = 0.5 * df['Prediccion_RF'] + 0.5 * df['Nota_Final']
    df['Riesgo']             = df['Prediccion_Hibrida'].apply(clasificar_riesgo)

    # Métricas
    metricas = {
        'total_estudiantes': int(len(df)),
        'promedio':          float(round(df['Nota_Final'].mean(), 2)),
        'aprobacion':        float(round((df['Nota_Final'] >= 3).mean(), 4)),
        'mae':               round(mae, 4),
        'r2':                round(r2,  4),
        'riesgo_alto':       int((df['Riesgo'] == 'ALTO').sum()),
        'riesgo_medio':      int((df['Riesgo'] == 'MEDIO').sum()),
        'riesgo_bajo':       int((df['Riesgo'] == 'BAJO').sum()),
        'materias':          materias,
    }

    # PDF
    pdf_path = os.path.join(output_dir, 'reporte_analitico.pdf')
    generar_reporte_pdf(df, metricas, periodo, p1, p2, p3, pdf_path)

    # Estudiantes para la tabla Blade
    estudiantes = []
    for _, row in df.iterrows():
        estudiantes.append({
            'codigo':       str(row['Codigo']),
            'estudiante':   str(row['Estudiante']),
            'materia':      str(row['Materia']),
            'corte1':       float(round(row['Nota_Corte1'],        2)),
            'corte2':       float(round(row['Nota_Corte2'],        2)),
            'corte3':       float(round(row['Nota_Corte3'],        2)),
            'nota_final':   float(round(row['Nota_Final'],         2)),
            'prediccion':   float(round(row['Prediccion_Hibrida'], 2)),
            'probabilidad': float(round(row['Probabilidad_Aprobacion'], 4)),
            'riesgo':       str(row['Riesgo']),
        })

    print(json.dumps({
        'status':      'ok',
        'metricas':    metricas,
        'estudiantes': estudiantes,
        'pdf_path':    pdf_path,
    }, ensure_ascii=False))


# ── Entry point ───────────────────────────────────────────────────

if __name__ == '__main__':
    try:
        args = json.loads(sys.argv[1])
        ejecutar(args)
    except Exception as e:
        print(json.dumps({'status': 'error', 'mensaje': str(e)}))
        sys.exit(1)