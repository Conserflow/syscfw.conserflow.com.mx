<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PRH-01/F-06 - EVALUCIÓN DE SATISFACCIÓN DE CAPACITACIÓN</title>
</head>

<style type="text/css">
    @page {
        margin-top: 3cm;
        margin-left: 1cm;
        margin-right: 1cm;
        margin-bottom: 2cm;
    }

    header {
        position: fixed;
        top: -90px;
        left: 0px;
        right: 0px;
    }

    footer {
        position: fixed;
        bottom: -40px;
        left: 0px;
        right: 0px;
        height: 30px;
        font-family: Arial, Helvetica, sans-serif;
        font-size: 14px;
    }

    table {
        border-collapse: collapse;
        font-family: Arial, Helvetica, sans-serif;
        font-size: 11px;
        text-align: center;
    }

    .border-all tr td {
        border: 1px solid black;
    }

    .text-blue {
        color: #0070c0;
    }

    .text-white {
        color: white;
    }

    .h-2 {
        padding-top: .5rem;
        padding-bottom: .5rem;
    }

    .h-4 {
        padding-top: 1rem;
        padding-bottom: 1rem;
    }

    .h-1 {
        padding-top: .3rem;
        padding-bottom: .3rem;
    }

    .text-bold {
        font-weight: bold;
    }

    .text-left {
        text-align: left;
    }

    .text-center {
        text-align: center;
    }

    .text-end {
        text-align: right;
    }

    .text-sm {
        font-size: 8;
    }

    .bg-blue {
        background-color: #0070c0;
        color: white;
        font-weight: bold;
    }

    .bg-gray {
        color: black;
        background-color: #BFBFBF;
    }

    body {
        font-family: Arial, Helvetica, sans-serif;
    }

    table tr .border-bottom-none {
        border-bottom: none
    }

    table tr .border-top-none {
        border-top: none;
    }
</style>

<body>
    <header>

        <table width="100%" class="table border-all">
            <tr>
                <td colspan="4">
                    <div class="text-blue text-bold">CONSERFLOW S.A. DE C.V.</b></div>
                </td>
            </tr>
            <tr>
                <td rowspan="3" width="20%"><img src="img/conserflow.png" width="120"></td>
                <td rowspan="3"><b>EVALUCIÓN DE SATISFACCIÓN DE CAPACITACIÓN</b> </td>
                <td width="15%" class="text-bold"> CÓDIGO</td>
                <td width="15%">PRH-01/F-06</td>
            </tr>
            <tr>
                <td class="text-bold">REVISIÓN</td>
                <td>00</td>
            </tr>
            <tr>
                <td class="text-bold">EMISIÓN</td>
                <td>01.ABR.20</td>
            </tr>

        </table>
    </header>

    <footer>
        <div>
            <p style="color: #0070c0;font-weight: bold;font-size: 10;">
                CONSERFLOW S.A. DE C.V.
            </p>
        </div>
    </footer>

    <div>
        <table class="border-all" width="100%">
            <tr>
                <td colspan="10" class="bg-blue">DATOS DE EVALUACIÓN</td>
            </tr>
            <tr>
                <td class="bg-gray">Nombre del curso</td>
                <td colspan="9">{{$curso->nombre}}</td>
            </tr>
            <tr>
                <td class="bg-gray">instructor</td>
                <td colspan="9">{{$curso->instructor}}</td>
            </tr>
            <tr>
                <td class="bg-gray">Evaluaador</td>
                <td colspan="9">{{$curso->nombre}}</td>
            </tr>
            <tr>
                <td class="bg-gray">Fecha</td>
                <td colspan="2">{{$curso->fecha}}</td>
                <td class="bg-gray">Puntaje</td>
                <td colspan="2"></td>
                <td class="bg-gray">calificación</td>
                <td colspan="3"></td>
            </tr>
            <tr>
                <td colspan="10" class="border-bottom-none"></td>
            </tr>
            <tr>
                <td style="border: 1px solid white;" width="25%"></td>
                <td style="border: 1px solid white;" class="text-end">1</td>
                <td style="border: 1px solid white;" class="border-bottom-none border-top-none text-center">Insatisfecho</td>
                <td style="border: 1px solid white;" width="10%" class="text-end">2</td>
                <td style="border: 1px solid white;" class="text-center">Satisfecho</td>
                <td style="border: 1px solid white;" class="text-end">3</td>
                <td style="border: 1px solid white;" width="15%" class=" text-center">Muy satisfecho</td>
                <td style="border: 1px solid white;" class="" colspan="3"></td>
            </tr>
            <tr>
                <td style="border-left: 1px solid white;border-right: 1px solid white;" colspan="10">&nbsp;</td>
            </tr>
            <tr>
                <td colspan="7" class="bg-blue">CONCEPTOS A EVALUAR</td>
                <td class="bg-blue">1</td>
                <td class="bg-blue">2</td>
                <td class="bg-blue">3</td>
            </tr>
            <tr>
                <td colspan="10" class="bg-gray text-left" style="font-weight: bold;">CONTENIDO DEL CURSO</td>
            </tr>

            <tr>
                <td colspan="7" class="text-left">
                    De manera general tu nivel de satisfacción con respecto al contenido temático es
                </td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td colspan="7" class="text-left">
                    Las dinámicas y ejercicios práctivos realizados durante el evento, que nivel de satisfacción te dejan
                </td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td colspan="7" class="text-left">
                    ¿Consideras que los ejercicios prácticos, logran aumeontar la compresión de los temas expuestos?
                </td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td colspan="7" class="text-left">
                    Los conocimientos adquieridos en qué mivel consideras mejorarán tu desempeño laboral
                </td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td colspan="7" class="text-left">
                    Los conocimientos adquiridos en qué nivel son útiles para tus funciones laborales
                </td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td colspan="10" class="bg-gray text-left" style="font-weight: bold;">HABILIDADES DEL INSTRUCTOR</td>
            </tr>

            <tr>
                <td colspan="7" class="text-left">
                    En que escala cosideras que el instructor fue claro, y logró transmitir los conocimientos
                </td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td colspan="7" class="text-left">
                    Consideras que el dominio del tema por parte del instructor fue
                </td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td colspan="7" class="text-left">
                    El tono y el nivel de voz utilizado por el instructor consideras que fue
                </td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td colspan="7" class="text-left">
                    Durante el evento el instructor dio respuesta y resolvió dudas
                </td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td colspan="7" class="text-left">
                    ¿El instructor se mostró respetuoso al dirigirse con los participantes?
                </td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td colspan="10">
                </td>
            </tr>
            <tr>
                <td colspan="10" class="bg-blue">OBSERVACIONES O COMENTARIOS</td>
            </tr>
            <tr>
                <td colspan="10">
                    <br>
                    <br>
                    <br>
                    <br>
                </td>
            </tr>
        </table>
    </div>

    <script type="text/php">
        if (isset($pdf)) {
        $text = "PAGINA {PAGE_NUM} DE {PAGE_COUNT}";
        $size = 9;
        $color = #0070c0;
        $font = $fontMetrics->getFont("Verdana");
        $width = $fontMetrics->get_text_width($text, $font, $size) / 2;
        $x = ($pdf->get_width() - $width) / 1;
        $y = $pdf->get_height() - 35;
        $pdf->page_text($x, $y, $text, $font, $size,$color);
    }
</script>
</body>

</html>