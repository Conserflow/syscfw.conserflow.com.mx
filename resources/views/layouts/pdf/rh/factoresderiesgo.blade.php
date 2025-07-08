<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PRH-01/F-07 - CUESTIONARIO DE IDENTIFICIÓN DE FACTORES DE RIESGO PSICOSOCIAL Y ENTORNO ORGANIZACIONAL</title>
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
        padding-bottom: .20rem;
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

    .text-sm-2 {
        font-size: 7;
        font-weight: lighter;
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

    .bg-yellow {
        color: black;
        font-weight: bold;
        background-color: #ffc000;
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
                <td rowspan="3"><b>CUESTIONARIO DE IDENTIFICIÓN DE FACTORES DE RIESGO PSICOSOCIAL Y ENTORNO ORGANIZACIONAL</b> </td>
                <td width="15%" class="text-bold"> CÓDIGO</td>
                <td width="15%">PRH-01/F-07</td>
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

    <main>
        <table class="table border-all" width="100%">
            <tr>
                <td width="15%" class="bg-gray">Nombre</td>
                <td colspan="6">{{$evaluacion->empleado}}</td>
            </tr>
            <tr>
                <td class="bg-gray">Puesto</td>
                <td width="35%">{{$evaluacion->puesto}}</td>
                <td class="bg-gray" colspan="2">Fecha</td>
                <td colspan="3">{{$evaluacion->fecha}}</td>
            </tr>
            <tr>
                <td colspan="7" style="border: none;">
                    <div class="text-bold">
                        Marca con una "X" la casilla que consideres
                        como respuesta a cada cuestión
                    </div>
                </td>
            </tr>
            @foreach($conceptos as $c)
            <tr>
                <td colspan="7" class="bg-blue text-sm">{{$c["concepto"]}}</td>
            </tr>
            <tr>
                <td colspan="2" class="bg-gray text-sm-2">CONCEPTO</td>
                <td class="bg-gray text-sm-2">SIEMPRE</td>
                <td class="bg-gray text-sm-2">CASI SIEMPRE</td>
                <td class="bg-gray text-sm-2">ALGUNAS VECES</td>
                <td class="bg-gray text-sm-2">CASI NUNCA</td>
                <td class="bg-gray text-sm-2">NUNCA</td>
            </tr>
            @foreach($c["preguntas"] as $p)
            <tr>
                <td colspan="2" class=" h-1 text-sm text-left">{{$p}}</td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
            @endforeach
            @endforeach
            <tr>
                <td colspan="7" class="bg-blue text-sm">
                    {{$conceptos2[0]["concepto"]}}
                </td>
            </tr>
            <tr>
                <td colspan="4" rowspan="2">
                    En mi trabajo debo brindar servicio a clientes o usuarios:
                </td>
                <td>No</td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td>Sí</td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td colspan="7" class="bg-yellow text-sm h-1">
                    Si su respuesta fue "SÍ", responda las preguntas siguientes.
                    Si su respuesta fue "NO" pase a las preguntas de la sección siguiente.
                </td>
            </tr>
            <tr>
                <td colspan="2" class="bg-gray text-sm-2">CONCEPTO</td>
                <td class="bg-gray text-sm-2">SIEMPRE</td>
                <td class="bg-gray text-sm-2">CASI SIEMPRE</td>
                <td class="bg-gray text-sm-2">ALGUNAS VECES</td>
                <td class="bg-gray text-sm-2">CASI NUNCA</td>
                <td class="bg-gray text-sm-2">NUNCA</td>
            </tr>
            @foreach($conceptos2[0]["preguntas"] as $p)
            <tr>
                <td colspan="2" class="text-sm text-left h-1">{{$p}}</td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
            @endforeach

            <tr>
                <td colspan="4" rowspan="2">
                    Soy jefe de otros trabajadores:
                </td>
                <td>No</td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td>Sí</td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td colspan="7" class="bg-yellow text-sm h-1">
                    Si su respuesta fue "SÍ", responda las preguntas siguientes.
                    Si su respuesta fue "NO", ha concluido el cuestionario.
                </td>
            </tr>
            <tr>
                <td colspan="7" class="bg-blue text-sm">
                    {{$conceptos3[0]["concepto"]}}
                </td>
            </tr>
            <tr>
                <td colspan="2" class="bg-gray text-sm-2">CONCEPTO</td>
                <td class="bg-gray text-sm-2">SIEMPRE</td>
                <td class="bg-gray text-sm-2">CASI SIEMPRE</td>
                <td class="bg-gray text-sm-2">ALGUNAS VECES</td>
                <td class="bg-gray text-sm-2">CASI NUNCA</td>
                <td class="bg-gray text-sm-2">NUNCA</td>
            </tr>
            @foreach($conceptos3[0]["preguntas"] as $p)
            <tr>
                <td colspan="2" class="text-sm text-left h-1">{{$p}}</td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
            @endforeach
        </table>
    </main>

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