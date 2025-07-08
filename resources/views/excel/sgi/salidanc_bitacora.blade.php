<table>
    <tr>
        <td colspan="12" style="color: #0070c0;">CONSERFLOW S.A. DE C.V.</td>
    </tr>

    <tr>
        <td rowspan="3"></td> <!-- imagen -->
        <td rowspan="3" colspan="9">BITÁCORA DE SALIDAS NO CONFORMES</td>
        <td colspan="1" style="text-align : center;">
            CÓDIGO
        </td>
        <td colspan="1" style="text-align : center;">PCC-14/F-02</td>
    </tr>
    <tr>
        <td colspan="1" style="text-align : center;">REVISIÓN</td>
        <td colspan="1" style="text-align : center;">01</td>
    </tr>
    <tr>
        <td colspan="1" style="text-align : center;">EMISIÓN</td>
        <td colspan="1" style="text-align : center;">13.JUN.22</td>
    </tr>

    <tr>
        <td colspan="12"></td>
    </tr>
    <tr>
        <td>No. de Reporte</td>
        <td>Detectado por</td>
        <td>Fecha de detección</td>
        <td>Proyecto/ Servicio</td>
        <td>Nombre del cliente, proveedor o proceso</td>
        <td>Número de comunicado</td>
        <td>Orden de Compra</td>
        <td>Descripción de la Salida No Conforme</td>
        <td>Tratamiento</td>
        <td>Fecha de Verificación</td>
        <td>Resultado de las actividades</td>
        <td>Número de acción correctiva (si lo requiere)</td>
    </tr>
    @foreach ($salidas as $salida)
        <tr>
            <td>{{ $salida->folio }}</td>
            <td>{{ $salida->empleadoDetecta->nombre }}</td>
            <td>{{ $salida->fecha_deteccion }}</td>
            <td>{{ $salida->proyecto->nombre_corto }}</td>
            <td>{{ $salida->cliente_proveedor }}</td>
            <td>{{ $salida->no_comunicado }}</td>
            <td>{{ $salida->no_oc }}</td>
            <td>{{ $salida->descripcion }}</td>
            @switch($salida->tratamiento)
                @case(1)
                    <td>Corrección</td>
                @break

                @case(2)
                    <td>Separación</td>
                @break

                @case(3)
                    <td>Contención</td>
                @break

                @case(4)
                    <td>Contención</td>
                @break

                @case(5)
                    <td>Devolución</td>
                @break

                @case(6)
                    <td>Suspensión</td>
                @break

                @case(7)
                    <td>Desecho</td>
                @break

                @case(8)
                    <td>Información al cliente</td>
                @break

                @case(9)
                    <td>{{ $salida->tratamiento_otro }}</td>
                @break
            @endswitch
            <td>{{ $salida->fecha_verificacion }}</td>
            <td>{{ $salida->resultado }}</td>
            <td>{{ $salida->require_correccion == 1 ? $salida->no_accion_correctiva : 'N/A' }}
            </td>
        </tr>
    @endforeach
</table>
