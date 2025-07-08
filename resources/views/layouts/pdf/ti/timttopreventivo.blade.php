<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>PTI-01_F-05 REPORTE DE MANTENIMIENTO PREVENTIVO DE EQUIPO DE TI</title>
    <style type="text/css">
        @page {
            margin-top: 3cm;
            margin-left: 1cm;
            margin-right: 1cm;
            margin-bottom: 2cm;
        }

        header {
            position: fixed;
            top: -100px;
            left: 0px;
            right: 0px;
            height: 160px;
        }

        footer {
            position: fixed;
            bottom: -40px;
            left: 0px;
            right: 0px;
            height: 20px;
            font-family: Arial, Helvetica, sans-serif;
            font-size: 14px;
        }

        .borc {
            border: 1px solid;
            text-align: center;
            font-size: 12px;
        }

        .borce {
            border: 1px solid;
            text-align: center;
            background-color: #0070C0;
            font-weight: bold;
            font-size: 14px;
        }

        .borcd {
            border: 1px solid;
            text-align: center;
            background-color: #B2DE82;
            font-weight: bold;
        }

        .borl {
            border: 1px solid;
            font-size: 12;
        }

        .gris {
            border: 1px solid;
            font-size: 14px;
            font-weight: bold;
            background-color: #BFBFBF;
        }

        .tabe {
            border-collapse: collapse;
            border: 1px solid;
            font-family: Arial, Helvetica, sans-serif;
            font-size: 14px;
            text-align: center;
        }

        .tab {
            border-collapse: collapse;
            font-family: Arial, Helvetica, sans-serif;
            font-size: 8px;
            text-align: center;
        }
    </style>

</head>

<body>
    <header>

        <table width="100%" class="tabe">
            <tr>
                <td colspan="4" style="border: 1px solid; ">
                    <div style="color: #0070C0;"><b> CONSERFLOW S.A. DE C.V.</b></div>
                </td>
            </tr>
            <tr>
                <td rowspan="3" width="20%"><img src="img/conserflow.png" width="120"></td>
                <td rowspan="3" class="borc" width="60%"><b>REPORTE DE MANTENIMIENTO PREVENTIVO DE EQUIPO DE TI</b> </td>
                <td class="borc" width="10%">
                    <div ><b>CÓDIGO</b> </div>
                </td>
                <td class="borc" width="10%">
                    <div >PTI-01/F-05</div>
                </td>
            </tr>
            <tr>

                <td class="borc">
                    <div ><b>REVISIÓN</b></div>
                </td>
                <td class="borc">
                    <div >00</div>
                </td>
            </tr>
            <tr>
                <td class="borc">
                    <div ><b>EMISIÓN</b></div>
                </td>
                <td class="borc">
                    <div >01.ABR.20</div>
                </td>
            </tr>
        </table>

    </header>

    <footer>
        <table style="color: #0070C0;" width="80%">
            <tr>
                <td width="80%">CONSERFLOW S.A. DE C.V.</td>
            </tr>
        </table>
    </footer>

    <!-- Wrap the content of your PDF inside a main tag -->
    <main>

        <table width="100%" class="tab">
            <tr>
                <td class="borce" colspan="6">
                    <div style="color: white;">DATOS GENERALES DEL MANTENIMIENTO</div>
                </td>
            </tr>
            <tr>
                <td class="gris" width="20%">Número de serie</td>
                <td class="borl" width="80%" colspan="5">{{$mtto["equipo"]->no_serie}}</td>
            </tr>
            <tr>
                <td class="gris">Fecha</td>
                <td class="borl">{{$mtto["mtto"]->fecha}}</td>
                <td class="gris">Hora de inicio</td>
                <td class="borl">{{$mtto["mtto"]->hora_inicio}}</td>
                <td class="gris">Hora de término</td>
                <td class="borl">{{$mtto["mtto"]->hora_final}}</td>
            </tr>
            <tr>
                <td class="gris">Persona asignada al servicio</td>
                <td class="borl" colspan="5">{{$mtto["mtto"]->empelado_revisa}}</td>
            </tr>
        </table>
        <table width="100%" class="tab">
            <tr>
                <td class="borce">
                    <div style="color: white;">ACTIVIDADES</div>
                </td>
            </tr>
            <tr>
                <td class="borl" height="40px;">
                    <br>
                    {{$mtto["mtto"]->actividades}}
                    <br>
                    <br>
                </td>
            </tr>
            <tr>
                <td class="borce">
                    <div style="color: white;">OBSERVACIONES</div>
                </td>
            </tr>
            <tr>
                <td class="borl" height="40px;">
                    <br>
                    {{$mtto["mtto"]->observaciones}}
                    <br>
                    <br>
                </td>
            </tr>
            <tr>
                <td class="borce">
                    <div style="color: white;">REFACCIONES Y MATERIALES USADOS</div>
                </td>
            </tr>
            <tr>
                <td class="borl" height="40px;">
                    <br>
                    @foreach($mtto["consumibles"] as $con)
                    <span style="font-size: 10;">
                        &nbsp;&nbsp;{{$con->nombre}} &nbsp;&nbsp;&nbsp;
                    </span>
                    @endforeach
                    <br>
                    <br>
                </td>
            </tr>

        </table>
        <br>
        <br>
        <table width="100%" style="padding-left: 20px;padding-right: 20px; font-family: Arial, Helvetica, sans-serif;
            font-size: 12px; text-align: center; font-weight: bold;">
            <tr>
                <td width="30%">REALIZÓ
                </td>
                <td width="35%">&nbsp;</td>
                <td width="30%">AUTORIZÓ
                </td>
            </tr>

            <tr>
                <td width="30%">
                    <img src="administrativos/{{$mtto['mtto']->empleado_asignado}}.png" width="150px" height="100px">
                </td>
                <td width="35%">&nbsp;</td>
                <td width="30%">
                    <img src="administrativos/{{$mtto['mtto']->empleado_autoriza}}.png" width="150px" height="100px">
                </td>
            </tr>

            <tr>
                <td style="border-bottom: 1px solid;">
                    {{$mtto["mtto"]->empleado_sigado}}
                </td>
                <td></td>
                <td style=" border-bottom: 1px solid;">
                    {{$mtto["mtto"]->empelado_revisa}}
                </td>
            </tr>
            <tr>
                <td>
                    <br>NOMBRE Y FIRMA
                </td>
                <td>&nbsp;</td>
                <td>
                    <br>NOMBRE Y FIRMA
                </td>
            </tr>
        </table>
    </main>
    <script type="text/php">
        if (isset($pdf)) {
        $text = "PÁGINA {PAGE_NUM} DE {PAGE_COUNT}";
        $size = 10;
        $color = #0070C0;
        $font = $fontMetrics->getFont("Verdana");
        $width = $fontMetrics->get_text_width($text, $font, $size) / 2;
        $x = ($pdf->get_width() - $width) / 1;
        $y = $pdf->get_height() - 35;
        $pdf->page_text($x, $y, $text, $font, $size,$color);
    }
</script>
</body>

</html>
