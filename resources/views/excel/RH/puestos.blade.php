<tr>
    <td style="background-color: #0070c0">Puesto</td>
    <td style="background-color: #0070c0">Area</td>
    <td style="background-color: #0070c0">Departamento</td>
    <td style="background-color: #0070c0">Dirección</td>
</tr>

@foreach ($puestos as $p)
    <tr>
        <td>{{ $p->puesto }}</td>
        <td>{{ $p->area }}</td>
        <td>{{ $p->departamento }}</td>
        <td>{{ $p->direccion }}</td>
    </tr>
@endforeach
