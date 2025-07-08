<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>PSE-01/F-05 - Inspección Y Control De Botiquin De Primeros Auxilios</title>
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
            font-size: 10px;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            font-family: Arial, Helvetica, sans-serif;
            font-size: 10px;
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

        .text-left {
            text-align: left;
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
            font-size: 8;
            font-weight: bold;
            background-color: #BFBFBF;
        }

        .text-bold {
            font-weight: bold;
        }

        .text-line {
            text-decoration: underline;
        }

        .line-full {
            margin-left: 2.3rem;
            margin-right: 2rem;
            height: 1px;
            margin-top: 1rem;
            border: none;
            background-color: black;
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
                <td rowspan="3"><b>INSPECCIÓN Y CONTROL DE BOTIQUIN DE PRIMEROS AUXILIOS</b> </td>
                <td width="15%" class="text-bold"> CÓDIGO</td>
                <td width="15%">PSE-01/F-05</td>
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
        <table class="table border-all">
            <tr>
                <td width="10%" colspan="2" class="bg-gray text-bold">Área</td>
                <td width="35%" colspan="2">{{$inspeccion->area}}</td>
                <td width="10%" class="bg-gray text-bold">Número de Botiquín</td>
                <td width="10%">{{$inspeccion->numero}}</td>
                <td width="10%" class="bg-gray text-bold">Fecha de Inspección</td>
                <td width="15%" colspan="3">{{$inspeccion->fecha}}</td>
            </tr>
            <tr>
                <td width="10%" colspan="2" class="bg-gray text-bold">Nombrel del Inspector</td>
                <td width="55%" colspan="4">{{$inspeccion->inspector}}</td>
                <td width="25%" colspan="4" class="text-bold bg-blue">TIPO DE BOTIQUÍN</td>
            </tr>
            <tr>
                <td width="10%" colspan="2" class="bg-gray text-bold">Responsable del Botiquín</td>
                <td width="55%" colspan="4">{{$inspeccion->responsable}}</td>
                <td class="bg-gray text-bold">Portátil</td>
                <td class="">{{$inspeccion->tipo==1?"X":""}}</td>
                <td class="bg-gray text-bold">Fijo</td>
                <td class="">{{$inspeccion->tipo==2?"X":""}}</td>
            </tr>

        </table>

        <br>
        <table class="table border-all">
            <tr>
                <td colspan="10" class="bg-blue text-bold">BOTIQUINES</td>
            </tr>
            <tr>
                <td width="5%" class="bg-gray">No</td>
                <td width="35%" class="bg-gray" colspan="2">Material de Curación y de Apoyo</td>
                <td width="10%" class="bg-gray">Existencia</td>
                <td width="10%" class="bg-gray">Reposición</td>
                <td width="10%" class="bg-gray">Fecha de Vencimiento</td>
                <td width="27%" class="bg-gray" colspan="4" width="15%">Observación</td>
            </tr>

            @foreach($basicos as $i=>$b)
            <tr>
                <td width="5%" class="h-1">{{$i+1}}</td>
                <td width="35%" class="h-1" colspan="2">{{$b->material}}</td>
                <td width="10%" class="h-1">{{$b->existencia}}</td>
                <td width="10%" class="h-1">{{$b->reposicion}}</td>
                <td width="10%" class="h-1">{{$b->fecha_vencimiento}}</td>
                <td width="27%" class="h-1" colspan="4" width="15%">{{$b->observacion}}</td>
            </tr>
            @endforeach
            <tr>
                <td colspan="10" class="bg-blue text-bold">ELEMENTOS DE APOYO </td>
            </tr>
            @foreach($apoyo as $i=>$a)
            <tr>
                <td width="5%" class="h-1">{{$i+1}}</td>
                <td width="35%" class="h-1" colspan="2">{{$a->material}}</td>
                <td width="10%" class="h-1">{{$a->existencia}}</td>
                <td width="10%" class="h-1">{{$a->reposicion}}</td>
                <td width="10%" class="h-1">{{$a->fecha_vencimiento}}</td>
                <td width="27%" class="h-1" colspan="4" width="15%">{{$a->observacion}}</td>
            </tr>
            @endforeach
        </table>
        <br>
        <table class="table" style="border:1px solid black">
            <tr>
                <td colspan="5">
                    &nbsp;
                </td>
            </tr>
            <tr>
                <td width="43%">EL BOTIQUÍN ESTA EN UN LUGAR VISIBLE:</td>
                <td width="10%" class="text-left">SI:
                    <span class="text-line">
                        &nbsp;&nbsp;&nbsp;
                        {{$inspeccion->visible==1?"X":""}}
                        &nbsp;&nbsp;&nbsp;
                    </span>
                </td>
                <td width="10%" class="text-left"></td>
                <td width="10%" class="text-left"> NO:
                    <span class="text-line">
                        &nbsp;&nbsp;&nbsp;
                        {{$inspeccion->visible==2?"X":""}}
                        &nbsp;&nbsp;&nbsp;
                    </span>
                </td>
                <td></td>
            </tr>
            <tr>
                <td colspan="5" class="">
                    &nbsp;
                </td>
            </tr>
            <tr>
                <td width="43%">EL BOTIQUÍN ESTA EN BUEN ESTADO</td>
                <td width="10%" class="text-left">SI: _____

                </td>
                <td width="10%" class="text-left"></td>
                <td width="10%" class="text-left"> NO: _____

                </td>
                <td></td>
            </tr>
            <tr>
                <td colspan="5">&nbsp;</td>
            </tr>
            <tr>
                <td class="text-left "><span style="color: white;">mmmm</span> RECOMENDACIONES</td>
                <td colspan="4"></td>
            </tr>
            <tr>
                <td class="" colspan="5">
                    <hr class="line-full">
                    <hr class="line-full">
                    <hr class="line-full">
                </td>
            </tr>
            <tr>
                <td>
                    <p style="margin-left: 2rem;" class="text-bold">
                        NOMBRE Y FIRMA DE QUIÉN REALIZÓ LA INSPECCIÓN:
                    </p>
                </td>
                <td colspan="4" class="text-left">
                    {{$inspeccion->inspector}}
                </td>
            </tr>

            <tr>
                <td colspan="5">
                    &nbsp;
                </td>
            </tr>
        </table>

        <table class="table bor" style="border:1px solid black;margin-top: 0.2rem;">
        <tr>
                <td colspan="5">
                    <span class="text-bold">NOTA:</span>
                    <span>
                        Cada botiquín de primeros auxilios deberá contener la
                        cantidad establecida por la Supervisión de SSMA.
                    </span>
                </td>
            </tr>
            <tr>
                <td colspan="5">
                    El contenido de los botiquines de primeros auxilios puede modificarse
                    con la autorización del Supervisor de SSMA.
                </td>
            </tr>
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