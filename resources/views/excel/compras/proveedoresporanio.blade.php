<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Reporte de proveedores</title>
</head>

<body>

    <table class="borde">
        <tr>
            <td class="borde" colspan="15" style="color: #0070c0;">
                <b>CONSERFLOW S.A. DE C.V.</b>
            </td>
        </tr>
        <tr>
            <td></td>
        </tr>
        <tr>
            <td class="borde" colspan="2">
                <b>COMPRAS A PROVEEDORES - {{$anio}}</b>
            </td>
        </tr>
        <tr>
            <td></td>
        </tr>
        <thead>
            <tr>
                <th style="background-color : #0070C0; color:white" width="40"><b>Razón Social</b></th>
                <th style="background-color : #0070C0; color:white" width="10"><b>Total OCS</b></th>

        </thead>
        <tbody>
            @foreach($proveedores as $proveedor)
            <tr>
                <td>{{$proveedor->razon_social}}</td>
                <td>{{$proveedor->total_ocs}}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

</body>

</html>