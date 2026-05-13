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
    SimpleDocTemplate,
    Paragraph,
    Spacer,
    Table,
    TableStyle,
    PageBreak
)

from reportlab.lib.styles import getSampleStyleSheet
from reportlab.lib import colors
from reportlab.lib.pagesizes import letter

from datetime import datetime


# ==========================================================
# CONFIGURACIÓN GENERAL
# ==========================================================
warnings.filterwarnings("ignore")
np.random.seed(42)

BASE_DIR = os.path.dirname(os.path.abspath(__file__))

OUTPUT_DIR = os.path.join(BASE_DIR, "resultados_modelo")

if not os.path.exists(OUTPUT_DIR):
    os.makedirs(OUTPUT_DIR)


# ==========================================================
# FUNCIONES AUXILIARES
# ==========================================================
def validar_porcentajes(p1, p2, p3):

    total = round(p1 + p2 + p3, 4)

    if total != 1.0:
        raise ValueError(
            f"Los porcentajes deben sumar 100%. "
            f"Suma actual: {total * 100:.2f}%"
        )


def calcular_probabilidad(nota, std, umbral=3.0):

    std = max(std, 0.5)

    return 1 - norm.cdf(
        umbral,
        loc=nota,
        scale=std
    )


def clasificar_riesgo(nota):

    if nota < 3.0:
        return "ALTO"

    elif nota < 3.6:
        return "MEDIO"

    return "BAJO"


# ==========================================================
# HEADER / FOOTER PDF
# ==========================================================
def draw_header(canvas, doc):

    logo_path = os.path.join(BASE_DIR, "Fup.jpeg")

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
        f"Fundación Universitaria de Popayán — "
        f"{datetime.now().strftime('%d/%m/%Y')}"
    )


# ==========================================================
# GENERAR PDF
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

    # ======================================================
    # PORTADA
    # ======================================================
    content.append(
        Paragraph(
            'REPORTE ANALÍTICO ACADÉMICO PREDICTIVO',
            styles['Title']
        )
    )

    content.append(Spacer(1, 20))

    intro = f"""
    <b>Materia Analizada:</b> {materia}<br/>
    Fecha de generación:
    {datetime.now().strftime('%d/%m/%Y %H:%M')}<br/><br/>

    Este reporte presenta un análisis integral del desempeño
    académico mediante analítica predictiva y aprendizaje automático,
    permitiendo identificar patrones de rendimiento y riesgo académico.
    """

    content.append(Paragraph(intro, normal))

    content.append(PageBreak())

    # ======================================================
    # CONFIGURACIÓN
    # ======================================================
    content.append(
        Paragraph(
            '1. Configuración del Análisis',
            styles['Heading1']
        )
    )

    tabla_config = [
        ['Parámetro', 'Valor'],
        ['Materia', materia],
        ['Corte Analizado', f'Corte {corte}'],
        [f'Actividad 1 - Corte {corte}', f'{p1 * 100:.1f}%'],
        [f'Actividad 2 - Corte {corte}', f'{p2 * 100:.1f}%'],
        [f'Actividad 3 - Corte {corte}', f'{p3 * 100:.1f}%']
    ]

    tabla = Table(
        tabla_config,
        colWidths=[220, 150]
    )

    tabla.setStyle(TableStyle([
        ('BACKGROUND', (0, 0), (-1, 0), colors.darkblue),
        ('TEXTCOLOR', (0, 0), (-1, 0), colors.white),
        ('GRID', (0, 0), (-1, -1), 0.5, colors.black),
        ('ROWBACKGROUNDS',
         (0, 1),
         (-1, -1),
         [colors.whitesmoke, colors.lightgrey])
    ]))

    content.append(tabla)

    content.append(PageBreak())

    # ======================================================
    # MÉTRICAS
    # ======================================================
    content.append(
        Paragraph(
            '2. Resumen Ejecutivo',
            styles['Heading1']
        )
    )

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

    tabla = Table(
        tabla_metricas,
        colWidths=[220, 150]
    )

    tabla.setStyle(TableStyle([
        ('BACKGROUND', (0, 0), (-1, 0), colors.darkgreen),
        ('TEXTCOLOR', (0, 0), (-1, 0), colors.white),
        ('GRID', (0, 0), (-1, -1), 0.5, colors.black),
        ('ROWBACKGROUNDS',
         (0, 1),
         (-1, -1),
         [colors.whitesmoke, colors.lightgrey])
    ]))

    content.append(tabla)

    content.append(PageBreak())

    # ======================================================
    # RESULTADOS
    # ======================================================
    content.append(
        Paragraph(
            '3. Resultados Detallados por Estudiante',
            styles['Heading1']
        )
    )

    rows = [[
        'Estudiante',
        'Materia',
        'Nota',
        'Pred.',
        'Prob.',
        'Riesgo'
    ]]

    for _, row in df.iterrows():

        rows.append([
            Paragraph(str(row['Estudiante']), normal),
            Paragraph(str(row['Materia']), normal),
            f"{row['Nota_Final']:.2f}",
            f"{row['Prediccion_Hibrida']:.2f}",
            f"{row['Probabilidad_Aprobacion']:.2%}",
            row['Riesgo']
        ])

    tabla_detalle = Table(
        rows,
        repeatRows=1,
        colWidths=[120, 170, 45, 45, 55, 45]
    )

    tabla_detalle.setStyle(TableStyle([
        ('BACKGROUND', (0, 0), (-1, 0), colors.grey),
        ('TEXTCOLOR', (0, 0), (-1, 0), colors.white),
        ('GRID', (0, 0), (-1, -1), 0.25, colors.black),
        ('FONTSIZE', (0, 0), (-1, -1), 7),
        ('VALIGN', (0, 0), (-1, -1), 'TOP')
    ]))

    content.append(tabla_detalle)

    # ======================================================
    # CONCLUSIONES
    # ======================================================
    content.append(PageBreak())

    content.append(
        Paragraph(
            '4. Conclusiones',
            styles['Heading1']
        )
    )

    conclusiones = """
    El análisis realizado evidencia el comportamiento académico general
    de la cohorte evaluada, permitiendo identificar estudiantes con
    potencial riesgo de reprobación y apoyar la toma de decisiones
    institucionales mediante analítica predictiva.
    """

    content.append(
        Paragraph(
            conclusiones,
            normal
        )
    )

    # ======================================================
    # GENERAR PDF
    # ======================================================
    doc.build(
        content,
        onFirstPage=draw_header,
        onLaterPages=draw_header
    )


