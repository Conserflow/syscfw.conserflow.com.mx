<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Equipos en Resguardo</title>
</head>

<body>
    <table>
        <tr><td></td></tr>
        <tr><td></td></tr>
        <tr><td></td></tr>
        <tr><td></td></tr>
        <tr>
            <td style="background-color: #0070c0; color: white;">No.</td>
            <td style="background-color: #0070c0; color: white;">Fecha</td>
            <td style="background-color: #0070c0; color: white;">Tipo</td>
            <td style="background-color: #0070c0; color: white;">Equipo</td>
            <td style="background-color: #0070c0; color: white;">Empresa</td>
            <td style="background-color: #0070c0; color: white;">Empleado</td>
            <td style="background-color: #0070c0; color: white;">Observaciones</td>
        </tr>
        @foreach($list_resguardos as $i=>$r)
        <tr>
            <td>{{$i+1}}</td>
            <td>{{$r->fecha}}</td>
            <td>{{$r->tipo==1?'COMPUTO':($r->tipo==2?'ACCESORIOS':($r->tipo==3?'IMPRESION':'VIDEO'))}}</td>
            <td>{{$r->descripcion}}</td>
            <td>{{$r->empresa==1?"CONSERFLOW":"CSCT"}}</td>
            <td>{{$r->empleado_r}}</td>
            <td>{{$r->observacion_uno}}</td>
        </tr>
        @endforeach
    </table>
</body>

</html>