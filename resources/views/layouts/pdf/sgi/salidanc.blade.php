<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PRH-01/F-01 - PERFIL Y DESCRIPCION DE PUESTO</title>
</head>

<style type="text/css">
    @page {
        margin-top: 3cm;
        margin-left: 1cm;
        margin-right: 1cm;
        margin-bottom: 2cm;
    }

    header {
        position: fixed;
        top: -90px;
        left: 0px;
        right: 0px;
    }

    footer {
        position: fixed;
        bottom: -40px;
        left: 0px;
        right: 0px;
        height: 30px;
        font-family: Arial, Helvetica, sans-serif;
        font-size: 14px;
    }

    table {
        border-collapse: collapse;
        font-family: Arial, Helvetica, sans-serif;
        font-size: 12px;
        text-align: center;
    }

    .border-all tr td {
        border: 1px solid black;
    }

    .text-blue {
        color: #0070c0;
    }

    .text-white {
        color: white;
    }

    .h-2 {
        padding-top: .5rem;
        padding-bottom: .5rem;
    }

    .h-4 {
        padding-top: 1rem;
        padding-bottom: 1rem;
    }

    .h-1 {
        padding-top: .3rem;
        padding-bottom: .3rem;
    }

    .text-bold {
        font-weight: bold;
    }

    .text-left {
        text-align: left;
    }

    .text-center {
        text-align: center;
    }

    .text-end {
        text-align: right;
    }

    .text-lg {
        font-size: 13;
    }

    .text-normal {
        font-weight: normal
    }

    .bg-blue {
        background-color: #0070c0;
        color: white;
        font-weight: bold;
    }

    .bg-gray {
        color: black;
        font-weight: bold;
        background-color: #BFBFBF;
    }

    .bg-gray2 {
        background-color: #808080;
    }

    body {
        font-family: Arial, Helvetica, sans-serif;
    }

    table tr .border-bottom-none {
        border-bottom: none
    }

    table tr .border-top-none {
        border-top: none;
    }

    .border-external {
        border: 2px solid black;
    }

    .border-all-external tr td {
        border: 2px solid black;
    }

    .cell-check {
        height: 1.5rem;
        width: 1.5rem;
        border: 1px solid;
        padding-top: 10px;
    }

    .cell-line {
        border-bottom: 1px solid;
    }

    .padding {
        padding: .3rem 0;
    }

    .mt-1 {
        margin-top: .5rem;
    }
</style>

