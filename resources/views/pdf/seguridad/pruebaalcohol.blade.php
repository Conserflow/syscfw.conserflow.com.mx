<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>PSE-01/F-06 - Registro De Pruebas De Alcoholimetría</title>
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

        .table-observaciones {
            border-collapse: collapse;

            font-family: Arial, Helvetica, sans-serif;
        }

        .table-observaciones .titulo {
            font-size: 9;
            border-bottom: none;
        }

        .table-observaciones .obs {
            font-size: 9;
            border-bottom: none;
            border-top: none;
            text-align: center;
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

        .h-2 {
            padding-top: .5rem;
            padding-bottom: .5rem;
        }

        .h-1 {
            padding-top: .4rem;
            padding-bottom: .4rem;
        }

        .bg-gray {
            color: black;

            background-color: #BFBFBF;
        }

        .text-bold {
            font-weight: bold;
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
                <td rowspan="3" class="text-blue"><b>REGISTRO DE PRUEBAS DE ALCOHOLIMETRÍA</b> </td>
                <td width="15%" class="text-bold"> CÓDIGO</td>
                <td width="15%">PSE-01/F-06</td>
            </tr>
            <tr>
                <td class="text-bold">REVISIÓN</td>
                <td>01</td>
            </tr>
            <tr>
                <td class="text-bold">EMISIÓN</td>
                <td>10.NOV.22</td>
            </tr>

        </table>


    </header>

    <footer>
        <p style="color: #2F86DE;">CONSERFLOW S.A. DE C.V.</p>
    </footer>

    <!-- Wrap the content of your PDF inside a main tag -->
    <main>
        <div>
            <table width="100%" class="table border-all">
                <tr>
                    <td width="20%" class="bg-gray text-bold">FECHA</td>
                    <td colspan="3">{{$prueba->fecha}}</td>
                    <td width="15%" class="bg-gray text-bold">UBICACIÓN</td>
                    <td colspan="3">{{$prueba->ubicacion}}</td>
                </tr>
                <tr>
                    <td width="20%" class="bg-gray text-bold">REALIZÓ PRUEBA</td>
                    <td colspan="7" style="border: 1px solid black;">{{$prueba->responsable}}</td>
                </tr>
                <tr>
                    <td colspan="8" style="border: none;">&nbsp;</td>
                </tr>
            </table>
            <table class="table border-all" width="100%">
                <tr>
                    <td colspan="5" class="text-center text-bold bg-blue">
                        TABLA DE CONVERSIÓN DE ALCOHOL
                    </td>
                </tr>
                <tr>
                    <td width="15%" class="text-bold">RESULTADO EN PANTALLA (BAC)</td>
                    <td width="20%" class="text-bold">CONCENTRACIÓN DE ALCOHOL EN LA SANGRE (g/L)</td>
                    <td width="15%" class="text-bold">UNIDAD ALCOHOLICA</td>
                    <td width="20%" class="text-bold">CANTIDAD DE BEBIDA APROX</td>
                    <td rowspan="4" width="30%">
                        La abreviación BAC:
                        Breath Alcohol Concentration se traduce como
                        CAA concentración
                        de Alcohol en el Aliento
                    </td>
                </tr>
                <tr>
                    <td>0.00% - 0.01</td>
                    <td>0.00 - 0.10</td>
                    <td>1 unidad</td>
                    <td>1 copa de vino</td>
                </tr>
                <tr>
                    <td>0.02% - 0.04</td>
                    <td>0.20 - 0.40</td>
                    <td>2 unidades</td>
                    <td>1 - 1/2 cerveza</td>
                </tr>
                <tr>
                    <td>0.05%</td>
                    <td>0.5</td>
                    <td>5 unidades</td>
                    <td>3 cervezas</td>
                </tr>
            </table>
            <br>

            <table class="table border-all" width="100%">

                <tr>
                    <td class="text-bold bg-blue" width="7%">N°</td>
                    <td class="text-bold bg-blue" width="30%">NOMBRE</td>
                    <td class="text-bold bg-blue" width="20%">PUESTO/CATEGORIA</td>
                    <td class="text-bold bg-blue" width="20%">RESULTADO</td>
                    <td class="text-bold bg-blue">FIRMA</td>
                </tr>
                @for($i=1;$i<=20;$i++) <tr>
                    <td class="h-1 bg-gray">{{$i}}</td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    </tr>
                    @endfor
            </table>
            <table class="table-observaciones border-all" style="margin-top: 0.5rem;" width="100%">
                <tr>
                    <td class="titulo">Observaciones</td>
                </tr>
                <tr>
                    <td class="obs">{{$prueba->observaciones}}</td>
                </tr>
                <tr>
                    <td style="border-top: none;">&nbsp;</td>
                </tr>
            </table>
        </div>

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