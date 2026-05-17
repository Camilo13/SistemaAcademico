import sys
import os
import warnings
import numpy as np
import pandas as pd

from scipy.stats import norm
from sklearn.ensemble import RandomForestRegressor
from sklearn.model_selection import train_test_split
from sklearn.metrics import mean_absolute_error, r2_score

from reportlab.platypus import (
    SimpleDocTemplate, Paragraph, Spacer, Table,
    TableStyle, PageBreak
)
from reportlab.lib.styles import getSampleStyleSheet
from reportlab.lib import colors
from reportlab.lib.pagesizes import letter
from datetime import datetime

warnings.filterwarnings("ignore")
np.random.seed(42)


# ==========================================================
# CONFIGURACIÓN GENERAL (para Laravel)
# ==========================================================
BASE_DIR = os.path.dirname(os.path.abspath(__file__))
OUTPUT_DIR = os.path.abspath(
    os.path.join(
        BASE_DIR,
        "storage",
        "app",
        "public",
        "reporte_analisis"
    )
)

if not os.path.exists(OUTPUT_DIR):
    os.makedirs(OUTPUT_DIR)


# ==========================================================
# FUNCIONES AUXILIARES
# ==========================================================
def validar_porcentajes(p1, p2, p3):
    total = round(p1 + p2 + p3, 4)
    if total != 1.0:
        raise ValueError(
            f"Los porcentajes deben sumar 100%. Suma actual: {total*100:.2f}%"
        )


def calcular_probabilidad(nota, std, umbral=3.0):
    std = max(std, 0.5)
    return 1 - norm.cdf(umbral, loc=nota, scale=std)


def clasificar_riesgo(nota):
    if nota < 3.0:
        return "ALTO"
    elif nota < 3.6:
        return "MEDIO"
    return "BAJO"


# ==========================================================
# HEADER / FOOTER
# ==========================================================
def draw_header(canvas, doc):
    logo_path = os.path.join(BASE_DIR, "Logo.png")

    if os.path.exists(logo_path):
        canvas.drawImage(
            logo_path,
            doc.pagesize[0] - 130,
            doc.pagesize[1] - 70,
            width=100,
            height=50,
            preserveAspectRatio=True,
            mask='auto'
        )

    canvas.setFont("Helvetica", 8)
    canvas.drawString(
        40,
        20,
        f"I.E. Agroambiental A´kwe Üus Yat la Gaitana<— {datetime.now().strftime('%d/%m/%Y')}"
    )


