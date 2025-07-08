<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PRH-01/F-02 - EVALUACIÓN DE COMPETENCIAS</title>
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
        font-size: 12px;
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

    .text-normal {
        font-weight: normal !important;
    }

    .text-left {
        text-align: left;
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

    .bg-yellow {
        background-color: #ffc000;
        color: black;
        font-weight: bold;
        padding-top: 0.2rem;
    }

    .bg-gray {
        color: black;
        font-weight: bold;
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

    table tr .border-left-none {
        border-left: none;
    }

    table tr .border-right-none {
        border-right: none;
    }

    .opciones {
        margin-top: 0.5rem;
        margin-bottom: 0.5rem;
        text-align: left;
        margin-left: 3rem;
    }

    .opciones p {
        font-size: 10px;
        margin-bottom: 0px;
        margin-top: 0px;
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
                <td rowspan="3"><b>EVALUACIÓN DE COMPETENCIAS</b> </td>
                <td width="15%" class="text-bold"> CÓDIGO</td>
                <td width="15%">PRH-01/F-02</td>
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
            <p style="color: #0070c0;font-weight: bold;font-size: 10;">CONSERFLOW S.A. DE C.V.</p>
        </div>
    </footer>

    <div>
        <table width="100%" class="table border-all">
            <tr>
                <td width="20%" class="bg-gray h-1">Evaluador</td>
                <td colspan="6">{{$evaluacion->evaluador_nombre}}</td>
                <td rowspan="2" width="15%" class="bg-gray">Fecha</td>
                <td rowspan="2" width="15%">{{$evaluacion->fecha}}</td>
            </tr>
            <tr>
                <td class="bg-gray h-1">Puesto</td>
                <td colspan="6">{{$evaluacion->evaluador_puesto}}</td>
            </tr>
            <tr>
                <td width="20%" class="bg-gray h-1">Evaluado</td>
                <td colspan="6">{{$evaluacion->evaluado_nombre}}</td>
                <td rowspan="2" width="15%" class="bg-gray">Puntaje</td>
                <td rowspan="2" width="15%">{{$evaluacion->puntaje}}</td>
            </tr>
            <tr>
                <td class="bg-gray h-1">Puesto</td>
                <td colspan="6">{{$evaluacion->evaluado_puesto}}</td>
            </tr>
        </table>

        <table width="100%" class="table border-all">
            <tr>
                <td colspan="2" class="bg-blue border-bottom-none">SECCIÓN 1</td>
            </tr>
            <tr>
                <td colspan="2" class="bg-blue border-top-none">EVALUACIÓN</td>
            </tr>
            <tr>
                <td colspan="2" class="text-bold text-sm border-bottom-none">Marca con una X la casida considerando el
                    perfil y descripción de puesto correspondiente al puesto:</td>
            </tr>
            <tr>
                <td width="50%" class="border-right-none border-top-none ">
                    <div class="opciones">
                        <p>&nbsp;&nbsp;20&nbsp; &nbsp;&nbsp;&nbsp;= &nbsp;&nbsp;Rendimiento laboral no aceptable </p>
                        <p>&nbsp;&nbsp;40&nbsp; &nbsp;&nbsp;&nbsp;= &nbsp;&nbsp;Rendimiento laboral regular</p>
                        <p>&nbsp;&nbsp;60&nbsp; &nbsp;&nbsp;&nbsp;= &nbsp;&nbsp;Rendimiento laboral bueno</p>
                        <p>&nbsp;&nbsp;80&nbsp; &nbsp;&nbsp;&nbsp;= &nbsp;&nbsp;Rendimiento laboral muy bueno</p>
                        <p>100 &nbsp;&nbsp;&nbsp; = &nbsp;&nbsp;Rendimiento laboral excelente</p>
                    </div>
                </td>
                <td width="50%" class="border-left-none border-top-none" style="padding-left: 4rem;padding-right: 4rem;">
                    <p class="text-bold">Puntaje mínimo considerado como aceptable: 60 </p>
                </td>
            </tr>
        </table>
        <table width="100%" class="table border-all text-sm">
            <tr>
                <td colspan="2" width="30%" class="bg-blue ">CONCEPTO A EVALUAR</td>
                <td class="bg-blue">20</td>
                <td class="bg-blue">40</td>
                <td class="bg-blue">60</td>
                <td class="bg-blue">80</td>
                <td class="bg-blue">100</td>
                <td width="30%" class="bg-blue">OBSERVACIONES</td>
            </tr>
            @foreach($preguntas as $p)
            <tr>
                <td colspan="8" class="text-left bg-yellow">{{$p["c"]}}</td>
            </tr>
            @foreach($p["p"] as $p2)
            <tr>
                <td colspan="2" width="30%" class="bg-gray text-normal text-left">{{$p2["p"]}}</td>
                <td>{{$p2["r"]==2?"X":""}}</td>
                <td>{{$p2["r"]==4?"X":""}}</td>
                <td>{{$p2["r"]==6?"X":""}}</td>
                <td>{{$p2["r"]==8?"X":""}}</td>
                <td>{{$p2["r"]==10?"X":""}}</td>
                <td width="30%"></td>
            </tr>
            @endforeach
            @endforeach
        </table>
        <div style="page-break-after: always;"></div>
        <table class="table border-all" width="100%">
            <tr>
                <td colspan="2" class="bg-blue border-bottom-none">SECCIÓN 2</td>
            </tr>
            <tr>
                <td colspan="2" class="bg-blue border-top-none">
                    OBSERVACIONES Y CONCLUSIONES DERIVADAS DE LA RETROALIMENTACIÓN EVALUADOR - EVALUADO
                </td>
            </tr>
            <tr>
                <td width="50%" class="bg-gray">Compromiso por parte del Evaluador</td>
                <td width="50%" class="bg-gray">Compromiso por parte del Evaluado</td>
            </tr>
            <tr>
                <td class="h-4">
                    <p class="text-sm">{{$evaluacion->compromisos_evaluador}}</p>
                </td>
                <td class="h-4">
                    <p class="text-sm">{{$evaluacion->compromisos_evaluado}}</p>
                </td>
            </tr>
        </table>
        <br>
        <table class="table border-all" width="100%">
            <tr>
                <td class="bg-blue border-bottom-none">SECCIÓN 3</td>
            </tr>
            <tr>
                <td class="bg-blue border-top-none">
                    CAPACITACIONES REQUERIDAS ORIENTADAS A LA COMPETENCIA
                </td>
            </tr>
            <tr>
                <td class="text-left text-bold text-sm h-2 border-bottom-none">
                    Selecciona los fines a lograr con la capacitación requerida:
                </td>
            </tr>
            @foreach($capacitaciones as $c)
            <tr>
                <td class="border-top-none border-bottom-none">
                    <div class="text-left" style="margin-left: 3rem;">
                        <span style="border: 1px solid black; width: 2rem; display: inline-block;
                        text-align: center;">
                            {{$c["r"]==1?"X":" "}} &nbsp;
                        </span>
                        {{$c["c"]}}
                    </div>
                </td>
            </tr>
            @endforeach
            <tr>
                <td class="border-top-none ">&nbsp;</td>
            </tr>

            <tr>
                <td class="bg-gray">Temas Sugeridos</td>
            </tr>
            <tr>
                <td>&nbsp;</td>
            </tr>
            <tr>
                <td>&nbsp;</td>
            </tr>
            <tr>
                <td>&nbsp;</td>
            </tr>
            <tr>
                <td>&nbsp;</td>
            </tr>
            <tr>
                <td>&nbsp;</td>
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