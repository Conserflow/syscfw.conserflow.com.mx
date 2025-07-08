<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PTI-01/F-08 - Respaldo</title>
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
        <table width="100%" style="border-collapse: collapse; border: 1px solid; font-family: Arial, Helvetica, sans-serif;
            font-size: 14px;
            text-align: center;">
            <tr>
                <th colspan="4" style="border: 1px solid; ">
                    <div style="color: #4472C4;"> CONSERFLOW S.A. DE C.V.</div>
                </th>
            </tr>
            <tr>
                <td rowspan="3" width="20%"><img src="img/conserflow.png" width="120"></td>
                <td rowspan="3" style="border: 1px solid; text-align: center;" width="55%"><b>BITÁCORA DE RESPALDO DE INFORMACIÓN</b> </td>
                <td style="border: 1px solid; text-align: center;" width="10%"><b>CÓDIGO</b></td>
                <td style="border: 1px solid; text-align: center;" width="15%">PTI-01/F-08</td>
            </tr>
            <tr>
                <td style="border: 1px solid; text-align: center;"><b>REVISIÓN</b></td>
                <td style="border: 1px solid; text-align: center;">00</td>
            </tr>
            <tr>
                <td style="border: 1px solid; text-align: center;"><b>EMISIÓN</b></td>
                <td style="border: 1px solid; text-align: center;">01.ABR.20</td>
            </tr>
        </table>

    </header>

    <footer>
        <div>
            <p style="color: #0070c0;font-weight: bold;font-size: 10;">CONSERFLOW S.A. DE C.V.</p>
        </div>
    </footer>

    <main>
        <br>
        Año: {{$anio}}
        <br>
        <table width="100%" style="border-collapse: collapse; border: 2px solid; font-family: Arial, Helvetica, sans-serif;
            font-size: 12px;">
            <tr>
                <td style="border: 1px solid; background-color: #4472C4; text-align: center;">
                    <div style="color:white; "><b> Responsable de la Información</b></div>
                </td>
                <td style="border: 1px solid; background-color: #4472C4; text-align: center;">
                    <div style="color:white;"><b> Ruta de Respaldo</b></div>
                </td>
                <td style="border: 1px solid; background-color: #4472C4; text-align: center;">
                    <div style="color:white;"><b> Tamaño</b></div>
                </td>
                <td style="border: 1px solid; background-color: #4472C4; text-align: center;">
                    <div style="color:white;"><b>Fecha de Respaldo </b></div>
                </td>
                <td style="border: 1px solid; background-color: #4472C4; text-align: center;">
                    <div style="color:white;"><b> Ubicación de Respaldo</b></div>
                </td>
                <td style="border: 1px solid; background-color: #4472C4; text-align: center;">
                    <div style="color:white;"><b> Responsable del respaldo</b></div>
                </td>
                <td style="border: 1px solid; background-color: #4472C4; text-align: center;">
                    <div style="color:white;"><b> Observaciones</b></div>
                </td>
            </tr>
            @foreach ($data as $key => $value)
            <tr>
                <td style="border: 1px solid;">&nbsp;{{$value->responsable_i==-1?"SERVIDOR":$value->empleado_ii}}</td>
                <td style="border: 1px solid;">&nbsp;{{$value->ruta}}</td>
                <td style="border: 1px solid;">&nbsp;{{$value->tamanio}}</td>
                <td style="border: 1px solid;">&nbsp;{{$value->fecha}}</td>
                <td style="border: 1px solid;">&nbsp;{{$value->ubicacion}}</td>
                <td style="border: 1px solid;">&nbsp;{{$value->empleado_ri}}</td>
                <td style="border: 1px solid;">&nbsp;{{$value->observaciones}}</td>
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