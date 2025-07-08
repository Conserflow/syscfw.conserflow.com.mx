<table>
    <tr>
        <td colspan="17">
            <p>CONSUMO DE COMBUSTIBLE Y DIESEL TEHUACAN</p>
        </td>
    </tr>
    <tr>
        <td colspan="2"></td>
        <td colspan="15"></td>
    </tr>
    <tr>
        <td colspan="17"></td>
    </tr>
    <tr>
        <td style="background-color: #0070c0;">PROVEEDOR</td>
        <td style="background-color: #0070c0;">FOLIO</td>
        <td style="background-color: #0070c0;">FECHA</td>
        <td style="background-color: #0070c0;">PROYECTO</td>
        <td style="background-color: #0070c0;">OPERADOR</td>
        <td style="background-color: #0070c0;">FACTURA</td>
        <td style="background-color: #0070c0;">PLACAS</td>
        <td style="background-color: #0070c0;">UNIDAD</td>
        <td style="background-color: #0070c0;">PRODUCTO</td>
        <td style="background-color: #0070c0;">HRS MOTOR</td>
        <td style="background-color: #0070c0;">KILOMETRAJE</td>
        <td style="background-color: #0070c0;">CANTIDAD</td>
        <td style="background-color: #0070c0;">PRECIO</td>
        <td style="background-color: #0070c0;">SUBTOTAL</td>
        <td style="background-color: #0070c0;">IVA</td>
        <td style="background-color: #0070c0;">TOTAL</td>
        <td style="background-color: #0070c0;">ACUMULADO</td>
    </tr>
    @foreach($vales as $c)
    <tr>
        <td>{{$c->razon_social}}</td>
        <td>{{$c->folio}}</td>
        <td>{{$c->fecha}}</td>
        <td>{{$c->nombre_corto==null?"CANCELADO":$c->nombre_corto}}</td>
        <td>{{$c->nombre==null?"CANCELADO":$c->nombre}}</td>
        <td>{{$c->factura}}</td>
        @if($c->unidad_id==-1)
        <td>N/D</td>
        <td>{{$c->cantidad_bidones}}</td>
        @else
        <td>{{$c->placas}}</td>
        <td>{{$c->unidad}}</td>
        @endif
        <td>
            @if($c->producto_id==1) MAGNA
            @elseif($c->producto_id==2) PREMIUM
            @elseif($c->producto_id==3)DIESEL
            @else CANCELADO
            @endif
        </td>
        <td>{{$c->horas}}</td>
        <td>{{$c->kilometraje}}</td>
        <td>{{$c->cantidad}}</td>
        <td>{{$c->precio}}</td>
        <td>{{$c->subtotal}}</td>
        <td>{{$c->iva}}</td>
        <td>{{$c->total}}</td>
        <td></td>
    </tr>
    @endforeach
    <tr>
        <td colspan="17" style="text-align: center;font-weight: bold; background-color: yellow; color: red;">
            Transferencias
        </td>
    </tr>
    @foreach($transferencias as $c)
    <tr>
        <td>{{$c->razon_social}}</td>
        <td>{{$c->folio}}</td>
        <td>{{$c->fecha}}</td>
        <td>{{$c->nombre_corto==null?"CANCELADO":$c->nombre_corto}}</td>
        <td>{{$c->nombre==null?"CANCELADO":$c->nombre}}</td>
        <td>{{$c->factura}}</td>
        @if($c->unidad_id==-1)
        <td>BIDONES</td>
        <td>{{$c->cantidad_bidones}}</td>
        @else
        <td>{{$c->placas}}</td>
        <td>{{$c->unidad}}</td>
        @endif
        <td>
            @if($c->producto_id==1) MAGNA
            @elseif($c->producto_id==2) PREMIUM
            @else DIESEL
            @endif
        </td>
        <td>{{$c->horas}}</td>
        <td>{{$c->kilometraje}}</td>
        <td>{{$c->cantidad}}</td>
        <td>{{$c->precio}}</td>
        <td>{{$c->subtotal}}</td>
        <td>{{$c->iva}}</td>
        <td>{{$c->total}}</td>
        <td></td>
    </tr>
    @endforeach

</table>