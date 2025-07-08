<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>PSE-01/F-01 - INSPECCION DE EQUIPO DE PROTECCION PERSONAL</title>
    <style type="text/css">
        @page {
            margin-top: 3cm;
            margin-left: 1cm;
            margin-right: 1cm;
            margin-bottom: 2cm;
        }

        header {
            position: fixed;
            top: -70px;
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

        .table {
            border-collapse: collapse;
            font-family: Arial, Helvetica, sans-serif;
            font-size: 12px;
            text-align: center;
        }

        .table-criterios {
            border: 1px solid black;
            font-size: 7;
        }

        .border-all tr td {
            border: 1px solid black;
        }

        .text-blue {
            color: #0070c0;
        }

        .bg-blue {
            background-color: #0070c0;
            color: white;
        }

        .h-4 {
            padding-top: 1.5rem;
            padding-bottom: 1.5rem;
        }

        .ht-3 {
            padding-top: .8rem;
        }

        .hb-3 {
            padding-bottom: .8rem;
        }

        .h-2 {
            padding-top: .5rem;
            padding-bottom: .5rem;
        }

        .h-1 {
            padding-top: .3rem;
            padding-bottom: .3rem;
        }

        .bg-gray {
            color: black;

            background-color: #BFBFBF;
        }

        .text-bold {
            font-weight: bold;
        }

        .text-left {
            text-align: left;
        }

        .text-vertical {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 12px;
            color: black;
            margin-left: -0.5rem;
            text-align: center;
            g-origin: 10% 10%;
            -webkit-transform: rotate(270deg);
            -moz-transform: rotate(270deg);
            -ms-transform: rotate(270deg);
            -o-transform: rotate(270deg);
            transform: rotate(270deg);
            position: absolute;
            z-index: 99;

        }
    </style>

</head>

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
                <td rowspan="3"><b>INSPECCION DE EQUIPO DE PROTECCION PERSONAL</b> </td>
                <td width="15%" class="text-bold"> CÓDIGO</td>
                <td width="15%">PSE-01/F-03</td>
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
        <p style="color: #2F86DE;">CONSERFLOW S.A. DE C.V.</p>
    </footer>

    <!-- Wrap the content of your PDF inside a main tag -->
    <div>
        <table style="margin-left: -1px; margin-right: -1px;" class="table table-criterios" width="100%">
            <tr>
                <td class="ht-3">CRITERIOS DE REVISIÓN</td>
                <td>OK</td>
                <td class="text-left">BUENAS CONDICIONES</td>
                <td>X</td>
                <td class="text-left">MALAS CONDICIONES</td>
            </tr>
            <tr>
                <td class="hb-3">&nbsp;</td>
                <td>CD</td>
                <td class="text-left">CAMBIO POR DESGASTE</td>
                <td>N/A</td>
                <td class="text-left">NO APLICA</td>
            </tr>
        </table>
        <table class="table border-all" width="100%">
            <tr>
                <td width="10%" class="bg-blue text-bold h-1">Ubicación:</td>
                <td width="10%">{{$inspeccion->ubicacion}}</td>
                <td colspan="8" class="bg-blue text-bold">Equipo de Protección Personal</td>
                <td colspan="5" class="bg-blue text-bold">Equipo de Protección Adicional</td>
            </tr>
            <tr>
                <td width="10%" class="bg-blue text-bold ht-3 hb-3">Fecha de Inspección</td>
                <td>{{$inspeccion->fecha}}</td>

                <td class="bg-gray" rowspan="2"></td>
                <td class="bg-gray" rowspan="2"></td>
                <td class="bg-gray" rowspan="2"></td>
                <td class="bg-gray" rowspan="2"></td>
                <td class="bg-gray" rowspan="2"></td>
                <td class="bg-gray" rowspan="2"></td>
                <td class="bg-gray" rowspan="2"></td>
                <td class="bg-gray" rowspan="2"></td>
                <td class="bg-gray" rowspan="2"></td>
                <td class="bg-gray" rowspan="2"></td>
                <td class="bg-gray" rowspan="2"></td>
                <td class="bg-gray" rowspan="2"></td>
                <td class="bg-gray" rowspan="2"></td>
            </tr>
            <tr>
                <td colspan="2" class="bg-blue h-4 text-bold">Nombre del Trabajador</td>
            </tr>
            @foreach([1] as $d)
            @foreach($participantes as $p)
            <tr>
                <td colspan="2">{{$p->empleado}}</td>
                <td>{{$p->epp_overol}}</td>
                <td>{{$p->epp_calzado}}</td>
                <td>{{$p->epp_casco}}</td>
                <td>{{$p->epp_guantes}}</td>
                <td>{{$p->epp_ocular}}</td>
                <td>{{$p->epp_respiratoria}}</td>
                <td>{{$p->epp_auditiva}}</td>
                <td>{{$p->epp_barbiquejo}}</td>
                <td>{{$p->epa_respiratoria}}</td>
                <td>{{$p->epa_arnes}}</td>
                <td>{{$p->epa_careta}}</td>
                <td>{{$p->epa_mangas}}</td>
                <td>{{$p->epa_mascarilla}}</td>
            </tr>
            @endforeach
            @endforeach
            <tr>
                <td colspan="15" class="text-left" style="border-bottom: none;">
                    Observaciones
                </td>
            <tr>
                <td style="border-bottom: none;border-top: none;" colspan="15">{{$inspeccion->observaciones}}</td>
            </tr>
            <tr>
                <td colspan="15" style="border-top: none;" class="h-1"></td>
            </tr>
        </table>
        <table class="table" width="100%" style="border:1px solid black; margin-top: -1px; margin-left: -1px; margin-right: -1px;">
            <tr>
                <td></td>
                <td class="ht-3">Realizó</td>
                <td></td>
                <td class="ht-3">Revisó</td>
                <td></td>
            </tr>
            <tr>
                <td></td>
                <td class="ht-3">Nombrey Firma</td>
                <td></td>
                <td class="ht-3">Nombrey Firma</td>
                <td></td>
            </tr>
            <tr>
                <td></td>
                <td class="hb-3">Supervisor de Seguridad en Sitio</td>
                <td></td>
                <td class="hb-3">Responsable de SSMA</td>
                <td></td>
            </tr>
        </table>
        <span class="text-vertical" style="max-width: 100px; top: 120px; left: 190px;">Overol o ropa de algodón RF</span>
        <span class="text-vertical" style="max-width: 100px; top: 120px; left: 270px;"> Calzado de Seguridad</span>
        <span class="text-vertical" style="max-width: 100px; top: 125px; left: 320px;"> Casco con logo</span>
        <span class="text-vertical" style="max-width: 100px; top: 120px; left: 373px;"> Guantes adecuados al riesgo de trabajo</span>
        <span class="text-vertical" style="max-width: 100px; top: 125px; left: 430px;"> Protección ocular </span>
        <span class="text-vertical" style="max-width: 100px; top: 125px; left: 500px;"> Protección respiratoria</span>
        <span class="text-vertical" style="max-width: 100px; top: 120px; left: 565px;"> Protección Auditiva</span>
        <span class="text-vertical" style="max-width: 100px; top: 125px; left: 615px;"> Barbiquejo</span>
        <span class="text-vertical" style="max-width: 100px; top: 120px; left: 660px;"> Protección Respiratoria ERA</span>
        <span class="text-vertical" style="max-width: 120px; top: 128px; left: 715px;"> Arnes de Seguridad</span>
        <span class="text-vertical" style="max-width: 100px; top: 130px; left: 795px;"> Careta Facial</span>
        <span class="text-vertical" style="max-width: 100px; top: 120px; left: 845px;"> Mangas, peto, polainas,rodillera </span>
        <span class="text-vertical" style="width: 120px; top: 125px; left: 893px;"> Mascarilla de media cara/Cara completa</span>

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