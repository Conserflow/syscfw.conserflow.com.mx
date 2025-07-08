<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>asd</title>
    <link rel="stylesheet" href="css/tablas.css">
</head>

<body>
    <table>
        <tr>
            <td colspan="13"><b>CONSERFLOW S.A. DE C.V.</b></td>
        </tr>
        <tr>
            <td rowspan="3" colspan="2"></td> <!-- imagen -->
            <td colspan="9" rowspan="3">
                <p class="th">CONTROL DE CALIBRACIÓN DE EQUIPOS <br>
                    <span>EQUIPMENT CALIBRATION CONTROL</span>
                </p>
            </td>
            <td><b>CÓDIGO</b></td>
            <td>PCC-03/F-01</td>
        </tr>
        <tr>
            <td>
                <p><b>REVISIÓN</b></p>
            </td>
            <td>01</td>
        </tr>
        <tr>
            <td>
                <p><b>EMISIÓN</b></p>
            </td>
            <td>
                <p>12.ABR.23</p>
            </td>
        </tr>
        <tr>
            <td colspan="13"></td>
        </tr>
        <tr>
            <td colspan="10">
            </td>
            <td colspan="2">
                <p class="td">FECHA DE ACTUALIZACIÓN: <br><span>Date</span></p>
            </td>
            <td>14-jun-2023</td>
        </tr>
        <tr>
            <td colspan="13"></td>
        </tr>
        <!-- encabezado -->
        <tr>
            <td rowspan="2">
                <p class="th">No.</p>
            </td>
            <td rowspan="2" class="th" style="font-weight: bold">
                <span>Equipo a calibrar</span> <br>
                <span>Equipment to calibrate</span>
            </td>
            <td rowspan="2">
                <span>Marca</span><br><span>Brand</span>
            </td>
            <td rowspan="2">
                <p class="th">Modelo <br><span>Model</span></p>
            </td>
            <td rowspan="2">
                <p class="th">Rango de Medición <br><span>Metering range</span></p>
            </td>
            <td rowspan="2">
                <p class="th">No. De serie <br><span>Serial number</span></p>
            </td>
            <td colspan="3">
                <p class="th">Calibración <br><span>Calibration</span></p>
            </td>
            <td>
                <p class="th">Estatus <br><span>Status</span></p>
            </td>
            <td rowspan="2">
                <p class="th">Resguardo<br> <span>Backup</span></p>
            </td>
            <td rowspan="2">
                <p class="th">Revisó <br><span>Revised</span></p>
            </td>
            <td rowspan="2">
                <p class="th">Observaciones <br><span>Comments</span></p>
            </td>
        </tr>
        <tr>
            <td>
                <p>Frecuencia <br><span>Frecuency</span></p>
            </td>
            <td>
                <p>Fecha del Servicio <br><span>Service date</span></p>
            </td>
            <td>
                <p>Próxima Fecha<br><span>Next date</span></p>
            </td>
            <td>
                <p>Activo / Inactivo<br><span>Active / Inactive</span></p>
            </td>
        </tr>
        {{$i=1}}
        @foreach($equipos as $clase=>$datos)
        <tr>
            <td style="background-color: #ffc000;" align="center" colspan="13"><b>{{$clase}}</b></td>
        </tr>
        @foreach($datos as $d)
        <tr>
            <td>{{$i}}</td>
            <td>{{$d->equipo}}</td>
            <td>{{$d->marca}}</td>
            <td>{{$d->modelo}}</td>
            <td>{{$d->rango_medicion}}</td>
            <td>{{$d->ns}}</td>
            <td>{{$d->frecuencia}}</td>
            <td>{{$d->fecha_servicio}}</td>
            <td>{{$d->proxima_fecha}}</td>
            @if($d->condicion)
            <td style="background-color: #00cc66;"><b>Activo</b></td>
            @else
            <td style="background-color: #ff4215;color:white"><b>Inactivo</b></td>
            @endif
            <td>{{$d->resguardo}}</td>
            <td>{{$d->revisa}}</td>
            <td>{{$d->observaciones}}</td>
        </tr>
        {{$i+=1}}
        @endforeach
        @endforeach
        <tr>
            <td></td>
        </tr>
        <tr>
            <td colspan="10"></td>
            <td colspan="2">
                <center>Areli Cruz Roque</center>
            </td>
        </tr>
        <tr>
            <td colspan="10"></td>
            <td colspan="2"><center>Supervisó</center></td>
        </tr>
    </table>
</body>

</html>