<table>
    <tr>
        <td>Proyecto</td>
        <td>Personal Recibe</td>
        <td>Fecha</td>
        <td>Equipo</td>
        <td>Motivo</td>
        <td>Observaciones</td>
    </tr>

    @foreach($equipos as $e)
    <tr>
        <td>{{$e->proyecto}}</td>
        <td>{{$e->empleado}}</td>
        <td>{{$e->fecha_entrega}}</td>
        <td>{{$e->equipo}}</td>
        <td>
            {{$e->motivo==1?"Cambio por desgaste":
                ($e->motivo==2?"Cambio por extravío":"Personal de nuevo ingreso")}}
        </td>
        <td>{{$e->observaciones}}</td>
    </tr>
    @endforeach
</table>