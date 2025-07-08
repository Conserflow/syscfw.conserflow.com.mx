<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>PSE-02_F-03 BITACORA DE ENTRADA Y SALIDA DE RESIDUO DE MANEJO ESPECIAL</title>

    <head>
        <style>
            /** Define the margins of your page **/
            @page {
                margin-top: 3cm;
                margin-left: 2cm;
                margin-right: 2cm;
                margin-bottom: 2cm;
            }

            header {
                position: fixed;
                top: -70px;
                left: 0px;
                right: 0px;
                height: 20px;

                /** Extra personal styles **/
                /* background-color: #03a9f4;
                color: white;
                text-align: center;
                line-height: 35px; */
            }

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

            .letter {
                font-family: Arial, Helvetica, sans-serif;
                font-size: 16px;
            }

            .border-all {
                border: 1px solid black;
            }

            .bg-gray {
                background-color: #BFBFBF;
            }

            .table {
                border-collapse: collapse;
                font-family: Arial, Helvetica, sans-serif;
                font-size: 14px;
                text-align: center;
                font-weight: bold;

            }

            .table-border tr td {
                border: 1px solid;
            }

            .table-border tr th {
                border: 1px solid;
            }

            .text-blue {
                color: #0070c0;
            }

            tr.border-bold td {
                border: 2px solid red;
            }

            .bg-blue {

                color: white;
                background-color: #0070c0;
            }

            .center-v {
                vertical-align: middle;
            }

            .bg-p {
                padding-top: 10px;
                padding-bottom: 10px;
                background-color: #2f5597;
                color: white;

            }

            .bg-pc {
                background-color: #996633;
            }

            .bg-m {
                background-color: #ffe699;
            }

            .bg-v {
                background-color: white;
            }

            .bg-mf {
                background-color: #ff572f;
            }

            .bg-mnf {
                background-color: #ffcb25;
            }
        </style>
    </head>

<body>

    <!-- Define header and footer blocks before your content -->
    <header>
        <table width="100%" class="table table-border">
            <tr>
                <th colspan="4">
                    <div class="text-blue"> CONSERFLOW S.A. DE C.V.</div>
                </th>
            </tr>
            <tr>
                <td rowspan="3" width="20%"><img src="img/conserflow.png" width="120"></td>
                <td rowspan="3" width="55%">
                    <b>BITACORA DE ENTRADA Y SALIDA DE RESIDUO DE MANEJO ESPECIAL</b>
                </td>
                <td width="10%">CÓDIGO</td>
                <td width="15%">PSE-02/F-03</td>
            </tr>
            <tr>
                <td>REVISIÓN</td>
                <td>01</td>
            </tr>
            <tr>
                <td>EMISIÓN</td>
                <td>21.JUN.21</td>
            </tr>
        </table>

    </header>

    <footer>
        <table width="100%">
            <tr>
                <td width="30%"> CONSERFLOW S.A. DE C.V. </td>
                <td width="40%">&nbsp;</td>
                <td width="30%">Página <b>1</b> de <b>1</b></td>
            </tr>
        </table>
    </footer>

    <!-- Wrap the content of your PDF inside a main tag -->
    <main>
        <div style="height: 60px;">&nbsp;</div>

        <table width="100%" class="table">

            <tr class="">
                <td rowspan="2" class="bg-gray border-all">
                    <div class="">Localidad</div>
                </td>
                <td colspan="2" rowspan="2" class="border-all">
                    <div class="">{{$gral->localidad}}</div>
                </td>
                <td></td>
                <td colspan="6" class="bg-blue border-all">
                    TIPO DE RESIDUO
                </td>
            </tr>
            <tr>
                <td colspan="1"></td>
                <td class="border-all bg-p">Plástico</td>
                <td class="border-all bg-pc">Papel y Cartón</td>
                <td class="border-all bg-m">Madera</td>
                <td class="border-all bg-v">Vidrio</td>
                <td class="border-all bg-mf">Metal Ferroso </td>
                <td class="border-all bg-mnf">Metal No Ferroso </td>
            </tr>
            <tr>
                <td colspan="10">&nbsp;</td>
            </tr>


            <tr>
                <td rowspan="2" style="padding-top: 10px; padding-bottom: 10px;" class="bg-blue border-all text-white">
                    <div">Fecha de ingreso</div>
                </td>
                <td rowspan="2" class="bg-blue border-all text-white">
                    <div>Nombre del Residuo</div>
                </td>
                <td rowspan="2" class="bg-blue border-all text-white">
                    <div>Clasificación</div>
                </td>
                <td rowspan="2" class="bg-blue border-all text-white">
                    <div>Cantidad(Kg)</div>
                </td>
                <td rowspan="2" class="bg-blue border-all text-white">
                    <div>Área o Proceso de generación</div>
                </td>
                <td rowspan="2" class="bg-blue border-all text-white">
                    <div>Fecha de salida</div>
                </td>
                <td colspan="2" class="bg-blue border-all">
                    Destino
                </td>
                <td rowspan="2" class="bg-blue border-all text-white">
                    <div>Proveedor o prestador de servicio</div>
                </td>
                <td rowspan="2" class="bg-blue border-all text-white">
                    <div>Folio</div>
                </td>
            </tr>
            <tr>

                <td style="border: 1px solid; background-color: #BFBFBF; text-align: center;">
                    <div>Reuso</div>
                </td>
                <td style="border: 1px solid; background-color: #BFBFBF; text-align: center;">
                    <div>Recicle</div>
                </td>
            </tr>
            @foreach ($bre as $key => $value)
            <tr>
                <td style="border: 1px solid; text-align: center;">&nbsp;{{$value->fecha}}</td>
                <td style="border: 1px solid; text-align: center;">&nbsp;
                    {{$value->nr}}
                </td>
                <td style="border: 1px solid; text-align: center;">&nbsp;
                    @if($value->tipo == 1)
                    RME
                    @elseif($value->tipo == 2)
                    RP
                    @endif
                </td>
                <td style="border: 1px solid; text-align: center;">&nbsp;
                    {{$value->cantidad}}
                </td>
                <td style="border: 1px solid; text-align: center;">&nbsp;
                    {{$value->area_proceso}}
                </td>
                <td style="border: 1px solid; text-align: center;">&nbsp;
                    {{$value->fecha_salida_rme}}
                </td>
                <td style="border: 1px solid; text-align: center;">&nbsp;
                    {{$value->destino==1?"X":""}}
                </td>
                <td style="border: 1px solid; text-align: center;">&nbsp;
                    {{$value->destino==2?"X":""}}
                </td>
                <td style="border: 1px solid; text-align: center;">&nbsp;
                    {{$value->proveedor}}
                </td>
                <td style="border: 1px solid; text-align: center;">&nbsp;
                    {{$gral->folio}}
                </td>
            </tr>
            @endforeach

            <tr>
                <td colspan="10">&nbsp;</td>
            </tr><tr>
                <td colspan="10">&nbsp;</td>
            </tr>
            <tr>
                <td colspan="10">&nbsp;</td>
            </tr>
            <tr>
                <td colspan="8">&nbsp;</td>
                <td colspan="2" style="text-align: center">&nbsp;{{$gral->responsable_nombre}}</td>
            </tr>
            <tr>
                <td colspan="8">&nbsp;</td>
                <td colspan="2" style="border-top: 1px solid; text-align: center;">
                    Nombre y Firma del Responsable
                </td>
            </tr>
        </table>





    </main>
</body>

</html>