<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PCO-02/F-01 - ALTA Y/O MODIFICACIÓN DE PROVEEDORES</title>
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
        border: 2px solid black;
        font-family: Arial, Helvetica, sans-serif;
        font-size: 12px;
        text-align: center;
        width: 100%
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
        padding-top: 1.5rem;
        padding-bottom: 1.5rem;
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

    .text-sm {
        font-size: 8;
    }

    .bg-blue {
        background-color: #0070c0;
        color: white;
        text-transform: uppercase;
        font-weight: bold;
        padding-top: 3px;
        padding-bottom: 3px;
    }

    .bg-gray {
        color: black;
        background-color: #BFBFBF;
        padding-top: 2px;
        padding-bottom: 2px;
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
</style>

<body>
    <header>
        <table class="table border-all">
            <tr>
                <td colspan="4">
                    <div class="text-blue text-bold">CONSERFLOW S.A. DE C.V.</b></div>
                </td>
            </tr>
            <tr>
                <td rowspan="3" width="20%"><img src="img/conserflow.png" width="120"></td>
                <td rowspan="3"><b>ALTA Y/O MODIFICACIÓN DE PROVEEDORES</b> </td>
                <td width="13%" class="text-bold"> CÓDIGO</td>
                <td width="13%">PCO-02/F-01</td>
            </tr>
            <tr>
                <td class="text-bold">REVISIÓN</td>
                <td>{{$revision}}</td>
            </tr>
            <tr>
                <td class="text-bold">EMISIÓN</td>
                <td>{{$emision}}</td>
            </tr>

        </table>
    </header>

    <footer>
        <div>
            <p style="color: #0070c0;font-weight: bold;font-size: 10;">CONSERFLOW S.A. DE C.V.</p>
        </div>
    </footer>

    <div>
        <table class="table border-all">
            <tr>
                <td class="bg-gray" width="20%">Tipo de movimiento</td>
                <td>{{$cambio["tipo_movimiento"]}}</td>
                <td class="bg-gray" width="13%">Fecha</td>
                <td width="13%">{{$cambio["fecha"]}}</td>
            </tr>
        </table>

        <div style="height: 5px;"></div>
        <table class="table border-all">
            <tr>
                <td class="bg-blue" colspan="9">Datos del proveedor</td>
            </tr>
            <tr>
                <td class="bg-gray" width="15%">Razón Social</td>
                <td colspan="8">{{$campos->razon_social}}</td>
            </tr>
            @if($formatoNR02)
            <tr>
                <td class="bg-gray" width="15%">Nombre comercial</td>
                <td colspan="8">{{$campos->nombre}}</td>
            </tr>
            @endif
            <tr>
                <td class="bg-gray" width="15%">RFC</td>
                <td>{{$campos->rfc}}</td>
                @if($formatoNR02)
                <td class="bg-gray" width="8%">Suministra</td>
                <td colspan="3">{{$campos->giro}}</td>
                @else
                <td class="bg-gray" width="8%">Giro</td>
                <td colspan="3">{{$campos->giro}}</td>
                 @endif
                <td class="bg-gray" width="13%">Nacionalidad</td>
                <td colspan="2">{{$campos->nacionalidad}}</td>
            </tr>
            <tr>
                <td class="bg-gray">Calle</td>
                <td colspan="2">{{$campos->calle}}</td>
                <td class="bg-gray" width="10%">No. Exterior</td>
                <td>{{$campos->no_exterior}}</td>
                <td class="bg-gray">No. Interior</td>
                <td>{{$campos->no_interior}}</td>
                <td class="bg-gray">C.P.</td>
                <td>{{$campos->cp}}</td>
            </tr>
            <tr>
                <td class="bg-gray">Colonia</td>
                <td colspan="2">{{$campos->colonia}}</td>
                <td class="bg-gray">Delegación/ Municipio</td>
                <td colspan="2">{{$campos->municipio}}</td>
                <td class="bg-gray">Ciudad/Estado</td>
                <td colspan="2">{{$campos->estado}}</td>
            </tr>
        </table>

        <div style="height: 5px;"></div>

        <table class="table border-all">
            <tr>
                <td class="bg-blue" colspan="6">Datos bancarios</td>
            </tr>
            <tr>
                <td class="bg-gray" width="15%">Banco</td>
                <td width="26%">{{$campos->banco}}</td>
                <td class="bg-gray">Cuenta Clabe</td>
                <td>{{$campos->cuenta}}</td>
                <td class="bg-gray" width="10%">Tipo de moneda</td>
                <td>{{$campos->moneda}}</td>
            </tr>
            @if($formatoNR02)
            <tr>
                <td class="bg-gray" width="15%">Banco</td>
                <td width="26%">{{$campos->banco2 ?? '---'}}</td>
                <td class="bg-gray" width="15%">Cuenta Clabe</td>
                <td>{{$campos->cuenta2 ?? '---'}}</td>
                <td class="bg-gray" width="10%">Tipo de moneda</td>
                <td>{{$campos->moneda2 ?? '---'}}</td>
            </tr> 
            <tr>
                <td class="bg-gray" width="15%">Crédito</td>
                <td width="26%">
                     <table width="100%" border="0" cellspacing="0" cellpadding="0" style="border: none;">
                        <tr border="0" style="border: none;">
                            <td class="bg-gray" width="33%" style="border: 1px solid #BFBFBF; text-align: center;">Si</td>
                            <td width="34%" style="border: none; padding: 0; text-align: center;">X</td>
                            <td class="bg-gray" width="33%" style="border: 1px solid #BFBFBF; text-align: center;">No</td>
                        </tr>
                    </table>    
                </td>
                <td width="9%"></td>
                <td class="bg-gray" width="15%">Límite de crédito</td>
                <td colspan="2">552</td>                
            </tr>           
            @else
            <tr>
                <td class="bg-gray" width="15%">Condiciones de pago</td>
                <td colspan="5">{{$campos->condiciones}}</td>
            </tr>
            @endif
        </table>

        <div style="height: 5px;"></div>
        <table class="table border-all">
            <tr>
                <td class="bg-blue" colspan="6">Contacto</td>
            </tr>
            <tr>
                <td class="bg-gray" width="15%" rowspan="2">Contacto de Venta</td>
                <td width="35%" rowspan="2">{{$campos->ventas_contacto}}</td>
                <td class="bg-gray" width="15%">Teléfono</td>
                <td width="10%">{{$campos->ventas_telefono}}</td>
                <td class="bg-gray" width="10%">Celular</td>
                <td width="10%">{{$campos->ventas_celular}}</td>
            </tr>
            <tr>
                <td class="bg-gray" width="15%">Correo</td>
                <td colspan="3">{{$campos->ventas_correo}}</td>
            </tr>
            <tr>
                <td class="bg-gray" width="15%" rowspan="2">Contacto de Facturación</td>
                <td width="35%" rowspan="2">{{$campos->facturacion_contacto}}</td>
                <td class="bg-gray" width="15%">Teléfono</td>
                <td width="10%">{{$campos->facturacion_telefono}}</td>
                <td class="bg-gray" width="10%">Celular</td>
                <td width="10%">{{$campos->facturacion_celular}}</td>
            </tr>
            <tr>
                <td class="bg-gray" width="15%">Correo</td>
                <td colspan="3">{{$campos->facturacion_correo}}</td>
            </tr>
        </table>

        <div style="height: 5px;"></div>
        @if($cambio->fecha<"2022-08-22")
        <table class="table border-all">
            <tr>
                <td class="bg-blue" colspan="7">Referencias</td>
            </tr>
            <tr>
                <td class="bg-gray" width="15%">Empresa 1</td>
                <td colspan="2">{{$campos->referencia1_empresa}}</td>
                <td class="bg-gray" width="13%">Contacto</td>
                <td width="15%">{{$campos->referencia1_contacto}}</td>
                <td class="bg-gray" width="10%">Teléfono</td>
                <td width="15%">{{$campos->referencia1_telefono}}</td>
            </tr>
            <tr>
                <td class="bg-gray" colspan="2">Tiempo de relacion comercial</td>
                <td width="15%">{{$campos->referencia1_relacion}}</td>
                <td class="bg-gray">Comentarios</td>
                <td colspan="3">{{$campos->referencia1_comentarios}}</td>
            </tr>
            <tr>
                <td style="height: 5px;" colspan="7"></td>
            </tr>
            <tr>
                <td class="bg-gray" width="15%">Empresa 2</td>
                <td colspan="2">{{$campos->referencia2_empresa}}</td>
                <td class="bg-gray" width="13%">Contacto</td>
                <td width="15%">{{$campos->referencia2_contacto}}</td>
                <td class="bg-gray" width="10%">Teléfono</td>
                <td width="15%">{{$campos->referencia2_telefono}}</td>
            </tr>
            <tr>
                <td class="bg-gray" colspan="2">Tiempo de relacion comercial</td>
                <td width="15%">{{$campos->referencia2_relacion}}</td>
                <td class="bg-gray">Comentarios</td>
                <td colspan="3">{{$campos->referencia2_comentarios}}</td>
            </tr>
            </table>
            @endif

            @if($formatoNR02)
            <div style="height: 5px;"></div>
            <table class="table border-all">
                <tr>
                    <td class="bg-blue" colspan="4">Razón de modificación</td>
                </tr>
                <tr>
                    <td width="30%" class="bg-gray">Cambio de domicilio</td>
                    <td width="20%"></td>
                    <td width="30%" class="bg-gray">Cambio datos bancarios</td>
                    <td width="20%"></td>
                </tr>
                 <tr>
                    <td width="30%" class="bg-gray">Cambio de datos de contacto de ventas</td>
                    <td width="20%"></td>
                    <td width="30%" class="bg-gray">Cambio de datos de contacto de facturación</td>
                    <td width="20%"></td>
                </tr>               
            </table>
            <div style="height: 5px;"></div>
            <table class="table border-all">
                <tr>
                    <td class="bg-blue" colspan="2">Documentos recibidos</td>
                    <td class="bg-blue" >Realizó</td>
                </tr>
                <tr>
                    <td width="35%" class="bg-gray">Cambio de datos de contacto de ventas</td>
                    <td width="15%"></td>
                    <td width="50%" rowspan="2"></td>
                </tr>
                <tr>
                    <td width="35%" class="bg-gray">Caratula bancaría</td>
                    <td width="15%"></td>
                </tr>
                <tr>
                    <td width="35%" class="bg-gray">Aplicables conforme a los criterios adicionales (PCO-02/F-05)</td>
                    <td width="15%"></td>
                    <td width="50%" class="bg-gray"> Nombre</td>
                </tr>
            </table>
            @else
            <div style="height: 5px;"></div>
            <table class="table border-all">
                <tr>
                    <td class="bg-blue">modificación</td>
                </tr>
                <tr>
                    <td class="h-4">{{$cambio["modificacion"]}}</td>
                </tr>
            </table>
            <div style="height: 5px;"></div>
            <table class="table border-all">
                <tr>
                    <td class="bg-blue">Anexos</td>
                </tr>
                <tr>
                    <td class="h-4">{{$cambio["anexos"]}}</td>
                </tr>
            </table>
            @endif 



    </div>

    <script type="text/php">
        if (isset($pdf)) {
        $text = "PAGINA {PAGE_NUM} DE {PAGE_COUNT}";
        $size = 8;
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