<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>PMN-02-F_06 MANTENIMIENTO VEHICULAR</title>
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
            background-color: #0070C0;
            font-weight: bold;
        }

        .borcd {
            border: 1px solid;
            text-align: center;
            background-color: #B2DE82;
            font-weight: bold;
        }

        .borl {
            border: 1px solid;
        }

        .table {
            border-collapse: collapse;
            border: 1px solid black;
            font-family: Arial, Helvetica, sans-serif;
            font-size: 10px;
            text-align: center;
        }

        .table-border tr td {
            border: 1px solid black;
        }

        .tab {
            border-collapse: collapse;
            font-family: Arial, Helvetica, sans-serif;
            font-size: 12px;
            text-align: center;
            border: 2px solid;
        }

        .gr {
            border: 1px solid;
            background-color: #bfbfbf;
        }

        .bg-azul {
            font-weight: bold;
            text-transform: uppercase;
            background-color: #0082B0;
            color: white;
        }

        .bg-gris {
            font-weight: bold;
            background-color: #bfbfbf;
        }

        .central {
            border-bottom: none;
            border-top: none;
        }
    </style>

</head>

<body>
    <header>

        <table width="100%" class="table">
            <tr>
                <td colspan="4" style="border: 1px solid; ">
                    <div style="color: #0070C0;">
                        <b>
                            CONSERFLOW S.A. DE C.V.
                        </b>
                    </div>
                </td>
            </tr>
            <tr>
                <td rowspan="3" width="20%"><img src="img/conserflow.png" width="120"></td>
                <td rowspan="3" style="border-left: 1px solid; border-bottom: 0px solid white;" width="50%">
                    <b>MANTENIMIENTO VEHICULAR</b>
                </td>
                <td class="borc" width="15%">
                    <div> CÓDIGO</div>
                </td>
                <td class="borc" width="15%">
                    <div>PMN-02/F-03</div>
                </td>
            </tr>
            <tr>
                <td class="borc">
                    <div>REVISIÓN</div>
                </td>
                <td class="borc">
                    <div>{{ $revision }}</div>
                </td>
            </tr>
            <tr>
                <td class="borc">
                    <div>EMISIÓN</div>
                </td>
                <td class="borc">
                    <div>{{ $emision }}</div>
                </td>
            </tr>
        </table>

    </header>

    <!-- Wrap the content of your PDF inside a main tag -->
    <main>
        <table class="table table-border" width="100%">
            <tr>
                <td class="bg-azul" colspan="9">
                    <div>DATOS DEL VEHÍCULO</div>
                </td>
            </tr>
            <tr>
                <td class="bg-gris">Marca</td>
                <td>{{ $mantenimiento->marca }}</td>
                <td class="bg-gris" colspan="2">Modelo</td>
                <td colspan="3">{{ $mantenimiento->modelo }}</td>
                <td class="bg-gris">Color</td>
                <td>{{ $mantenimiento->color }}</td>
            </tr>
            <tr>
                <td class="bg-gris">Placas</td>
                <td width="15%">{{ $mantenimiento->placas }}</td>
                <td class="bg-gris" colspan="2">No. Serie</td>
                <td colspan="3">{{ $mantenimiento->numero_serie }}</td>
                <td class="bg-gris">No. Motor</td>
                <td>{{ $mantenimiento->no_motor }}</td>
            </tr>
        </table>
        <table style="margin-top: -1px; border-top: none;" width="100%" class="table table-border">
            <tr>
                <td class="bg-gris" colspan="2">Capacidad de carga</td>
                <td colspan="2">{{ $mantenimiento->capacidad }}</td>
                <td style="border-bottom: none;" width="1%"></td>
                <td class="bg-gris" colspan="2">Cilindros</td>
                <td colspan="2">{{ $mantenimiento->cilindros }}</td>
            </tr>
            <tr>
                <td class="bg-gris" colspan="2">Póliza de Seguro</td>
                <td colspan="2">{{ $mantenimiento->numero_poliza }}</td>
                <td style="border-top: none;" width="1%"></td>
                <td class="bg-gris" colspan="2">Tarjeta de Circulación</td>
                <td colspan="2">{{ $mantenimiento->numero_tarjeta_circulacion }}</td>
            </tr>
            <tr>
                <td colspan="9">
                    <div></div>
                </td>
            </tr>
            <tr>
                <td class="bg-azul" colspan="9">Datos del mantenimiento</td>
            </tr>
            <tr>
                <td colspan="3" class="bg-gris">Mantenimiento Preventivo</td>
                <td widht="10%">{{ $mantenimiento->tipo == 1 ? 'X' : '' }}</td>
                <td style="border-top: none; border-bottom: none;" width="1%"></td>
                <td colspan="3" class="bg-gris">Mantenimiento Correctivo</td>
                <td widht="10%">{{ $mantenimiento->tipo == 2 ? 'X' : '' }}</td>
            </tr>
            <tr>
                <td colspan="4" class="bg-gris">Descripción del servicio a realizar</td>
                <td style="border-top: none; border-bottom: none;" width="1%"></td>
                <td colspan="4" class="bg-gris">Descripción del daño, falla o avería detectada</td>
            </tr>
            <tr>
                <td colspan="4">
                    <br>
                    <p>{{ $mantenimiento->tipo == 1 ? $mantenimiento->descripcion : '' }}</p>
                    <br>
                    <br>
                </td>
                <td style="border-top: none; border-bottom: none;" width="1%"></td>
                <td colspan="4">
                    <br>
                    <p>{{ $mantenimiento->tipo == 2 ? $mantenimiento->descripcion : '' }}</p>
                    <br>
                    <br>
                </td>
            </tr>
            <tr>
                <td class="bg-gris" colspan="3">
                    Nombre de quién solicita el mantenimiento
                </td>
                <td colspan="6">
                    {{ $mantenimiento->empleado_solicita }}
                </td>
            </tr>
            <tr>
                <td class="bg-gris" colspan="3">
                    Nombre de quién recibe la solicitud de mantenimiento
                </td>
                <td colspan="6">
                    {{ $mantenimiento->empleado_recibe }}
                </td>
            </tr>
            <tr>
                <td colspan="2" class="bg-gris">Fecha de inicio</td>
                <td colspan="2" widht="10%">{{ $mantenimiento->fecha_inicio }}</td>
                <td colspan="3" class="bg-gris">Fecha de salida</td>
                <td colspan="2" widht="10%">{{ $mantenimiento->fecha_salida }}</td>
            </tr>
            <tr>
                <td colspan="2" class="bg-gris">Proveedor asignado</td>
                <td colspan="7" widht="10%">{{ $mantenimiento->proveedor }}</td>
            </tr>
            @if ($mantenimiento->fecha_inicio < '2023-04-28')
                <tr>
                    <td colspan="9" class="bg-azul">Detalles del servicio efectuado</td>
                </tr>
                <tr>
                    <td colspan="9">
                        <br>
                        {{ $mantenimiento->detalle }}
                        <br>
                        <br>
                    </td>
                </tr>
            @endif
            <tr>
                <td colspan="9" class="bg-azul">Observaciones</td>
            </tr>
            <tr>
                <td colspan="9">
                    <br>
                    <br>
                    <br>
                </td>
            </tr>
            <tr>
                <td colspan="4" class="bg-azul">REFACCIONES Y MATERIALES EMPLEADOS</td>
                <td style="border-top: none; border-bottom: none;" width="1%"></td>
                <td colspan="4" class="bg-azul">QUIMICOS EMPLEADOS</td>
            </tr>
            <tr>
                <td colspan="4" style="border-top: none; border-bottom: none;">
                    <br>
                    {{ $mantenimiento->materiales }}
                    <br>
                    <br>
                </td>
                <td style=" border-top: none; border-bottom: none;" width="1%"></td>
                <td colspan="4" style="border-top: none; border-bottom: none;">
                    <br>

                    {{ $mantenimiento->quimicos }}
                    <br>
                    <br>
                </td>
            </tr>

            <tr>
                <td colspan=" 4" style="border-right: none;">
                    <br>
                    Elaboró
                    <br>
                    <br>
                    @if (file_exists('administrativos/' . $mantenimiento->empleado_entrega_id . '.png'))
                        <img src="administrativos/{{ $mantenimiento->empleado_entrega_id }}.png" width="120px">
                    @else
                        <img src="administrativos/0.png" width="120px">
                    @endif
                    <br>
                    {{ $mantenimiento->empleado_entrega }}
                    <br>
                    <span style="text-decoration: overline;">
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        Nombre
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    </span>
                </td>
                <td style="border: none;" width="1%"></td>
                <td colspan="4" style="border-left: none;">
                    <br>
                    Recibió vehículo
                    <br>
                    <br>
                    @if (file_exists('administrativos/' . $mantenimiento->empleado_recibe_id . '.png'))
                        <img src="administrativos/{{ $mantenimiento->empleado_recibe_id }}.png" width="120px">
                    @else
                        <img src="administrativos/0.png" width="120px">
                    @endif
                    <br>
                    {{ $mantenimiento->empleado_recibe }}
                    <br>
                    <span style="text-decoration: overline;">
                        &nbsp;&nbsp;&nbsp;
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        Nombre&nbsp;&nbsp;&nbsp;
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    </span>
                    <br>
                    <br>
                </td>
            </tr>
        </table>

    </main>
    <script type="text/php">
        if (isset($pdf)) {
        $text = "PAGINA {PAGE_NUM} DE {PAGE_COUNT}";
        $size = 12;
        $font = $fontMetrics->getFont("Verdana");
        $width = $fontMetrics->get_text_width($text, $font, $size) / 2;
        $x = ($pdf->get_width() - $width) / 1;
        $y = $pdf->get_height() - 35;
        $pdf->page_text($x, $y, $text, $font, $size);
    }
</script>
</body>

</html>
