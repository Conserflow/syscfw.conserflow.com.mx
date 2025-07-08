<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PRH-01/F-01 - PERFIL Y DESCRIPCION DE PUESTO</title>
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
                <td rowspan="3"><b>PERFIL Y DESCRIPCION DE PUESTO</b> </td>
                <td width="15%" class="text-bold"> CÓDIGO</td>
                <td width="15%">PRH-01/F-01</td>
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

    <div style="margin-left: 3rem;margin-right: 3rem;">
        <table class="table text-left" width="100%">
            <tr>
                <td colspan="2" class="bg-blue text-center h-1">GENERALIDADES</td>
            </tr>
            <tr>
                <td width="35%" class="text-bold h-1">NOMBRE DEL PUESTO</td>
                <td>{{$puesto->puesto}}</td>
            </tr>
            <tr>
                <td width="35%" class="text-bold h-1">ÁREA</td>
                <td>{{$puesto->area}}</td>
            </tr>
            <tr>
                <td width="35%" class="text-bold h-1">JEFE INMEDIATO (PUESTO)</td>
                <td>{{$puesto->puesto_jefe}}</td>
            </tr>
            <tr>
                <td width="35%" class="text-bold h-1">PERSONAL A CARGO</td>
                <td>{{$puesto->personal_cargo}}</td>
            </tr>
            <tr>
                <td colspan="2" class="bg-blue text-center h-1">REQUISITOS DEL PUESTO</td>
            </tr>
            <tr>
                <td width="35%" class="text-bold h-1">ESCOLARIDAD:</td>
                <td>{{$puesto->escolaridad}}</td>
            </tr>
            <tr>
                <td width="35%" class="text-bold h-1">EXPERIENCIA:</td>
                <td>{{$puesto->experiencia}}</td>
            </tr>
            <tr>
                <td width="35%" class="text-bold h-1">COMPETENCIAS:</td>
                <td>{{$puesto->competencias}}</td>
            </tr>
            <tr>
                <td width="35%" class="text-bold h-1">CONOCIMIENTOS:</td>
                <td>{{$puesto->conocimientos}}</td>
            </tr>

            <tr>
                <td colspan="2" class="bg-blue text-center h-1">DESCRIPCIÓN DEL PUESTO</td>
            </tr>
            <tr>
                <td width="35%" class="text-bold h-1">OBJETIVO DE PUESTO</td>
                <td>{{$puesto->objetivo_puesto}}<br><br> </td>
            </tr>
            <tr>
                <td width="35%" class="text-bold h-1">FUCIONES</td>
                <td>{{$puesto->funciones}}<br><br></td>
            </tr>
            <tr>
                <td width="35%" class="text-bold h-1">RELACIÓN CON OTRAS ÁREAS</td>
                <td>{{$puesto->relacion_areas}}</td>
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