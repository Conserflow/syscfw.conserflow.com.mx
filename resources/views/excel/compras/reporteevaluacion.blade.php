<table>
    <tr>
        <td colspan="2" style="font-weight: bold; font-size: 14; text-align: center;">
            Evaluacion {{$anio}}
        </td>
    </tr>
    <tr>
        <td></td>
    </tr>
    <tr>
        <td style="background-color: #0070c0;color:white; font-weight: bold;">Proveedor</td>
        <td style="background-color: #0070c0;color:white; font-weight: bold;">Total Evaluación</td>
    </tr>
    @foreach($proveedores as $p)
    <tr>
        <td>{{$p["razon_social"]}}</td>
        @if($p["total_evaluacion"]>=54)
        <td>APROBADO</td>
        @elseif($p["total_evaluacion"]>=36 && $p["total_evaluacion"]<=53) <td>CONDICIONADO</td>
            @elseif($p["total_evaluacion"]>0 && $p["total_evaluacion"]<=35) <td>NO APTO</td>
                @else
                <td>N/D</td>
                @endif
    </tr>
    @endforeach
</table>