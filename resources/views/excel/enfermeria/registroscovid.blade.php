<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registros Covid</title>
</head>

<body>
    <table class="table bg-success">
        <tr>
            <td colspan="9" style="font-weight: bold;">CONSERFLOW S.A. DE C.V.</td>
        </tr>
        <tr>
            <td>&nbsp;</td>
        </tr>
        <tr>
            <td></td>
            <td></td>
            <td colspan="7" style="font-weight: bold; font-size: 12;">REGISTROS COVID</td>
        </tr>
        <tr>
            <td>&nbsp;</td>
        </tr>
        <tr>
            <td>&nbsp;</td>
        </tr>
        <tr>
            <td style="background-color: #0070c0; font-weight: bold;">Nombre</td>
            <td style="background-color: #0070c0; font-weight: bold;">Puesto</td>
            <td style="background-color: #0070c0; font-weight: bold;">Diagnóstico</td>
            <td style="background-color: #0070c0; font-weight: bold;">Inicio de Sintomas</td>
            <td style="background-color: #0070c0; font-weight: bold;">Fecha de Detección</td>
            <td style="background-color: #0070c0; font-weight: bold;">Inicio de Incapacidad/Permiso</td>
            <td style="background-color: #0070c0; font-weight: bold;">Días de Incapacidad/Aislamiento</td>
            <td style="background-color: #0070c0; font-weight: bold;">Término de Incapacidad/Permiso</td>
            <td style="background-color: #0070c0; font-weight: bold;">Prueba</td>
        </tr>
        @foreach($registros as $r)
        <tr>
            <td>{{$r->nombre}}</td>
            <td>{{$r->puesto}}</td>
            <td>{{$r->diagnostico}}</td>
            <td>{{$r->inicio_sintomas}}</td>
            <td>{{$r->fecha_deteccion}}</td>
            <td>{{$r->inicio_incapacidad}}</td>
            <td>{{$r->dias_incapacidad}}</td>
            <td>{{$r->termino_incapacidad}}</td>
            <td>{{$r->prueba}}</td>
        </tr>
        @endforeach
    </table>
</body>

</html>