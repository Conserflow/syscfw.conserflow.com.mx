<style>
    .unidad_table td,
    .unidad_table th {
        border: 1px solid #dddddd;
        padding: 8px;
    }

    .detalle_table td,
    .detalle_table th {
        border: 1px solid #dddddd;
    }
</style>
<table style="border: solid 1px #bbbccc; width: 700px;" cellspacing="0" cellpadding="0">
    <tbody>
        @if ($isError == true)
            <tr style="background-color: red; color: #fff;">
            @else
            <tr style="background-color: #0073b7; color: #fff;">
        @endif
        <td width="10">&nbsp;</td>
        <td align="center">
            <h1>{{ $mensaje }}</h1>
        </td>
        <td width="10px">&nbsp;</td>
        </tr>
        <tr>
            <td colspan="4">
                <table width="100%" cellspacing="0" cellpadding="0">
                    <tbody>
                        <tr>
                            <td colspan="3">&nbsp;</td>
                        </tr>
                        @foreach ($detalle as $renglon)
                            <tr>
                                <td colspan="3">
                                    <table width="100%" class="unidad_table">
                                        <tr>
                                            @if (isset($renglon['mensaje']))
                                                <td>{!! $renglon['mensaje'] !!}</td>
                                            @endif
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        @endforeach
                        <tr>
                            <td colspan="3">&nbsp;</td>
                        </tr>
                        <tr>
                            <td colspan="3">
                                <p>Este ecorreo ha sido generado automaticamente. Por favor, no responda.</p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </td>
        </tr>
    </tbody>
</table>
