<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PTI-01/F-07 - VALE DE SALIDA</title>
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
                <td rowspan="3"><b>VALE DE SALIDA</b> </td>
                <td width="15%" class="text-bold">CÓDIGO</td>
                <td width="15%">PTI-01/F-07</td>
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

    <div>
        <table class="border-all" width="100%">
            <tr>
                <td width="15%" class="bg-gray text-bold">PROYECTO</td>
                <td width="35%">{{$data->nombre_corto}}</td>

                <td width="15%" class="bg-gray text-bold">FECHA DE SALIDA</td>
                <td width="35%">{{$fecha_salida}}</td>
            </tr>
            <tr>
                <td width="15%" class="bg-gray text-bold">UBICACIÓN</td>
                <td width="35%">{{$data->ciudad}}</td>

                <td width="15%" class="bg-gray text-bold">FECHA DE RETORNO</td>
                <td width="35%">{{$fecha_retorno}}</td>
            </tr>
        </table>

        <br>
        <table class="border-all" width="100%">
            <tr>
                <td width="10%" class="bg-blue">PARTIDA</td>
                <td width="10%" class="bg-blue">CANTIDAD</td>
                <td width="10%" class="bg-blue">UNIDAD</td>
                <td width="" class="bg-blue">DESCRIPCIÓN</td>
            </tr>
            @foreach ($arreglo as $i => $p)
            <tr>
                <td width="10%" class="bg-gray">{{$i+1}}</td>
                <td width="10%" class="">{{$p['data']->cantidad}}</td>
                <td width="10%" class="">{{$p['data']->unidad}}</td>
                <td width="" class="">{{$p["descripcion"]}}</td>
            </tr>
            @endforeach
        </table>
        <br>
        <br>
        <table width="100%">
            <tr>
                <td class="text-bold" width="3%">&nbsp;</td>
                <td class="text-bold" width="">SOLICITA</td>
                <td class="text-bold" width="3%">&nbsp;</td>
                <td class="text-bold" width="">ENTREGA</td>
                <td class="text-bold" width="3%">&nbsp;</td>
                <td class="text-bold" width="">AUTORIZA</td>
                <td class="text-bold" width="3%">&nbsp;</td>
                <td class="text-bold" width="">REGRESA</td>
                <td class="text-bold" width="3%">&nbsp;</td>
            </tr>
            <tr>
                <td>
                    <br>
                    <br>
                    <br>
                    <br>
                </td>
            </tr>
            <tr>
                <td style="height: 30px;">&nbsp;</td>
                <td style="height: 30px;">&nbsp;{{$data->empleado_r}}</td>
                <td style="height: 30px;">&nbsp;</td>
                <td style="height: 30px;">&nbsp;{{$data->empleado_e}}</td>
                <td style="height: 30px;">&nbsp;</td>
                <td style="height: 30px;">&nbsp;</td>
                <td style="height: 30px;">&nbsp;</td>
                <td style="height: 30px;">&nbsp;{{$data->empleado_retorno}}</td>
                <td style="height: 30px;">&nbsp;</td>
            </tr>
            <tr>
                <td class="text-bold">&nbsp;</td>
                <td class="text-bold" style="border-top: 1px solid;">NOMBRE Y FIRMA</td>
                <td class="text-bold">&nbsp;</td>
                <td class="text-bold" style="border-top: 1px solid;">NOMBRE Y FIRMA</td>
                <td class="text-bold">&nbsp;</td>
                <td class="text-bold" style="border-top: 1px solid;">NOMBRE Y FIRMA</td>
                <td class="text-bold">&nbsp;</td>
                <td class="text-bold" style="border-top: 1px solid;">NOMBRE Y FIRMA</td>
                <td class="text-bold">&nbsp;</td>
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