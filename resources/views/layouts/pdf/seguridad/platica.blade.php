<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>PSE-01/F-01 - Platicas De Seguridad</title>
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
                <td rowspan="3"><b>PLATICAS DE SEGURIDAD</b> </td>
                <td width="15%" class="text-bold"> CÓDIGO</td>
                <td width="15%">PSE-01/F-01</td>
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
    <main>
        <div>

            <table width="100%" class="table border-all">
                <tr>
                    <td width="15%" class="bg-gray">UBICACIÓN</td>
                    <td colspan="3">{{$platica->ubicacion}}</td>
                </tr>
                <tr>
                    <td  width="15%" class="bg-gray">FECHA</td>
                    <td colspan="3">{{$platica->fecha}}</td>
                </tr>
                <tr>
                    <td width="15%"  class="bg-gray">TEMA</td>
                    <td colspan="3">{{$platica->tema}}</td>
                </tr>
                <tr>
                    <td colspan="4" style="border: none;">&nbsp;</td>
                </tr>
            </table>
            <table class="table border-all" width="100%">

                <tr>
                    <td class="bg-blue" width="7%">NO</td>
                    <td class="bg-blue" width="40%">NOMBRE</td>
                    <td class="bg-blue" width="30%">PUESTO/CATEGORIA</td>
                    <td class="bg-blue">FIRMA</td>
                </tr>
                @for($i=1;$i<=25;$i++)
                <tr>
                    <td class="h-1">{{$i}}</td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
                @endfor
            </table>

            <br>
            <table class="table border-all" width="100%">
                <tr>
                    <td width="13%" class="bg-gray h-2">DIFUNDIÓ</td>
                    <td width="55%">{{$platica->nombre}}</td>
                    <td width="15%" class="bg-gray">FIRMA</td>
                    <td></td>
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