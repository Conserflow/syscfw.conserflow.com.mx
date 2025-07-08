<table>
    <tr>
        <td colspan="10" style="text-align : center;">CONSERFLOW S.A. DE C.V.</td>
    </tr>
    <tr>
        <th style="background-color : #0070c0; text-align : center;"><b>No.</b></th>
        <th style="background-color : #0070c0; text-align : center;"><b>Tipo</b></th>
        <th style="background-color : #0070c0; text-align : center;"><b>Oc Folio</b></th>
        <th style="background-color : #0070c0; text-align : center;"><b>Proyecto Origen</b></th>
        <th style="background-color : #0070c0; text-align : center;"><b>Folio Salida</b></th>
        <th style="background-color : #0070c0; text-align : center;"><b>Proyecto Salida</b></th>
        <th style="background-color : #0070c0; text-align : center;"><b>Empleado Solicita</b></th>
        <th style="background-color : #0070c0; text-align : center;"><b>Fecha</b></th>
        <th style="background-color : #0070c0; text-align : center;"><b>Descripción</b></th>
        <th style="background-color : #0070c0; text-align : center;"><b>Código barras</b></th>
        <th style="background-color : #0070c0; text-align : center;"><b>Cantidad</b></th>
        <th style="background-color : #0070c0; text-align : center;"><b>Unidad</b></th>
        <th style="background-color : #0070c0; text-align : center;"><b>P.U.</b></th>
        <th style="background-color : #0070c0; text-align : center;"><b>Total</b></th>
    </tr>

    <tbody>
        @foreach($salidas as $i=> $articulo)
        <tr>
            <td>{{$i+1}}</td>
            <td>{{$articulo->tipo}}</td>
            <td>{{$articulo->oc_folio}}</td>
            <td>{{$articulo->nombre_corto}}</td>
            <td>{{$articulo->folio}}</td>
            <td>{{$articulo->p_salida}}</td>
            <td>{{$articulo->empleado_solicita}}</td>
            <td>{{$articulo->s_fecha}}</td>
            <td>{{$articulo->desc}}</td>
            <td>{{$articulo->codigo_barras}}</td>
            <td>{{$articulo->cantidad_salida}}</td>
            <td>{{$articulo->unidad}}</td>
            <td>{{$articulo->precio_unitario}}</td>
            <td>{{number_format($articulo->precio_unitario*$articulo->cantidad_salida,2)}}</td>
        </tr>
        @endforeach
    </tbody>
</table>