<table>
    <tr>
        <td></td>
    </tr>
    <tr>
        <td></td>
    </tr>
    <tr>
        <td></td>
    </tr>
    <tr>
        <td></td>
    </tr>
    <tr>
        <td></td>
    </tr>
    <tr>
        <td>Nombre</td>
        <td>Folio</td>
        <td>PO</td>
        <td>Monto Total</td>
        <td>Ciudad</td>
        <td>Total OC</td>
        <td>Total REQ</td>
        <td>Fecha inicio</td>
        <td>Fecha Fin</td>
        <td>Elabora</td>
        <td>Fecha / Hora</td>
    </tr>
    @foreach ($proyectos as $p)
        <tr>
            <td>{{ $p->nombre_corto }}</td>
            <td>{{ $p->folio }}</td>
            <td>{{ $p->clave }}</td>
            <td>{{ $p->monto_total }}</td>
            <td>{{ $p->ciudad }}</td>
            <td>{{ $p->total_oc }}</td>
            <td>{{ $p->total_req }}</td>
            <td>{{ $p->fecha_inicio }}</td>
            <td>{{ $p->fecha_fin }}</td>
            <td>{{ $p->registra }}</td>
            <td>{{ $p->created_at2 }}</td>
        </tr>
    @endforeach
</table>
