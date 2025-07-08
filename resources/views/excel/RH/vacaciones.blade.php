<table>
    <tr>
        <td></td>
    </tr>
    <tr>
        <td></td>
        <td colspan="10" style="background-color: #0070c0;">VACACIONES DEL PERSONAL</td>
    </tr>
    <tr>
        <td></td>
    </tr>
    <tr>
        <td></td>
    </tr>
    <tr>
        <td style="background-color: #0070c0;">NOMBRE</td>
        <td style="background-color: #0070c0;">PUESTO</td>
        <td style="background-color: #0070c0;">FECHA DE INGRESO</td>
        <td style="background-color: #0070c0;">DIAS DE VACACIONES</td>
        <td style="background-color: #0070c0;">DIAS SOLICITADOS</td>
        <td style="background-color: #0070c0;">FECHA SOLICITADA</td>
        <td style="background-color: #0070c0;">DIAS DISPONIBLES</td>
        <td style="background-color: #0070c0;">SD</td>
        <td style="background-color: #0070c0;">MONTO POR VACACIONES</td>
        <td style="background-color: #0070c0;">PRIMA VACACIONAL</td>
        <td style="background-color: #0070c0;">MONTO PAGADO</td>
    </tr>
    @foreach($empleados as $e)
    <tr>
        <td>{{$e->empleado}}</td>
        <td>{{$e->puesto}}</td>
        <td>{{$e->fecha_ingreso}}</td>
        <td>{{$e->dias_ganados}}</td>
        <td>{{$e->dias_a_tomar}}</td>
        <td>{{$e->fecha_inicio}}</td>
        <td>{{$e->dias_disponibles}}</td>
        <td>{{$e->sueldo_diario_integral}}</td>
        <td>{{$e->monto_vacaciones}}</td>
        <td>{{$e->monto_vacaciones*0.25}}</td>
        <td></td>
    </tr>
    @endforeach
</table>