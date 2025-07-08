<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Incapacidad</title>
</head>

<style>
    .bg-blue {
        background-color: red;
    }
</style>

<body>
    <table class="table bg-success" style="text-transform: uppercase;">
        <tr>
            <td colspan="26" style="font-weight: bold;">CONSERFLOW S.A. DE C.V.</td>
        </tr>
        <tr>
            <td>&nbsp;</td>
        </tr>
        <tr>
            <td></td>
            <td></td>
            <td colspan="7" style="font-weight: bold; font-size: 12;">Incapacidad</td>
        </tr>
        <tr>
            <td>&nbsp;</td>
        </tr>
        <tr>
            <td>&nbsp;</td>
        </tr>
        <tr>
            <td class="bg-blue" style="background-color: red;" rowspan="3">No.</td>
            <td rowspan="3">Nombre</td>
            <td rowspan="3">Días Totales</td>
            <td rowspan="3">Fecha de Inicio</td>
            <td rowspan="3">Fecha de Término</td>
            <td colspan="15">Incapacidad IMSS</td>
            <td colspan="2">Incapacidad Int.</td>
            <td rowspan="3">Departamento</td>
            <td rowspan="3">Puesto</td>
            <td rowspan="3">Causa</td>
            <td rowspan="3">Estado</td>
        </tr>
        <tr>
            <td colspan="10">Subsecuente</td>
            <td colspan="2">Riesgo de Trabajo en el Mes</td>
            <td rowspan="2">Enf. Gral</td>
            <td rowspan="2">Maternidad</td>
            <td rowspan="2">Paternidad</td>
            <td rowspan="2">Accidente Menor</td>
            <td rowspan="2">Fallecimiento Fam.</td>
        </tr>
        <tr>
            <td>Primera</td>
            <td>2A</td>
            <td>3RA</td>
            <td>4TA</td>
            <td>5TA</td>
            <td>6TA</td>
            <td>7MA</td>
            <td>8VA</td>
            <td>9NA</td>
            <td>10MA</td>
            <td>De trabajo</td>
            <td>Trayecto</td>
        </tr>
        @foreach($incapacidad as $n=>$i)
        <tr>
            <td>{{$n+1}}</td>
            <td>{{$i->empleado}}</td>
            <td>{{$i->total_dias}}</td>
            <td>{{$i->fecha_inicio}}</td>
            <td>{{$i->fecha_termino}}</td>
            <td>{{$i->subsecuente==1?"X":""}}</td>
            <td>{{$i->subsecuente==2?"X":""}}</td>
            <td>{{$i->subsecuente==3?"X":""}}</td>
            <td>{{$i->subsecuente==4?"X":""}}</td>
            <td>{{$i->subsecuente==5?"X":""}}</td>
            <td>{{$i->subsecuente==6?"X":""}}</td>
            <td>{{$i->subsecuente==7?"X":""}}</td>
            <td>{{$i->subsecuente==8?"X":""}}</td>
            <td>{{$i->subsecuente==9?"X":""}}</td>
            <td>{{$i->subsecuente==10?"X":""}}</td>
            <td>{{$i->tipo==2?"X":""}}</td>
            <td>{{$i->tipo==7?"X":""}}</td>
            <td>{{$i->tipo==3?"X":""}}</td>
            <td>{{$i->tipo==5?"X":""}}</td>
            <td>{{$i->tipo==6?"X":""}}</td>
            <td>{{$i->tipo==1?"X":""}}</td>
            <td>{{$i->tipo==4?"X":""}}</td>
            <td>{{$i->departamento}}</td>
            <td>{{$i->puesto}}</td>
            <td>{{$i->causa}}</td>
            <td>{{$i->estado}}</td>
        </tr>
        @endforeach
    </table>
</body>

</html>