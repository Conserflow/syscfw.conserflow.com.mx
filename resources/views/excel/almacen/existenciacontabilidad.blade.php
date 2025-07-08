<table>
    <tr>
        <td colspan="9" style="text-align : center;">CONSERFLOW S.A. DE C.V.</td>
    </tr>
    <tr>
        <td colspan="5"></td>
        <td colspan="2" style="background-color : #8E8F93; text-align : center;">Fecha del Reporte</td>
        <td style="text-align : center;">
            <b>{{$mes_letra}} {{$anio}}</b>
        </td>
    </tr>
    <tr>
        <th style="background-color : #0070c0; text-align : center;"><b>Categoria</b></th>
        <th width="80" style="background-color : #0070c0; text-align : justify;"><b>Artículo</b></th>
        <th style="background-color : #0070c0; text-align : center;"><b>Unidad</b></th>
        <th style="background-color : #0070c0; text-align : center;"><b>Precio Unitario</b></th>
        <th style="background-color : #0070c0; text-align : center;" width="30"><b>Proyecto</b></th>
        <th style="background-color : #0070c0; text-align : center;"><b>Entradas</b></th>
        <th style="background-color : #0070c0; text-align : center;"><b>Fecha Entrada</b></th>
        <th style="background-color : #0070c0; text-align : center;"><b>Lote</b></th>
    </tr>

    @foreach($entradas as $a)
    <tr>
        <td>{{$a->g_nombre}}</td>
        <td>{{$a->a_descripcion}}</td>
        <td>{{$a->a_um}}</td>
        <td>{{$a->pu}}</td>
        <td>{{$a->p_nombre}}</td>
        <td>{{$a->pe_cantidad}}</td>
        <td>{{$a->e_fecha}}</td>
        <td>{{$a->la_codigo_barras}}</td>
    </tr>
    @endforeach
</table>