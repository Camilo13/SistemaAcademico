"""
modelo_riesgo.py
Uso desde Laravel:
  python3 modelo_riesgo.py <excel_path> <corte> <p1> <p2> <p3> <output_dir>

Devuelve JSON por stdout para que Laravel lo lea.
El PDF se guarda en <output_dir>/reporte_analitico.pdf
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


# ==============================================================
# FUNCIONES AUXILIARES  (idénticas al modelo original del profe)
# ==============================================================

def validar_porcentajes(p1, p2, p3):
    total = round(p1 + p2 + p3, 4)
    if total != 1.0:
        raise ValueError(
            f"Los porcentajes deben sumar 100%. Suma actual: {total*100:.2f}%"
        )


def calcular_probabilidad(nota, std, umbral=3.0):
    std = max(std, 0.5)
    return float(1 - norm.cdf(umbral, loc=nota, scale=std))


def clasificar_riesgo(nota):
    if nota < 3.0:
        return "ALTO"
    elif nota < 3.6:
        return "MEDIO"
    return "BAJO"


# ==============================================================
# HEADER PDF
# ==============================================================

def draw_header(canvas, doc):
    canvas.setFont("Helvetica", 8)
    canvas.drawString(
        40, 20,
        f"I.E.A. Akwe Uus Yat — Análisis Predictivo — {datetime.now().strftime('%d/%m/%Y')}"
    )


# ==============================================================
# GENERAR PDF  (igual al original, ajustado para Laravel)
# ==============================================================

def generar_reporte_pdf(df, metricas, materia, corte, p1, p2, p3, output_path):

    styles  = getSampleStyleSheet()
    normal  = styles['Normal']

    doc = SimpleDocTemplate(
        output_path,
        pagesize=letter,
        topMargin=60,
        bottomMargin=40,
        leftMargin=30,
        rightMargin=30
    )

    content = []

    # -- Portada -------------------------------------------------------
    content.append(Paragraph('REPORTE ANALÍTICO ACADÉMICO PREDICTIVO', styles['Title']))
    content.append(Spacer(1, 16))
    content.append(Paragraph(
        f"<b>Materia:</b> {materia} &nbsp;&nbsp; "
        f"<b>Corte analizado:</b> {corte} &nbsp;&nbsp; "
        f"<b>Fecha:</b> {datetime.now().strftime('%d/%m/%Y %H:%M')}",
        normal
    ))
    content.append(Spacer(1, 8))
    content.append(Paragraph(
        "Este reporte presenta un análisis integral del desempeño académico "
        "mediante analítica predictiva y aprendizaje automático (Random Forest), "
        "permitiendo identificar patrones de rendimiento y riesgo académico de forma "
        "desacoplada e independiente de conectividad externa.",
        normal
    ))
    content.append(PageBreak())

    # -- Configuración -------------------------------------------------
    content.append(Paragraph('1. Configuración del Análisis', styles['Heading1']))
    t_config = [
        ['Parámetro',           'Valor'],
        ['Materia',              materia],
        ['Corte Analizado',      f'Corte {corte}'],
        [f'Actividad 1 - Corte {corte}', f'{p1*100:.1f}%'],
        [f'Actividad 2 - Corte {corte}', f'{p2*100:.1f}%'],
        [f'Actividad 3 - Corte {corte}', f'{p3*100:.1f}%'],
    ]
    t = Table(t_config, colWidths=[220, 150])
    t.setStyle(TableStyle([
        ('BACKGROUND',    (0,0), (-1,0), colors.darkblue),
        ('TEXTCOLOR',     (0,0), (-1,0), colors.white),
        ('GRID',          (0,0), (-1,-1), 0.5, colors.black),
        ('ROWBACKGROUNDS',(0,1), (-1,-1), [colors.whitesmoke, colors.lightgrey]),
    ]))
    content.append(t)
    content.append(PageBreak())

    # -- Resumen ejecutivo ---------------------------------------------
    content.append(Paragraph('2. Resumen Ejecutivo', styles['Heading1']))
    t_met = [
        ['Indicador',         'Valor'],
        ['Total Estudiantes',  metricas['total_estudiantes']],
        ['Promedio General',   f"{metricas['promedio']:.2f}"],
        ['% Aprobación',       f"{metricas['aprobacion']:.2%}"],
        ['MAE Modelo (RF)',     f"{metricas['mae']:.3f}"],
        ['R² Modelo (RF)',      f"{metricas['r2']:.3f}"],
        ['En Riesgo Alto',     metricas['riesgo_alto']],
        ['En Riesgo Medio',    metricas['riesgo_medio']],
    ]
    t2 = Table(t_met, colWidths=[220, 150])
    t2.setStyle(TableStyle([
        ('BACKGROUND',    (0,0), (-1,0), colors.darkgreen),
        ('TEXTCOLOR',     (0,0), (-1,0), colors.white),
        ('GRID',          (0,0), (-1,-1), 0.5, colors.black),
        ('ROWBACKGROUNDS',(0,1), (-1,-1), [colors.whitesmoke, colors.lightgrey]),
    ]))
    content.append(t2)
    content.append(PageBreak())

    # -- Resultados detallados -----------------------------------------
    content.append(Paragraph('3. Resultados Detallados por Estudiante', styles['Heading1']))
    rows = [['Estudiante', 'Materia', 'Nota Final', 'Predicción', 'Prob. Aprobación', 'Riesgo']]
    for _, row in df.iterrows():
        rows.append([
            Paragraph(str(row['Estudiante']), normal),
            Paragraph(str(row['Materia']),    normal),
            f"{row['Nota_Final']:.2f}",
            f"{row['Prediccion_Hibrida']:.2f}",
            f"{row['Probabilidad_Aprobacion']:.2%}",
            row['Riesgo'],
        ])
    t3 = Table(rows, repeatRows=1, colWidths=[130, 150, 55, 55, 80, 45])
    t3.setStyle(TableStyle([
        ('BACKGROUND', (0,0), (-1,0), colors.grey),
        ('TEXTCOLOR',  (0,0), (-1,0), colors.white),
        ('GRID',       (0,0), (-1,-1), 0.25, colors.black),
        ('FONTSIZE',   (0,0), (-1,-1), 7),
        ('VALIGN',     (0,0), (-1,-1), 'TOP'),
    ]))
    content.append(t3)

    # -- Riesgo Alto ---------------------------------------------------
    content.append(PageBreak())
    content.append(Paragraph('4. Estudiantes en Riesgo Alto', styles['Heading1']))
    alto = df[df['Riesgo'] == 'ALTO']
    if not alto.empty:
        for _, row in alto.iterrows():
            content.append(Paragraph(
                f"<b>{row['Estudiante']}</b> | {row['Materia']} | "
                f"Nota: {row['Nota_Final']:.2f} | Pred.: {row['Prediccion_Hibrida']:.2f} | "
                f"Prob. aprobación: {row['Probabilidad_Aprobacion']:.2%}",
                normal
            ))
            content.append(Spacer(1, 4))
    else:
        content.append(Paragraph('No se identificaron estudiantes en Riesgo Alto.', normal))

    # -- Riesgo Medio --------------------------------------------------
    content.append(PageBreak())
    content.append(Paragraph('5. Estudiantes en Riesgo Medio', styles['Heading1']))
    medio = df[df['Riesgo'] == 'MEDIO']
    if not medio.empty:
        for _, row in medio.iterrows():
            content.append(Paragraph(
                f"<b>{row['Estudiante']}</b> | {row['Materia']} | "
                f"Nota: {row['Nota_Final']:.2f} | Pred.: {row['Prediccion_Hibrida']:.2f} | "
                f"Prob. aprobación: {row['Probabilidad_Aprobacion']:.2%}",
                normal
            ))
            content.append(Spacer(1, 4))
    else:
        content.append(Paragraph('No se identificaron estudiantes en Riesgo Medio.', normal))

    # -- Conclusiones --------------------------------------------------
    content.append(PageBreak())
    content.append(Paragraph('6. Conclusiones', styles['Heading1']))
    content.append(Paragraph(
        "El análisis realizado evidencia el comportamiento académico general de la cohorte "
        "evaluada, permitiendo identificar estudiantes con potencial riesgo de reprobación "
        "y apoyar la toma de decisiones institucionales mediante analítica predictiva. "
        "Se recomienda emplear este reporte como insumo para estrategias de alerta "
        "temprana, acompañamiento académico y fortalecimiento pedagógico.",
        normal
    ))

    doc.build(content, onFirstPage=draw_header, onLaterPages=draw_header)


# ==============================================================
# MODELO PRINCIPAL  — ahora recibe args por CLI, no por input()
# ==============================================================

def ejecutar(excel_path, corte, p1, p2, p3, output_dir):

    os.makedirs(output_dir, exist_ok=True)

    # -- Leer Excel ----------------------------------------------------
    df = pd.read_excel(excel_path)
    df.columns = df.columns.str.strip()

    columnas_req = ['Codigo', 'Estudiante', 'Materia',
                    'Nota_Corte1', 'Nota_Corte2', 'Nota_Corte3']
    faltantes = [c for c in columnas_req if c not in df.columns]
    if faltantes:
        raise ValueError(f"Faltan columnas en el Excel: {faltantes}")

    for col in ['Nota_Corte1', 'Nota_Corte2', 'Nota_Corte3']:
        df[col] = pd.to_numeric(df[col], errors='coerce')
    df = df.dropna()

    # Filtrar solo la materia del corte si viene en la columna
    materias = df['Materia'].unique().tolist()

    # -- Nota final ponderada ------------------------------------------
    df['Nota_Final'] = (
        p1 * df['Nota_Corte1'] +
        p2 * df['Nota_Corte2'] +
        p3 * df['Nota_Corte3']
    )

    # -- Probabilidad de aprobación ------------------------------------
    std_final = max(df['Nota_Final'].std(), 0.5)
    df['Probabilidad_Aprobacion'] = df['Nota_Final'].apply(
        lambda x: calcular_probabilidad(x, std_final)
    )

    # -- Random Forest -------------------------------------------------
    X = df[['Nota_Corte1', 'Nota_Corte2', 'Nota_Corte3']]
    y = df['Nota_Final']

    X_train, X_test, y_train, y_test = train_test_split(
        X, y, test_size=0.2, random_state=42
    )

    modelo = RandomForestRegressor(n_estimators=300, max_depth=6, random_state=42)
    modelo.fit(X_train, y_train)

    mae = float(mean_absolute_error(y_test, modelo.predict(X_test)))
    r2  = float(r2_score(y_test, modelo.predict(X_test)))

    # -- Predicción híbrida --------------------------------------------
    df['Prediccion_RF']      = modelo.predict(X)
    df['Prediccion_Hibrida'] = 0.5 * df['Prediccion_RF'] + 0.5 * df['Nota_Final']
    df['Riesgo']             = df['Prediccion_Hibrida'].apply(clasificar_riesgo)

    # -- Métricas ------------------------------------------------------
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

    # -- PDF -----------------------------------------------------------
    pdf_path = os.path.join(output_dir, 'reporte_analitico.pdf')
    generar_reporte_pdf(
        df       = df,
        metricas = metricas,
        materia  = ', '.join(materias),
        corte    = corte,
        p1=p1, p2=p2, p3=p3,
        output_path = pdf_path,
    )

    # -- Resultados por estudiante (para la tabla en Blade) ------------
    estudiantes = []
    for _, row in df.iterrows():
        estudiantes.append({
            'codigo':      str(row['Codigo']),
            'estudiante':  str(row['Estudiante']),
            'materia':     str(row['Materia']),
            'corte1':      float(round(row['Nota_Corte1'], 2)),
            'corte2':      float(round(row['Nota_Corte2'], 2)),
            'corte3':      float(round(row['Nota_Corte3'], 2)),
            'nota_final':  float(round(row['Nota_Final'], 2)),
            'prediccion':  float(round(row['Prediccion_Hibrida'], 2)),
            'probabilidad':float(round(row['Probabilidad_Aprobacion'], 4)),
            'riesgo':      str(row['Riesgo']),
        })

    # -- Salida JSON ---------------------------------------------------
    resultado = {
        'status':      'ok',
        'metricas':    metricas,
        'estudiantes': estudiantes,
        'pdf_path':    pdf_path,
    }

    print(json.dumps(resultado, ensure_ascii=False))


# ==============================================================
# ENTRY POINT
# ==============================================================

if __name__ == '__main__':
    try:
        _, excel_path, corte, p1, p2, p3, output_dir = sys.argv
        ejecutar(
            excel_path = excel_path,
            corte      = int(corte),
            p1         = float(p1),
            p2         = float(p2),
            p3         = float(p3),
            output_dir = output_dir,
        )
    except Exception as e:
        print(json.dumps({'status': 'error', 'mensaje': str(e)}))
        sys.exit(1)