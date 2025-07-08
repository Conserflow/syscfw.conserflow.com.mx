<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Equipos de Calibración</title>

    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 12px;
        }

        .titulo {
            font-weight: 700;
            text-align: center;
            font-size: 1.5rem;
        }

        table {
            width: 100%;
            color: #212529;
            border-collapse: collapse;
        }

        table th {
            border-top: 1px solid #dee2e6;
        }

        thead {
            color: #fff;
            background-color: #0070c0;
            border-color: #fff;
        }

        .desc {
            font-size: 12px !important;
            font-weight: normal;
        }
    </style>

<body>
    <div class="card">
        @if (count($equipos_vencidos) > 0)
            <p class="titulo">Equipos Vencidos</p>
            <div style="max-height: 20rem;overflow-y: scroll">
                <table border="0">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Equipo</th>
                            <th>Marca</th>
                            <th>Modelo</th>
                            <th>NS</th>
                            <th>Rando Medición</th>
                            <th>Resguardo</th>
                            <th>Frecuencia de Calibración</th>
                            <th>Fecha</th>
                            <th>Tipo</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($equipos_vencidos as $i => $e)
                            <tr>
                                <th><span class="desc">{{ $i + 1 }}</span></th>
                                <th><span class="desc">{{ $e->equipo }}</span></th>
                                <th><span class="desc">{{ $e->marca }}</span></th>
                                <th><span class="desc">{{ $e->modelo }}</span></th>
                                <th><span class="desc">{{ $e->ns }}</span></th>
                                <th><span class="desc">{{ $e->rango_medicion }}</span></th>
                                <th><span class="desc">{{ $e->resguardo }}</span></th>
                                <th><span class="desc">{{ $e->frecuencia }}</span></th>
                                <th><span
                                        class="desc">{{ $e->proxima_fecha == 0 ? 'N/D' : $e->proxima_fecha }}</span>
                                </th>
                                <th><span class="desc">{{ $e->tipo }}</span></th>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <hr>
        @endif

        @if (count($equipos_por_vencer) > 0)
            <p class="titulo">Equipos Próximos a Vencer</p>
            <div style="max-height: 20rem;overflow-y: scroll">
                <table border="0">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Equipo</th>
                            <th>Marca</th>
                            <th>Modelo</th>
                            <th>NS</th>
                            <th>Rando Medición</th>
                            <th>Resguardo</th>
                            <th>Frecuencia de Calibración</th>
                            <th>Fecha</th>
                            <th>Tipo</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($equipos_por_vencer as $i => $e)
                            <tr>
                                <th><span class="desc">{{ $i + 1 }}</span></th>
                                <th><span class="desc">{{ $e->equipo }}</span></th>
                                <th><span class="desc">{{ $e->marca }}</span></th>
                                <th><span class="desc">{{ $e->modelo }}</span></th>
                                <th><span class="desc">{{ $e->ns }}</span></th>
                                <th><span class="desc">{{ $e->rango_medicion }}</span></th>
                                <th><span class="desc">{{ $e->resguardo }}</span></th>
                                <th><span class="desc">{{ $e->frecuencia }}</span></th>
                                <th><span
                                        class="desc">{{ $e->proxima_fecha == 0 ? 'N/D' : $e->proxima_fecha }}</span>
                                </th>
                                <th><span class="desc">{{ $e->tipo }}</span></th>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
        <br>
        <br>
        <p class="font-italic text-monospace">Este mensaje ha sido enviado de forma automática. Por favor, no responda.
        </p>
    </div>
</body>

</html>