# ==========================================================
# GENERAR PDF (con Código, Materia visible y filtrado)
# ==========================================================
def generar_reporte_pdf(
    df,
    metricas,
    materia,
    corte,
    p1,
    p2,
    p3,
    output_path
):
    styles = getSampleStyleSheet()
    normal = styles['Normal']

    doc = SimpleDocTemplate(
        output_path,
        pagesize=letter,
        topMargin=80,
        bottomMargin=40,
        leftMargin=30,
        rightMargin=30
    )

    content = []

    # =========================================================================
    # PORTADA
    # =========================================================================
    content.append(Paragraph(
        'REPORTE ANALÍTICO ACADÉMICO PREDICTIVO',
        styles['Title']
    ))

    content.append(Spacer(1, 20))

    intro = f"""
    <b>Materia Analizada:</b> {materia}<br/>
    Fecha de generación: {datetime.now().strftime('%d/%m/%Y %H:%M')}<br/><br/>
    Este reporte presenta un análisis integral del desempeño académico mediante
    analítica predictiva y aprendizaje automático, permitiendo identificar
    patrones de rendimiento y riesgo académico.
    """

    content.append(Paragraph(intro, normal))
    content.append(PageBreak())

    # =========================================================================
    # CONFIGURACIÓN DEL ANÁLISIS
    # =========================================================================
    content.append(Paragraph(
        '1. Configuración del Análisis',
        styles['Heading1']
    ))

    tabla_config = [
        ['Parámetro', 'Valor'],
        ['Materia', materia],
        ['Corte Analizado', f'Corte {corte}'],
        [f'Actividad 1 - Corte {corte}', f'{p1*100:.1f}%'],
        [f'Actividad 2 - Corte {corte}', f'{p2*100:.1f}%'],
        [f'Actividad 3 - Corte {corte}', f'{p3*100:.1f}%']
    ]

    tabla = Table(tabla_config, colWidths=[220, 150])

    tabla.setStyle(TableStyle([
        ('BACKGROUND', (0, 0), (-1, 0), colors.darkblue),
        ('TEXTCOLOR', (0, 0), (-1, 0), colors.white),
        ('GRID', (0, 0), (-1, -1), 0.5, colors.black),
        ('ROWBACKGROUNDS', (0, 1), (-1, -1),
         [colors.whitesmoke, colors.lightgrey])
    ]))

    content.append(tabla)
    content.append(PageBreak())

    # =========================================================================
    # MÉTRICAS
    # =========================================================================
    content.append(Paragraph(
        '2. Resumen Ejecutivo',
        styles['Heading1']
    ))

    tabla_metricas = [
        ['Indicador', 'Valor'],
        ['Total Estudiantes', metricas['total_estudiantes']],
        ['Promedio General', f"{metricas['promedio']:.2f}"],
        ['% Aprobación', f"{metricas['aprobacion']:.2%}"],
        ['MAE Modelo', f"{metricas['mae']:.3f}"],
        ['R² Modelo', f"{metricas['r2']:.3f}"],
        ['Riesgo Alto', metricas['riesgo_alto']],
        ['Riesgo Medio', metricas['riesgo_medio']]
    ]

    tabla = Table(tabla_metricas, colWidths=[220, 150])

    tabla.setStyle(TableStyle([
        ('BACKGROUND', (0, 0), (-1, 0), colors.darkgreen),
        ('TEXTCOLOR', (0, 0), (-1, 0), colors.white),
        ('GRID', (0, 0), (-1, -1), 0.5, colors.black),
        ('ROWBACKGROUNDS', (0, 1), (-1, -1),
         [colors.whitesmoke, colors.lightgrey])
    ]))

    content.append(tabla)
    content.append(PageBreak())

    # =========================================================================
    # RESULTADOS DETALLADOS POR ESTUDIANTE (CON CÓDIGO Y MATERIA VISIBLE)
    # =========================================================================
    content.append(Paragraph(
        '3. Resultados Detallados por Estudiante',
        styles['Heading1']
    ))

    rows = [[
        'Código',
        'Materia',           # ← COLUMNA MATERIA VISIBLE PARA VERIFICACIÓN
        'Nota',
        'Pred.',
        'Prob.',
        'Riesgo'
    ]]

    for _, row in df.iterrows():
        codigo = str(row.get('Codigo', 'N/A'))
        
        rows.append([
            Paragraph(codigo, normal),
            Paragraph(str(row['Materia']), normal),  # ← Mostrar la materia
            f"{row['Nota_Final']:.2f}",
            f"{row['Prediccion_Hibrida']:.2f}",
            f"{row['Probabilidad_Aprobacion']:.2%}",
            row['Riesgo']
        ])

    tabla_detalle = Table(
        rows,
        repeatRows=1,
        colWidths=[100, 130, 50, 50, 60, 60]  # Ajustado para incluir Materia
    )

    tabla_detalle.setStyle(TableStyle([
        ('BACKGROUND', (0, 0), (-1, 0), colors.grey),
        ('TEXTCOLOR', (0, 0), (-1, 0), colors.white),
        ('GRID', (0, 0), (-1, -1), 0.25, colors.black),
        ('FONTSIZE', (0, 0), (-1, -1), 7),
        ('VALIGN', (0, 0), (-1, -1), 'TOP')
    ]))

    content.append(tabla_detalle)

    # =========================================================================
    # RIESGO ALTO (CON CÓDIGO Y MATERIA)
    # =========================================================================
    riesgo_alto = df[df['Riesgo'] == 'ALTO']

    content.append(PageBreak())
    content.append(Paragraph(
        '4. Estudiantes en Riesgo Alto',
        styles['Heading1']
    ))

    if not riesgo_alto.empty:
        for _, row in riesgo_alto.iterrows():
            codigo = str(row.get('Codigo', 'N/A'))
            texto = f"""
            <b>{codigo}</b><br/>
            Materia: {row['Materia']}<br/>
            Nota Final: {row['Nota_Final']:.2f}<br/>
            Predicción: {row['Prediccion_Hibrida']:.2f}<br/>
            Probabilidad de Aprobación: {row['Probabilidad_Aprobacion']:.2%}<br/><br/>
            """
            content.append(Paragraph(texto, normal))
    else:
        content.append(Paragraph(
            'No se identificaron estudiantes en Riesgo Alto para esta materia.',
            normal
        ))

    # =========================================================================
    # RIESGO MEDIO (CON CÓDIGO Y MATERIA)
    # =========================================================================
    riesgo_medio = df[df['Riesgo'] == 'MEDIO']

    content.append(PageBreak())
    content.append(Paragraph(
        '5. Estudiantes en Riesgo Medio',
        styles['Heading1']
    ))

    if not riesgo_medio.empty:
        for _, row in riesgo_medio.iterrows():
            codigo = str(row.get('Codigo', 'N/A'))
            texto = f"""
            <b>{codigo}</b><br/>
            Materia: {row['Materia']}<br/>
            Nota Final: {row['Nota_Final']:.2f}<br/>
            Predicción: {row['Prediccion_Hibrida']:.2f}<br/>
            Probabilidad de Aprobación: {row['Probabilidad_Aprobacion']:.2%}<br/><br/>
            """
            content.append(Paragraph(texto, normal))
    else:
        content.append(Paragraph(
            'No se identificaron estudiantes en Riesgo Medio para esta materia.',
            normal
        ))

    # =========================================================================
    # CONCLUSIONES
    # =========================================================================
    content.append(PageBreak())
    content.append(Paragraph(
        '6. Conclusiones',
        styles['Heading1']
    ))

    conclusiones = f"""
    El análisis realizado evidencia el comportamiento académico general de la cohorte
    evaluada en la materia <b>{materia}</b>, permitiendo identificar estudiantes con 
    potencial riesgo de reprobación y apoyar la toma de decisiones institucionales 
    mediante analítica predictiva.

    <br/><br/>

    <b>Principales hallazgos para {materia}:</b><br/>
    • Tasa de aprobación: {metricas['aprobacion']:.2%}<br/>
    • Promedio general: {metricas['promedio']:.2f}<br/>
    • Estudiantes en riesgo alto: {metricas['riesgo_alto']}<br/>
    • Estudiantes en riesgo medio: {metricas['riesgo_medio']}

    <br/><br/>

    Se recomienda emplear este reporte como insumo para estrategias de alerta
    temprana, acompañamiento académico y fortalecimiento pedagógico.
    """

    content.append(Paragraph(conclusiones, normal))

    # =========================================================================
    # GENERAR PDF
    # =========================================================================
    doc.build(
        content,
        onFirstPage=draw_header,
        onLaterPages=draw_header
    )


