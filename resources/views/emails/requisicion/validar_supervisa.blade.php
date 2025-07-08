<!DOCTYPE html>
<html lang="es">

<head style="">
    <meta charset="utf-8" style="">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        body {
            color: #242424;
            font-family: Arial, Helvetica, sans-serif;
            font-size: 16px;
        }

        .table {
            width: 100%;
            max-width: 800px;
            border-collapse: collapse;
            font-family: Arial, Helvetica, sans-serif;
            text-align: center;
        }

        .border-all tr td {
            border: 1px solid black;
        }

        .text-center {
            text-align: center;
        }

        .fw-bold {
            font-weight: 700;
        }
    </style>
</head>

<body>
    <h4>REVISIÓN DE REQUISICIÓN</h4>
    <p><b>Proyecto: </b> {{ $proyecto }}</p>
    <p><b>Folio: </b> {{ $folio }}</p>
    <p><b>Solicitado por: </b> {{ $solicita }}</p>
    <p><b>Área Solicitante: </b> {{ $area_solicita }}</p>
    <p><b>Partidas totales:</b> {{ $total_partidas }}</p>
    @if ($total_partidas > 10)
        <p>Se muestran las 10 primeras:</p>
    @endif

    <table class="table border-all">
        <tr>
            <td class="fw-bold text-center">CONCEPTO</td>
            <td class="fw-bold text-center">MARCA</td>
            <td class="fw-bold text-center">CANTIDAD</td>
        </tr>
        <tbody>
            @foreach ($partidas as $p)
                <tr>
                    <td>{{ $p->concepto }}</td>
                    <td>{{ $p->marca }}</td>
                    <td>{{ $p->cantidad }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <p class="font-italic text-monospace">Este mensaje ha sido enviado de forma automática. Por favor, no responda.
</body>

</html>
