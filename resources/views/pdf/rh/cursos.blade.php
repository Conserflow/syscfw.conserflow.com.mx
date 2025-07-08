<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PRH-01/F-05 - LISTA DE ASISTENCIA</title>
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
                <td rowspan="3"><b>LISTA DE ASISTENCIA</b> </td>
                <td width="15%" class="text-bold"> CÓDIGO</td>
                <td width="15%">PRH-01/F-05</td>
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
        <table class="border-all" width="100%">
            <tr>
                <td colspan="7" class="bg-blue">DETALLES DEL CURSO</td>
            </tr>
            <tr>
                <td width="15%" class="bg-gray">CURSO</td>
                <td width="35%">{{$curso->nombre}}</td>
                <td width="20%" class="bg-gray">INSTRUCTOR</td>
                <td colspan="4" width="30%">{{$curso->instructor}}</td>
            </tr>
            <tr>
                <td width="15%" class="h-2 bg-gray">FECHA</td>
                <td width="35%">{{$curso->fecha}}</td>
                <td width="20%" class="bg-gray">FIRMA</td>p
                <td colspan="4" width="30%"></td>
            </tr>
            <tr>
                <td width="15%" rowspan="7" class="bg-gray">TEMARIO</td>
                <td width="35%" rowspan="7">
                    <div class="text-left" style="margin: 0.3rem;">
                        @foreach($temario as $t)
                        <span>{{$t}}<br> </span>
                        @endforeach
                    </div>
                </td>
                <td width="20%" class="bg-gray">EMPRESA</td>
                <td colspan="4" width="30%">{{$curso->empresa}}</td>
            </tr>
            <tr>
                <td width="20%" class="bg-gray">LUGAR</td>
                <td colspan="4" width="30%">{{$curso->lugar}}</td>
            </tr>
            <tr>
                <td width="20%" class="bg-gray">DURACIÓN(HRS)</td>
                <td colspan="4" width="30%">{{$curso->duracion}} hrs</td>
            </tr>
            <tr>
                <td width="20%" class="bg-gray">APLICA EVALUACIÓN DEL TEMA</td>
                <td width="8%" class="bg-gray">SI</td>
                <td width="7%">{{$curso->evaluacion_tema==1?"X":""}}</td>
                <td width="8%" class="bg-gray">NO</td>
                <td width="7%">{{$curso->evaluacion_tema==0?"X":""}}</td>
            </tr>
            <tr>
                <td width="20%" class="bg-gray">APLICA EVALUACIÓN DEL CURSO</td>
                <td width="8%" class="bg-gray">SI</td>
                <td width="7%">{{$curso->evaluacion_curso==1?"X":""}}</td>
                <td width="8%" class="bg-gray">NO</td>
                <td width="7%">{{$curso->evaluacion_curso==0?"X":""}}</td>
            </tr>
            <tr>
                <td colspan="5" class="bg-blue">DESCRIPCIÓN DE EVALUACIÓN</td>
            </tr>
            <tr>
                <td colspan="5" class="h-4">
                    {{$curso->descripcion_evaluacion}}
                </td>
            </tr>
        </table>
        <br>
        <table class="table border-all" width="100%">
            <tr>
                <td class="bg-blue" colspan="5">PARTICIPANTES</td>
            </tr>
            <tr>
                <td class="bg-gray text-bold">No.</td>
                <td class="bg-gray text-bold">NOMBRE</td>
                <td class="bg-gray text-bold">AREA</td>
                <td class="bg-gray text-bold">FIRMA</td>
                <td class="bg-gray text-bold">CALIFICACIÓN</td>
            </tr>
            @for($i=1; $i<=40; $i++) <tr>
                <td width="5%" class="h-2 bg-gray">{{$i}}</td>
                <td width="40%"></td>
                <td width="20%"></td>
                <td width="20%"></td>
                <td width="15%"></td>
                </tr>
                @endfor

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