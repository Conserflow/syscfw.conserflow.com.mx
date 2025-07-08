<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>PMN-02-F_03 VALE DE RESGUARDO</title>
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
        }

        .borce {
            border: 1px solid;
            text-align: center;
            background-color: #0082B0;
            font-weight: bold;
        }

        .borcd {
            border: 1px solid;
            text-align: center;
            background-color: #BFBFBF;
            font-weight: bold;
        }

        .borl {
            text-align: left;
        }

        .bort {
            text-align: center;
            border: 1px solid;
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
            font-size: 12px;
            text-align: center;
        }

        .taj {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 12px;
            text-align: justify;
        }

        .tal {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 12px;
            text-align: left;
        }
    </style>

</head>

<body>
    <header>

        <table width="100%" class="tabe">
            <tr>
                <td colspan="4" style="border: 1px solid; ">
                    <div style="color: #0171c1;"><b>
                            CONSERFLOW S.A. DE C.V.
                        </b></div>
                </td>
            </tr>
            <tr>
                <td rowspan="3" width="20%"><img src="img/conserflow.png" width="120"></td>
                <td rowspan="3" class="borc" width="50%">
                    <div><b>VALE DE RESGUARDO</b></div>
                </td>
                <td class="borc" width="15%">
                    <div><b>CÓDIGO</b></div>
                </td>
                <td class="borc" width="15%">
                    <div>PMN-02/F-05</div>
                </td>
            </tr>
            <tr>
                <td class="borc">
                    <div><b>REVISIÓN</b></div>
                </td>
                <td class="borc">
                    <div>00</div>
                </td>
            </tr>
            <tr>
                <td class="borc">
                    <div><b>EMISIÓN</b></div>
                </td>
                <td class="borc">
                    <div>29.JUN.20</div>
                </td>
            </tr>
        </table>

    </header>

    <footer>
        <p> CONSERFLOW S.A. DE C.V.</p>
    </footer>

    <!-- Wrap the content of your PDF inside a main tag -->
    <main>
        <table width="100%" class="tab">
            <tr>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
            </tr>
            <tr>
                <td width="60%">&nbsp;</td>
                <td class="borl" width="20%">Fecha:</td>
                <td style="border-bottom: 1px solid;" width="20%">{{$fecha}}</td>
            </tr>
        </table>

        <p class="tal">Por medio de la presente el</p>
        <div class="taj">C. <spam style="text-decoration: underline;">{{$vale->responsable}}</spam>
            declara que se le ha asignado un vehículo por el que firma el presente vale de conformidad y
            resguardo a su entera satisfacción, comprometiéndose a mantener dicho vehículo en el estado
            en el que lo recibe por un periodo de
            <spam style="text-decoration: underline;">{{$vale->periodo}}</spam>
            -. De igual manera el responsable del vehículo que en el presente documento se describe
            mantiene la obligación de conservar y custodiar el vehículo que se le asigna, bajo su cuidado.
            <br>
            <br>
            En el entendido de que en caso de que el mismo sufra cualquier daño parcial o total ocasionado
            por su dolo o negligencia se hará responsable de la reparación del mismo. En el caso de
            siniestro llamara a al aseguradora indicada en la siguiente tabla, así como también deberá
            informar inmediatamente al área de Control Vehicular, para que esta lo asesore.
        </div>
        <p class="tal">Nombre del propietario: Conserflow S.A de C.V.</p>

        <div height="15px;">&nbsp;</div>

        <table width="100%" class="tab">
            <tr>
                <td class="borce" colspan="2">
                    <div style="color:white;">DATOS DEL VEHICULO </div>
                </td>
            <tr>
                <td class="bort" style="background-color:#bfbfbf; border:1px solid;" width="30%">Marca</td>
                <td class="bort" width="70%">{{$vale->marca}}</td>
            </tr>
            <tr>
                <td class="bort" style="background-color:#bfbfbf; border:1px solid;">Modelo</td>
                <td class="bort">{{$vale->modelo}}</td>
            </tr>
            <tr>
                <td class="bort" style="background-color:#bfbfbf; border:1px solid;">Color</td>
                <td class="bort">{{$vale->color}}</td>
            </tr>
            <tr>
                <td class="bort" style="background-color:#bfbfbf; border:1px solid;">Placas</td>
                <td class="bort">{{$vale->placas}}</td>
            </tr>
            <tr>
                <td class="bort" style="background-color:#bfbfbf; border:1px solid;">No. de Serie</td>
                <td class="bort">{{$vale->numero_serie}}</td>
            </tr>
            <tr>
                <td class="bort" style="background-color:#bfbfbf; border:1px solid;">No. de Motor</td>
                <td class="bort" style="border:1px solid;">{{$vale->no_motor}}</td>
            </tr>
            <tr>
                <td class="bort" style="background-color:#bfbfbf; border:1px solid;">Capacidad de carga</td>
                <td class="bort" style="border:1px solid;">{{$vale->capacidad}}</td>
            </tr>
            <tr>
                <td class="bort" style="background-color:#bfbfbf; border:1px solid;">Cilindros</td>
                <td class="bort" style="border:1px solid;">{{$vale->cilindros}}</td>
            </tr>
            <tr>
                <td class="bort" style="background-color:#bfbfbf; border:1px solid;">Aseguradora</td>
                <td class="bort" style="border:1px solid;">{{$vale->proveedor}}</td>
            </tr>
            <tr>
                <td class="bort" style="background-color:#bfbfbf; border:1px solid;">Póliza de Seguro</td>
                <td class="bort" style="border:1px solid;">{{$vale->poliza_seguro}}</td>
            </tr>
            <tr>
                <td class="bort" style="background-color:#bfbfbf; border:1px solid;">Contacto Aseguradora</td>
                <td class="bort" style="border:1px solid;">{{$vale->contacto}}</td>
            </tr>
            <tr>
                <td class="bort" style="background-color:#bfbfbf; border:1px solid;">Tarjeta de Circulación</td>
                <td class="bort" style="border:1px solid;">{{$vale->u_tarjeta}}</td>
            </tr>
        </table>
        <div height="15px;">&nbsp;</div>

        <table width="100%" class="tab">
            <tr>
                <td colspan="5">&nbsp;</td>
            </tr>
            <tr>
                <td>&nbsp;&nbsp;&nbsp;</td>
                <td>{{$vale->responsable}}</td>
                <td>&nbsp;&nbsp;&nbsp;</td>
                <td>{{$vale->entrega}}</td>
                <td>&nbsp;&nbsp;&nbsp;</td>
            </tr>
            <tr>
                <td>&nbsp;&nbsp;&nbsp;</td>
                <td style="border-top: 1px solid;">Responsable del vehículo
                    <br> Nombre y firma
                </td>
                <td>&nbsp;&nbsp;&nbsp;</td>
                <td style="border-top: 1px solid;">Persona que proporciona el vehículo
                    <br> Nombrey firma
                </td>
                <td>&nbsp;&nbsp;&nbsp;</td>
            </tr>
        </table>
    </main>
    <script type="text/php">
        if (isset($pdf)) {
        $text = "PAGINA {PAGE_NUM} DE {PAGE_COUNT}";
        $size = 10;
        $font = $fontMetrics->getFont("Verdana");
        $width = $fontMetrics->get_text_width($text, $font, $size) / 2;
        $x = ($pdf->get_width() - $width) / 1;
        $y = $pdf->get_height() - 35;
        $pdf->page_text($x, $y, $text, $font, $size);
    }
</script>
</body>

</html>