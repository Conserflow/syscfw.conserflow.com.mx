{{-- Encabezado --}}
<table>
    <tr>
        <td colspan="8">
            CONSERFLOW S.A. DE C.V.
        </td>
    </tr>
    <tr>
        <td colspan="3" rowspan="3"></td> {{-- Logo --}}
        <td colspan="3" rowspan="3">REQUISICIÓN DE COMPRA</td>
        <td>CÓDIGO</td>
        <td>PCO-01/F-01</td>
    </tr>
    <tr>
        <td>REVISIÓN</td>
        <td>01</td>
    </tr>
    <tr>
        <td>EMISIÓN</td>
        <td>30.SEP.23</td>
    </tr>
</table>

<table>
    <tr>
        <td colspan="8">DIRECCIÓN:Calle Mezquite # 5, Colonia Santa Clara. Parque Industrial Tehuacan-Miahuatlán,
            Santiago Miahuatlán, Puebla, México C.P. 75820.</td>
    </tr>
</table>

{{-- Datos --}}
<table>
    <tr>
        <td colspan="2">NOMBRE PROYECTO:</td>
        <td colspan="3">{{ $requisicion->proyecto->nombre_corto }}</td>
        <td></td> {{-- Espacio --}}
        <td>FECHA DE EMISIÓN:</td>
        <td>{{ $requisicion->fecha_emision }}</td>
    </tr>
    <tr>
        <td colspan="2">NUMERO DE REQUISICIÓN (RQ):</td>
        <td colspan="3">{{ $requisicion->folio }}</td>
        <td></td> {{-- Espacio --}}
        <td>REVISIÓN:</td>
        <td>{{ $requisicion->revision }}</td>
    </tr>
    <tr>
        <td colspan="2">ÁREA SOLICITANTE:</td>
        <td colspan="3">{{ $requisicion->area->nombre }}</td>
        <td></td> {{-- Espacio --}}
        <td>FECHA DE ENTREGA REQUERIDA:</td>
        <td>{{ $requisicion->fecha_entrega }}</td>
    </tr>
    <tr>
        <td colspan="2">TIPO DE MATERIAL/SERVICIO :</td>
        <td colspan="3">{{ $requisicion->tipo->nombre }}</td>
        <td></td> {{-- Espacio --}}
        <td>LUGAR DE ENTREGA:</td>
        <td>{{ $requisicion->lugar_entrega }}</td>
    </tr>
</table>

{{-- Partidas --}}
<table>
    <tr>
        <td></td>
        <td colspan="3">Cantidades</td>
    </tr>
    <tr>
        <td>Partida</td>
        <td>Requerida</td>
        <td>Almacen</td>
        <td>Compras</td>
        <td>Unidad</td>
        <td>Descripción</td>
        <td>Marca</td>
        <td>Documento Requerido</td>
    </tr>
    @foreach ($partidas as $i => $partida)
        <tr>
            <td>{{ $i + 1 }}</td>
            <td>{{ $partida->cantidad }}</td>
            <td></td>
            <td></td>
            <td>{{ $partida->unidadMedida->nombre }}</td>
            <td>{{ $partida->concepto }}</td>
            <td>{{ $partida->marca }}</td>
            <td>{{ $partida->documentos_requeridos }}</td>
        </tr>
    @endforeach
</table>

{{-- Notas --}}
<table>
    <tr>
        <td colspan="8">NOTAS U OBSERVACIONES</td>
    </tr>
    <tr>
        <td colspan="8">* Indicar si las marcas son obligatorias o sugeridas</td>
    </tr>
    <tr>
        {{-- <td colspan="5"></td> --}}
        <td colspan="8">{!! nl2br($requisicion->notas) !!}</td>
        {{-- <td colspan="2"></td> --}}
    </tr>
</table>

{{-- Firmas --}}
<table>
    <tr>
        <td colspan="6"></td> {{-- Espacio --}}
        <td>SOLICITÓ:</td>
        <td>{{ $requisicion->solicita->nombre }}</td>
    </tr>
    <tr>
        <td colspan="6"></td> {{-- Espacio --}}
        <td>REVISÓ:</td>
        <td></td> {{-- Almacen --}}
    </tr>
    <tr>
        <td colspan="6"></td> {{-- Espacio --}}
        <td>APROBÓ:</td>
        <td>{{ $requisicion->aprueba->nombre }}</td>
    </tr>
</table>
