<table>
    <tr>
        <td style="font-weight: bold; background-color: #0070c0; color: white;">Proyecto</td>
        <td style="font-weight: bold; background-color: #0070c0; color: white;">Folio</td>
        <td style="font-weight: bold; background-color: #0070c0; color: white;">Nombre</td>
        <td style="font-weight: bold; background-color: #0070c0; color: white;">Descripción</td>
        <td style="font-weight: bold; background-color: #0070c0; color: white;">Cantidad</td>
        <td style="font-weight: bold; background-color: #0070c0; color: white;">Proveedor</td>
        <td style="font-weight: bold; background-color: #0070c0; color: white;">Fecha</td>
    </tr>
    @foreach ($entradas as $e)
    <tr>
        <td>{{$e->proyecto}}</td>
        <td>{{$e->folio}}</td>
        <td>{{$e->nombre}}</td>
        <td>{{$e->descripcion}}</td>
        <td>{{$e->cantidad}}</td>
        <td>{{$e->proveedor}}</td>
        <td>{{$e->fecha}}</td>
    </tr>
    @endforeach
</table>