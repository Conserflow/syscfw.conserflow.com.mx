<tr>
    <td style="background-color: #0070c0">Nombre</td>
    <td style="background-color: #0070c0">Apellido P</td>
    <td style="background-color: #0070c0">Apellido M</td>
    <td style="background-color: #0070c0">Banco</td>
    <td style="background-color: #0070c0">Cuenta</td>
    <td style="background-color: #0070c0">Clabe</td>
    <td style="background-color: #0070c0">Tarjeta</td>
</tr>

@foreach($empleados as $e)
<tr>
    <td>{{$e->nombre}}</td>
    <td>{{$e->ap_paterno}}</td>
    <td>{{$e->ap_materno}}</td>
    <td>{{$e->banco}}</td>
    <td>{{$e->cuenta}}</td>
    <td>{{$e->clabe}}</td>
    <td>{{$e->tarjeta}}</td>
</tr>
@endforeach