# ==========================================================
# MODELO PRINCIPAL
# ==========================================================
def ejecutar_modelo_avanzado(
    materia,
    corte,
    p1,
    p2,
    p3,
    archivo_excel
):

    try:

        validar_porcentajes(p1, p2, p3)

        # ==================================================
        # LEER EXCEL
        # ==================================================
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

        faltantes = [
            c for c in columnas_req
            if c not in df.columns
        ]

        if faltantes:

            return {
                "success": False,
                "message": f"Faltan columnas: {faltantes}"
            }

        for col in [
            'Nota_Corte1',
            'Nota_Corte2',
            'Nota_Corte3'
        ]:

            df[col] = pd.to_numeric(
                df[col],
                errors='coerce'
            )

        df = df.dropna()

        # ==================================================
        # NOTA FINAL
        # ==================================================
        df['Nota_Final'] = (
            p1 * df['Nota_Corte1'] +
            p2 * df['Nota_Corte2'] +
            p3 * df['Nota_Corte3']
        )

        # ==================================================
        # PROBABILIDAD
        # ==================================================
        std_final = max(
            df['Nota_Final'].std(),
            0.5
        )

        df['Probabilidad_Aprobacion'] = (
            df['Nota_Final'].apply(
                lambda x: calcular_probabilidad(
                    x,
                    std_final
                )
            )
        )

        # ==================================================
        # RANDOM FOREST
        # ==================================================
        X = df[[
            'Nota_Corte1',
            'Nota_Corte2',
            'Nota_Corte3'
        ]]

        y = df['Nota_Final']

        X_train, X_test, y_train, y_test = train_test_split(
            X,
            y,
            test_size=0.2,
            random_state=42
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

        # ==================================================
        # PREDICCIÓN HÍBRIDA
        # ==================================================
        df['Prediccion_RF'] = modelo.predict(X)

        df['Prediccion_Hibrida'] = (
            0.5 * df['Prediccion_RF'] +
            0.5 * df['Nota_Final']
        )

        df['Riesgo'] = (
            df['Prediccion_Hibrida']
            .apply(clasificar_riesgo)
        )

        # ==================================================
        # GUARDAR CSV
        # ==================================================
        salida_csv = os.path.join(
            OUTPUT_DIR,
            "resultados_modelo.csv"
        )

        df.to_csv(
            salida_csv,
            index=False
        )

        # ==================================================
        # MÉTRICAS
        # ==================================================
        metricas = {
            'total_estudiantes': len(df),
            'promedio': df['Nota_Final'].mean(),
            'aprobacion': (
                df['Nota_Final'] >= 3
            ).mean(),
            'mae': mae,
            'r2': r2,
            'riesgo_alto': (
                df['Riesgo'] == 'ALTO'
            ).sum(),
            'riesgo_medio': (
                df['Riesgo'] == 'MEDIO'
            ).sum()
        }

        # ==================================================
        # PDF
        # ==================================================
        ruta_pdf = os.path.join(
            OUTPUT_DIR,
            "reporte_analitico.pdf"
        )

        generar_reporte_pdf(
            df=df,
            metricas=metricas,
            materia=materia,
            corte=corte,
            p1=p1,
            p2=p2,
            p3=p3,
            output_path=ruta_pdf
        )

        return {
            "success": True,
            "pdf": ruta_pdf,
            "csv": salida_csv
        }

    except Exception as e:

        return {
            "success": False,
            "message": str(e)
        }


# ==========================================================
# EJECUCIÓN DESDE LARAVEL
# ==========================================================
if __name__ == "__main__":

    try:

        materia = sys.argv[1]

        corte = int(sys.argv[2])

        p1 = float(sys.argv[3]) / 100
        p2 = float(sys.argv[4]) / 100
        p3 = float(sys.argv[5]) / 100

        archivo_excel = sys.argv[6]

        resultado = ejecutar_modelo_avanzado(
            materia,
            corte,
            p1,
            p2,
            p3,
            archivo_excel
        )

        if resultado["success"]:

            print("PDF generado correctamente")
            print(resultado["pdf"])

        else:

            print("ERROR")
            print(resultado["message"])

    except Exception as e:

        print(f"ERROR GENERAL: {str(e)}")
