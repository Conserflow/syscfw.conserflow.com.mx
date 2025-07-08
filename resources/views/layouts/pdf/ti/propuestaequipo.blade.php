<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PTI-01/F-03 - PROPUESTA DE EQUIPO DE TI</title>
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
        font-size: 13px;
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

    .center {
        margin-left: 5rem;
        margin-right: 5rem;
    }

    .center table tr .bg-gray {
        line-height: 1.2rem;
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
                <td rowspan="3"><b>PROPUESTA DE EQUIPO DE TI</b> </td>
                <td width="15%" class="text-bold"> CÓDIGO</td>
                <td width="15%">PTI-01/F-03</td>
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

    <div class="center">
        <table class="border-all" width="100%">
            <tr>
                <td class="bg-blue" colspan="2">Usuario</td>
            </tr>
            <tr>
                <td class="bg-gray" width="30%">Fecha</td>
                <td class="">{{$propuesta->fecha}}</td>
            </tr>
            <tr>
                <td class="bg-blue" colspan="2">REQUISITOS DE EQUIPO</td>
            </tr>
            <tr>
                <td class="bg-gray">Usuario / Puesto</td>
                <td>{{$propuesta->puesto}}</td>
            </tr>
            <tr>
                <td class="bg-gray">Necesidad especial</td>
                <td>{{$propuesta->necesidad_especial}}</td>
            </tr>
            <tr>
                <td class="bg-gray">Tipo de equipo</td>
                <td>{{$propuesta->tipo==1?"CÓMPUTO":
                    ($propuesta->tipo==2?"ACCESORIOS":
                    (($propuesta->tipo==3?"IMPRESIÓN":"VIDEO")))}}</td>
            </tr>
            <tr>
                <td class="bg-gray">Marca</td>
                <td>{{$propuesta->marca}}</td>
            </tr>
            <tr>
                <td class="bg-gray">Modelo</td>
                <td>{{$propuesta->modelo}}</td>
            </tr>
            <tr>
                <td class="bg-gray">Almacenamiento</td>
                <td>{{$propuesta->almacenamiento}}</td>
            </tr>
            <tr>
                <td class="bg-gray">Procesador</td>
                <td>{{$propuesta->procesador}}</td>
            </tr>
            <tr>
                <td class="bg-gray">RAM</td>
                <td>{{$propuesta->ram}}</td>
            </tr>
            <tr>
                <td class="bg-gray">Comentarios</td>
                <td>{{$propuesta->comentarios}}</td>
            </tr>
            <tr>
                <td class="bg-gray">Enlistar accesorios adicionales</td>
                <td>{{$propuesta->accesorios}}</td>
            </tr>

        </table>
    </div>
    <br>

    <div class="center">
        <table class="border-all" width="100%">
            <tr>
                <td class="bg-blue" colspan="5">Cotizaciones</td>
            </tr>
            <tr>
                <td class="bg-gray">No</td>
                <td class="bg-gray">Proveedor</td>
                <td class="bg-gray">Marca</td>
                <td class="bg-gray">Costo</td>
                <td class="bg-gray">Forma de pago</td>
            </tr>
            @foreach($cotizaciones as $i=> $c)
            <tr>
                <td>{{$i+1}}</td>
                <td>{{$c->proveedor}}</td>
                <td>{{$c->marca}}</td>
                <td>${{number_format($c->costo,2)}}</td>
                <td>{{$c->forma_pago}}</td>
            </tr>
            @endforeach
        </table>
        <br>
        <br>
        <br>
        <br>
        <br>
        <br>
        <br>
        <table width="100%">
            <tr>
                <td width="40%" class="">{{$propuesta->registra}}</td>
            </tr>
            <tr>
                <td width="40%" style="border-top: 1px solid black;" class="">Solicita</td>
                <td>&nbsp;</td>
                <td width="40%" style="border-top: 1px solid black;" class="">Autoriza</td>
            </tr>
            <tr>
                <td>Tecnología de la información</td>
                <td>&nbsp;</td>
                <td>Dirección General</td>
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