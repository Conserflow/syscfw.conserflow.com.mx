<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Entrada</title>
    <style>
        footer {
            position: fixed;
            bottom: -20px;
            left: 0px;
            right: 0px;
            height: 20px;

            /** Extra personal styles **/
            color: #4472C4;
            font-family: Arial, Helvetica, sans-serif;
            font-size: 12px;

        }

        .Table {
            display: table;
            width: 100%;
        }

        .Row {
            display: table-row;
        }

        .Cell {
            display: table-cell;
            padding-left: 5px;
            padding-right: 5px;
        }

        .tab {
            font-size: 12px;
            font-family: Arial, Helvetica, sans-serif;
            text-align: center;
        }

        .taf {
            font-size: 12px;
            font-family: Arial, Helvetica, sans-serif;
            text-align: justify;
        }

        .tit {
            font-size: 12px;
            font-family: Arial, Helvetica, sans-serif;
            text-align: center;
            border-collapse: collapse;
            border: 1px solid;
            width: 100%;
            padding-top: 40px;
        }

        .ord {
            font-size: 10px;
            font-family: Arial, Helvetica, sans-serif;
            border-collapse: collapse;
            border: 1px solid;
            width: 100%;
        }

        .cons {
            font-size: 12px;
            font-family: Arial, Helvetica, sans-serif;
            text-align: justify;
        }

        .pdb {
            font-size: 12px;
            font-family: Arial, Helvetica, sans-serif;
        }
    </style>

<body>
    <footer>
        <p style="color: #2F86DE;">CONSERFLOW S.A. DE C.V.</p>

    </footer>
    <div class="Table" style="padding-top: 0px">
        <div class="Row">
            <div class="Cell">
                <p style="padding-top:10px;"> <img src="img/conserflow.png" width="250px"></p>
            </div>
            <div class="Cell">
                <table class="tit">
                    <tr>
                        <td style="border: 1px solid;">CÓDIGO</td>
                        <td style="border: 1px solid;">PAL-01/F-01</td>
                    </tr>
                    <tr>
                        <td style="border: 1px solid;">REVISIÓN</td>
                        <td style="border: 1px solid;">{{$revision}}</td>
                    </tr>
                    <tr>
                        <td style="border: 1px solid;">EMISIÓN</td>
                        <td style="border: 1px solid;">{{$emision}}</td>
                    </tr>
                </table>
            </div>
        </div>
        <div class="Row">
            <div class="Cell">
                <p class="cons" style="padding-top: -30px;"><b> CONSERFLOW, S.A. DE C.V. </b> <br>
                    <b>RFC:</b> {{$rfc}} <br>
                    <b>DIRECCIÓN:</b>&nbsp;{!!$direccion!!}
                </p>
            </div>
            <div class="Cell" style="padding-top: -20px;">
                <table class="ord">

                    <tr>
                        <th
                            style="font-size: 18px;
              font-family: Arial, Helvetica, sans-serif;
              text-align: center; background-color: #d2caca">
                            <b>ENTRADA ALMACEN</b> </th>
                    </tr>
                    <tr>
                        <td></td>
                    </tr>
                    <tr>
                        <td>&nbsp;&nbsp;<b>FOLIO:</b>&nbsp;{{ $entrada->formato_entrada }} </td>
                    </tr>
                    <tr>
                        <td>&nbsp;&nbsp;<b>FECHA DE ENTRADA:</b>&nbsp;{{ $entrada->fecha }} </td>
                    </tr>
                    <tr>
                        <td>&nbsp;&nbsp;<b>ORDEN DE
                                COMPRA:</b>&nbsp;{{ $arreglo_partidas[0]['partidas'][0]->foliocompra }} </td>
                    </tr>
                    <tr>
                        <td>&nbsp;&nbsp;<b>PROYECTO:</b>&nbsp;{{ $arreglo_partidas[0]['partidas'][0]->foliocompra }}
                        </td>
                    </tr>
                    <tr>
                        <td></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
    <p width="100%" style="border-top: 1px solid #000;">&nbsp;</p>

    <table width="100%" class="tab">
        {{ $count = 1 }}
        <tr>
            <td width="5%"><b>Partida</b> </td>
            <td width="10%"><b>Cantidad</b></td>
            <td width="10%"><b>U.M</b></td>
            <td width="55%"><b>Descripción</b></td>
        </tr>
        @foreach ($arreglo_partidas as $ky => $ap)
            @if ($ap['tamanio'] != 0)
                @foreach ($ap['partidas'] as $key => $pe)
                    <tr>
                        <td>{{ $count }}</td>
                        <td>{{ $pe->cantidad }}</td>
                        <td>{{ $pe->aunidad }}</td>
                        <td><b>{{ $pe->descripcion }}</b></td>
                    </tr>
                    {{ $count += 1 }}
                @endforeach
            @endif
        @endforeach
    </table>

    <p width="100%" style="border-top: 1px solid #000;">&nbsp;</p>

    <div style="page-break-inside: avoid; width: 100%; bottom: 0px;">
        <p style="font-size: 16px;
    font-family: Arial, Helvetica, sans-serif;
    text-align: center;">
            <b>Documento creado electrónicamente y aprobado por las siguientes personas:</b>
        </p>


        <table width="100%" style="text-align: center;">
            <tr>
                <td class="pdb" width="40%"><b>Eláboro:</b> </td>
                <td width="15%">&nbsp;</td>
                <td class="pdb" width="40%"><b>Autorizó</b></td>
                <td width="5%">&nbsp;</td>
                
            </tr>

            <tr>
                {{ $img = 'administrativos/' . $entrada->empleado_almacen_id . '.png' }}
                <td>
                    @if (file_exists($img))
                        {{-- <img src="administrativos/{{ $entrada->empleado_almacen_id }}.png" width="190px"> --}}
                    @endif
                </td>
                <td>&nbsp;</td>
                <td>
                    @if ($entrada->fecha >= '2020-03-11')
                        {{-- <img src="administrativos/147.png" width="190px"> --}}
                    @endif
                </td>
                <td>&nbsp;</td>
                <td>
                    @if ($entrada->fecha >= '2020-03-09')
                        {{-- <img src="administrativos/36.png" width="190px"> --}}
                    @endif
                </td>
            </tr>

            <tr>
                <td class="pdb">
                    {{ $entrada->nombreea }}
                </td>
                <td>&nbsp;</td>
                <td class="pdb">
                    @if ($entrada->fecha >= '2020-03-11')
                        JUAN JAIME MARTINEZ HERRERA
                    @endif
                </td>
                <td>&nbsp;</td>
              
            </tr>


            <tr>
                <td
                    style="border-top: 1px solid;
      font-size: 12px;
      font-family: Arial, Helvetica, sans-serif">
                    <b>Almacén</b></td>
                <td>&nbsp;</td>
                <td style="border-top: 1px solid; font-size: 12px;
      font-family: Arial, Helvetica, sans-serif">
                    <b>Usuario</b></td>
                <td>&nbsp;</td>
                
            </tr>
        </table>

    </div>
    <script type="text/php">
    if (isset($pdf)) {
        $text = "PAGINA {PAGE_NUM} DE {PAGE_COUNT}";
        $size = 10;
        $color = #2F86DE;
        $font = $fontMetrics->getFont("Verdana");
        $width = $fontMetrics->get_text_width($text, $font, $size) / 2;
        $x = ($pdf->get_width() - $width) / 1;
        $y = $pdf->get_height() - 35;
        $pdf->page_text($x, $y, $text, $font, $size, $color);
    }
</script>
</body>

</html>
