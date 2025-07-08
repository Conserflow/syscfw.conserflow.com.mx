<table>
  <tr>
    <th rowspan="2" style="background-color: #0070c0;">NOMBRE</th>
    <th rowspan="2" style="background-color: #0070c0;">CATEGORIA</th>
    <!-- Nombre del día -->
    @foreach($dias as $nombre)
    <th style="background-color: #0070c0;">{{$nombre}}</th>
    <th style="background-color: #0070c0;"></th><!-- ubicacion -->
    @endforeach
  </tr>
  <tr>
    <!-- Fecha -->
    @foreach($fechas_asistencia as $fecha)
    <th style="background-color: #0070c0;">{{$fecha}}</th>
    <th style="background-color: #0070c0;"></th><!-- ubicacion -->
    @endforeach
  </tr>

  @foreach ($asistencias_array as $a)
  <tr>
    <td>{{$a['empleado']->empleado}}</td>
    <td>{{$a['empleado']->puesto}}</td>
    @foreach ($a['registros'] as $a_)
    <td>
      @if(count($a_["asistencias"]) != 0)
      @foreach ($a_["asistencias"] as $v)
      {{$v->hora.'-'}}
      @endforeach
      @else
      -
      @endif
    </td>
    <td> {{$a_["ubicaciones"]}}</td>
    @endforeach
  </tr>
  <tr>
  </tr>
  @endforeach
</table>