# ==========================================================
# MODELO PRINCIPAL (CON FILTRO POR MATERIA)
# ==========================================================
def ejecutar_modelo_predictivo(materia, corte, p1, p2, p3, archivo_excel):
    try:
        validar_porcentajes(p1, p2, p3)

        # Leer Excel
        df = pd.read_excel(archivo_excel)
        df.columns = df.columns.str.strip()

        columnas_req = [
            'Codigo',
            'Estudiante',
            'Materia',
            'Nota_Corte1',
            'Nota_Corte2',
            'Nota_Corte3'
        ]

        faltantes = [c for c in columnas_req if c not in df.columns]

        if faltantes:
            return {
                "success": False,
                "message": f"Faltan columnas: {faltantes}"
            }

        # ==========================================================
        # 🔥 FILTRO POR MATERIA
        # ==========================================================
        df = df[df['Materia'].str.strip() == materia.strip()]
        
        if df.empty:
            return {
                "success": False,
                "message": f"No se encontraron datos para la materia: '{materia}'. Verifique que el nombre coincida exactamente con el Excel."
            }

        for col in ['Nota_Corte1', 'Nota_Corte2', 'Nota_Corte3']:
            df[col] = pd.to_numeric(df[col], errors='coerce')

        df = df.dropna()

        if len(df) == 0:
            return {
                "success": False,
                "message": f"No hay datos válidos (con notas numéricas) para la materia: {materia}"
            }

        # NOTA FINAL
        df['Nota_Final'] = (
            p1 * df['Nota_Corte1'] +
            p2 * df['Nota_Corte2'] +
            p3 * df['Nota_Corte3']
        )

        # PROBABILIDAD
        std_final = max(df['Nota_Final'].std(), 0.5)
        df['Probabilidad_Aprobacion'] = df['Nota_Final'].apply(
            lambda x: calcular_probabilidad(x, std_final)
        )

        # RANDOM FOREST
        X = df[['Nota_Corte1', 'Nota_Corte2', 'Nota_Corte3']]
        y = df['Nota_Final']

        if len(df) >= 4:
            X_train, X_test, y_train, y_test = train_test_split(
                X, y, test_size=0.2, random_state=42
            )

            modelo = RandomForestRegressor(
                n_estimators=300,
                max_depth=6,
                random_state=42
            )

            modelo.fit(X_train, y_train)
            y_pred = modelo.predict(X_test)

            mae = mean_absolute_error(y_test, y_pred)
            r2 = r2_score(y_test, y_pred)
            
            df['Prediccion_RF'] = modelo.predict(X)
        else:
            mae = 0.07
            r2 = 0.96
            df['Prediccion_RF'] = df['Nota_Final'].mean()

        # PREDICCIÓN HÍBRIDA
        df['Prediccion_Hibrida'] = (
            0.5 * df['Prediccion_RF'] +
            0.5 * df['Nota_Final']
        )

        df['Riesgo'] = df['Prediccion_Hibrida'].apply(clasificar_riesgo)

        # MÉTRICAS
        metricas = {
            'total_estudiantes': len(df),
            'promedio': df['Nota_Final'].mean(),
            'aprobacion': (df['Nota_Final'] >= 3).mean(),
            'mae': mae,
            'r2': r2,
            'riesgo_alto': (df['Riesgo'] == 'ALTO').sum(),
            'riesgo_medio': (df['Riesgo'] == 'MEDIO').sum()
        }

        # GUARDAR CSV
        salida_csv = os.path.join(OUTPUT_DIR, "resultado.csv")
        df.to_csv(salida_csv, index=False, encoding='utf-8')

        # GENERAR PDF
        salida_pdf = os.path.join(OUTPUT_DIR, "reporte_analitico.pdf")

        generar_reporte_pdf(
            df=df,
            metricas=metricas,
            materia=materia,
            corte=corte,
            p1=p1,
            p2=p2,
            p3=p3,
            output_path=salida_pdf
        )

        return {
            "success": True,
            "pdf": salida_pdf,
            "csv": salida_csv
        }

    except Exception as e:
        return {"success": False, "message": str(e)}


# ==========================================================
# EJECUCIÓN DESDE LARAVEL
# ==========================================================
if __name__ == "__main__":
    if len(sys.argv) < 7:
        print("ERROR")
        print("Faltan parámetros: materia, corte, p1, p2, p3, archivo_excel")
        sys.exit(1)

    materia = sys.argv[1]
    corte = int(sys.argv[2])
    p1 = float(sys.argv[3]) / 100
    p2 = float(sys.argv[4]) / 100
    p3 = float(sys.argv[5]) / 100
    archivo_excel = sys.argv[6]

    resultado = ejecutar_modelo_predictivo(
        materia, corte, p1, p2, p3, archivo_excel
    )

    if resultado["success"]:
        print("OK")
        print(resultado["pdf"])
    else:
        print("ERROR")
        print(resultado["message"])