<body>
    <header>

        <table width="100%" class="table border-all-external border-all">
            <tr>
                <td colspan="4">
                    <div class="text-blue text-bold">CONSERFLOW S.A. DE C.V.</b></div>
                </td>
            </tr>
            <tr>
                <td rowspan="3" width="20%"><img src="img/conserflow.png" width="120"></td>
                <td rowspan="3"><b>REPORTE DE SALIDA NO CONFORME</b> </td>
                <td width="15%" class="text-bold"> CÓDIGO</td>
                <td width="15%">PCC-014/F-01</td>
            </tr>
            <tr>
                <td class="text-bold">REVISIÓN</td>
                <td>02</td>
            </tr>
            <tr>
                <td class="text-bold">EMISIÓN</td>
                <td>13.ABR.23</td>
            </tr>

        </table>
    </header>

    <footer>
        <div>
            <p style="color: #0070c0;font-weight: bold;font-size: 10;">CONSERFLOW S.A. DE C.V.</p>
        </div>
    </footer>

    <div style="">
        <table class="table border-all border-external" width="100%">
            <tr>
                <td width="25%" class="bg-gray text-bold padding">
                    Elaborado por <br>
                    <span class="text-normal">Prepared by</span>
                </td>
                <td>{{ $salida->empleadoElabora->nombre }}</td>
                <td width="25%" class="bg-gray text-bold padding">
                    Fecha de elaboración <br>
                    <span class="text-normal">Date of preparation</span>
                </td>
                <td>{{ $salida->fecha_elaboracion }}</td>
            </tr>
            <tr>
                <td width="25%" class="bg-gray text-bold padding">
                    Detectado por <br>
                    <span class="text-normal">Detected by</span>
                </td>
                <td>{{ $salida->empleadoDetecta ? $salida->empleadoDetecta->nombre : '' }}</td>
                <td width="25%" class="bg-gray text-bold padding">
                    Área<br>
                    <span class="text-normal">Area</span>
                </td>
                <td>{{ $salida->area->nombre }}</td>
            </tr>
            <tr>
                <td width="25%" class="bg-gray text-bold padding">
                    Fecha de detección <br>
                    <span class="text-normal">Detection date</span>
                </td>
                <td>{{ $salida->fecha_deteccion }}</td>
                <td width="25%" class="bg-gray text-bold padding">
                    Número de reporte<br>
                    <span class="text-normal">Report Number</span>
                </td>
                <td>{{ $salida->folio }}</td>
            </tr>
        </table>
        <br>

        <table class="table border-all border-external" width="100%">
            <tr>
                <td class="text-white bg-blue text-bold text-lg" colspan="3">
                    1. IDENTIFICACIÓN <br>
                    <span class="text-normal">1. IDENTIFICATION</span>
                </td>
            </tr>
            <tr>
                <td class="text-bold bg-gray" colspan="3">
                    Descripción de la Salida No Conforme Detectada <br>
                    <span class="text-normal">Description of Noncompliant Output Detected</span>
                </td>
            </tr>
            <tr>
                <td style="height: 15rem" colspan="3">
                    {{ $salida->descripcion }}
                </td>
            </tr>

            <tr>
                <td class="text-bold bg-gray" width="15%" rowspan="4">
                    Rastreabilidad / Origen Asociado <br>
                    <span class="text-normal">Traceability / Associated Origin</span>
                </td>
                <td class="text-bold bg-gray padding" width="35%">
                    Proyecto / Servicio <br>
                    <span class="text-normal">Project / Service</span>
                </td>
                <td>{{ $salida->proyecto->nombre_corto }}</td>
            </tr>
            <tr>
                <td class="text-bold bg-gray padding">
                    Nombre del cliente, proveedor o proceso <br>
                    <span class="text-normal">Name of customer, supplier or process</span>
                </td>
                <td>{{ $salida->cliente_proveedor }}</td>
            </tr>
            <tr>
                <td class="text-bold bg-gray padding">
                    Número de comunicado <br>
                    <span class="text-normal">Press release number</span>
                </td>
                <td>{{ $salida->no_comunicado }}</td>
            </tr>
            <tr>
                <td class="text-bold bg-gray padding">
                    Orden de Compra <br>
                    <span class="text-normal">Purchase Order</span>
                </td>
                <td>{{ $salida->no_oc }}</td>
            </tr>
        </table>
        <br>
        <table class="border-external" width="100%">
            <tr>
                <td class="text-white bg-blue text-bold text-lg" colspan="12">
                    2. TRATAMIENTO <br>
                    <span class="text-normal">2. TREATMENT</span>
                </td>
            </tr>
            <tr>
                <td colspan="12"><span>&nbsp;</span></td>
            </tr>
            <tr>
                <td class="text-bold">Corrección
                    <span class="text-normal">Correction</span>
                </td>
                <td class="cell" width="5%">
                    <div class="cell-check">{{ $salida->tratamiento == 1 ? 'X' : '' }}</div>
                </td>
                <td class="text-bold">Separación
                    <span class="text-normal">Separation</span>
                </td>
                <td class="cell" width="5%">
                    <div class="cell-check">{{ $salida->tratamiento == 2 ? 'X' : '' }}</div>
                </td>
                <td class="text-bold">Contención
                    <span class="text-normal">Containment</span>
                </td>
                <td class="cell" width="5%">
                    <div class="cell-check">{{ $salida->tratamiento == 3 ? 'X' : '' }}</div>
                </td>
                <td class="text-bold">Devolución
                    <span class="text-normal">Return</span>
                </td>
                <td class="cell" width="5%">
                    <div class="cell-check">{{ $salida->tratamiento == 4 ? 'X' : '' }}</div>
                </td>
                <td class="text-bold">Suspensión
                    <span class="text-normal">Suspension</span>
                </td>
                <td class="cell" width="5%">
                    <div class="cell-check">{{ $salida->tratamiento == 5 ? 'X' : '' }}</div>
                </td>
                <td class="text-bold">Desecho
                    <span class="text-normal">Waste</span>
                </td>
                <td class="cell" width="5%">
                    <div class="cell-check">{{ $salida->tratamiento == 6 ? 'X' : '' }}</div>
                </td>
            </tr>
            <tr>
                <td colspan="12"><span>&nbsp;</span></td>
            </tr>
            <tr>
                <td colspan="3" class="text-bold">Información al cliente <br>
                    <span class="text-normal">Information to the client</span>
                </td>
                <td class="cell" width="5%">
                    <div class="cell-check">{{ $salida->tratamiento == 7 ? 'X' : '' }}</div>
                </td>
                <td class="text-bold">Otro <br>
                    <span class="text-normal">Other</span>
                </td>
                <td class="cell" width="5%">
                    <div class="cell-check">{{ $salida->tratamiento == 8 ? 'X' : '' }}</div>
                </td>
                <td colspan="5" class="cell-line">
                    <div>{{ $salida->tratamiento_otro }}</div>
                </td>
                <td></td>
            </tr>
            <tr>
                <td colspan="12"><span>&nbsp;</span></td>
            </tr>
        </table>
        <br>
        <br>
        <table width="100%" class="table border-all border-external">
            <tr>
                <td class="bg-gray2 text-bold" colspan="3">
                    ACCIONES <br>
                    <span class="text-normal">ACTIONS</span>
                </td>
            </tr>
            <tr>
                <td width="40%" class="text-bold bg-gray">Actividad <br>
                    <span class="text-normal">Activity</span>
                </td>
                <td width="30%" class="text-bold bg-gray">Responsable <br>
                    <span class="text-normal">Responsible</span>
                </td>
                <td width="30%" class="text-bold bg-gray">Fecha <br>
                    <span class="text-normal">Date</span>
                </td>
            </tr>
            @foreach (json_decode($salida->acciones) as $a)
                <tr>
                    <td class="padding">{{ $a->actividad }}</td>
                    <td class="padding">{{ $a->responsable->nombre }}</td>
                    <td class="padding">{{ $a->fecha }}</td>
                </tr>
            @endforeach
        </table>
        <br>
        <table class="border-external border-all" width="100%">
            <tr>
                <td class="text-white bg-blue text-bold text-lg" colspan="6">
                    3. VERIFICACIÓN <br>
                    <span class="text-normal">3. VERIFICATION</span>
                </td>
            </tr>
            <tr>
                <td class="text-bold" colspan="6">Resultado de las actividades <br>
                    <span class="text-normal">Outcome of activities</span>
                </td>
            </tr>
            <tr>
                <td class="padding" colspan="6" style="height: 3rem">
                    {{ $salida->resultado }}
                </td>
            </tr>
            <tr>
                <td class="text-bold bg-gray" width="18%">
                    Nombre y firma de quien verifica <br>
                    <span class="text-normal">Verifier name and signature</span>
                </td>
                <td class="padding" width="30%" colspan="2">
                    {{ $salida->empleadoVerifica ? $salida->empleadoVerifica->nombre : '' }}
                </td>
                {{ $img = $salida->empleadoVerifica ? 'administrativos/' . $salida->empleadoVerifica->id . '.png' : '' }}
                <td class="" width="15%">
                    @if (file_exists($img))
                        <img class="mt-1" src="{{ $img }}" height="50"> <br>
                    @endif
                    Firma <br>
                    Signature
                </td>
                <td class="padding text-bold bg-gray" width="15%">
                    Fecha de verificación <br>
                    <span class="text-normal">Verification Date</span>
                </td>
                <td>{{ $salida->fecha_verificacion }}</td>
            </tr>
            <tr>
                <td class="text-bold bg-gray" colspan="2">
                    ¿La Salida No Conforme requiere una Acción Correctiva? <br>
                    <span class="text-normal">Does Nonconforming Output Require Corrective Action?</span>
                </td>
                <td>{{ $salida->require_correccion == 1 ? 'SÍ' : 'NO' }}</td>
                <td class="text-bold bg-gray" colspan="2">
                    Número de acción correctiva correspondiente <br>
                    <span class="text-normal">Corresponding corrective action number</span>
                </td>
                <td>{{ $salida->no_accion_correctiva }}</td>
            </tr>
        </table>
    </div>

    <script type="text/php">
        if (isset($pdf)) {
        $text = "PAGINA {PAGE_NUM} DE {PAGE_COUNT}";
        $size = 9;
        $color = #0070c0;
        $font = $fontMetrics->getFont("Verdana");
        $width = $fontMetrics->get_text_width($text, $font, $size) / 2;
        $x = ($pdf->get_width() - $width) / 1;
        $y = $pdf->get_height() - 35;
        $pdf->page_text($x, $y, $text, $font, $size,$color);
    }
</script>
</body>

</html>
