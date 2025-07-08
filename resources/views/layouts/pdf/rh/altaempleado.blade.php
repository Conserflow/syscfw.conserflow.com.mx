<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ALTA DE PERSONAL</title>
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

    .alta {
        font-size: 12;
        font-weight: bold;
        text-decoration: underline;
    }

    .border-all tr td {
        border: 1px solid black;
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

    .text-end {
        text-align: right;
    }

    .text-sm {
        font-size: 8;
    }

    body {
        font-family: Arial, Helvetica, sans-serif;
    }

    .conser {
        font-weight: bold;
        font-size: 16;
    }

    .direccion {
        font-weight: bold;
        font-size: 8;
    }

    .fill-line {
        border-bottom: 1px solid black;
        width: 100%;
    }

    .nomina {
        margin-left: auto;
        margin-right: auto;
        font-weight: bold;
        font-size: 10;
        border: 1px solid black;
        width: 12rem;
        display: inline-block;
    }

    .sueldo {
        font-weight: bold;
        font-size: 10;
    }
</style>

<body>
    <header>
        <table width="100%" class="table border-all1">
            <tr>
                <td rowspan="2" width="20%">
                    <img src="{{'img/'.$datos_empresa['img']}}" width="180">
                </td>
                <td width="50%" class="conser">{{$datos_empresa["nombre"]}}</td>
            </tr>
            <tr>
                <td class="direccion">
                    {{$datos_empresa["direccion1"]}}
                    <br>
                    {{$datos_empresa["direccion2"]}}
                </td>
            </tr>
        </table>
    </header>

    <footer>
        <div>
            <p style="color: #0070c0;font-weight: bold;font-size: 10;">{{$datos_empresa["nombre"]}}</p>
        </div>
    </footer>

    <div>
        <br>
        <table class="table border-all1" width="100%">
            <tr>
                <td colspan="6" class="alta">ALTA DE PERSONAL</td>
            </tr>
            <tr>
                <td colspan="6">
                    <div>&nbsp;</div>
                </td>
            </tr>
            <tr>
                <td width="15%" class="text-bold">NOMBRE:</td>
                <td colspan="5">
                    <div class="fill-line">{{$empleado["nombre"]}}</div>
                </td>
            </tr>
            <tr>
                <td class="text-bold">RFC:</td>
                <td>
                    <div class="fill-line">{{$empleado["rfc"]}}</div>
                </td>
                <td class="text-bold">NSS:</td>
                <td>
                    <div class="fill-line">{{$empleado["nss"]}}</div>
                </td>
                <td class="text-bold">CURP:</td>
                <td>
                    <div class="fill-line">{{$empleado["curp"]}}</div>
                </td>
            </tr>
            <tr>
                <td class="text-bold">ESTADO CIVIL:</td>
                <td colspan="2">
                    <div class="fill-line">{{$empleado["edo_civil"]}}</div>
                </td>
                <td colspan="2" class="text-bold">TELEFONO FIJO / MOVIL</td>
                <td>
                    <div class="fill-line">{{$empleado["tel_celular"]}}</div>
                </td>
            </tr>
            <tr>
                <td class="text-bold" colspan="2">
                    LUGAR Y FECHA DE NACIMIENTO:
                </td>
                <td colspan="3">
                    <div class="fill-line">{{$empleado["lug_nac"]}}. {{$empleado["fecha_nacimiento"]}}</div>
                </td>
                <td>
                    <p>
                        <span class="text-bold">EDAD:</span>
                        <span class="fill-line">&nbsp;&nbsp; {{$edad}} AÑOS &nbsp;&nbsp;</span>
                    </p>
                </td>
            </tr>
            <tr>
                <td class="text-bold">
                    DOMICILIO:
                </td>
                <td colspan="5">
                    <div class="fill-line">{{$empleado["direccion"]}}</div>
                </td>
            </tr>
            <tr>
                <td class="text-bold">FECHA INGRESO:</td>
                <td colspan="2">
                    <div class="fill-line">{{$empleado["fecha_ingreso"]}}</div>
                </td>
                <td class="text-bold">PUESTO:</td>
                <td colspan="2">
                    <div class="fill-line">{{$empleado["puesto"]}}</div>
                </td>
            </tr>
            <tr>
                <td class="text-bold">DEPTO./ ÁREA</td>
                <td colspan="2">
                    <div class="fill-line">{{$empleado["departamento"]}}</div>
                </td>
            </tr>
            <tr>
                <td colspan="2" class="text-bold">TALLA CAMISA/OVEROL</td>
                <td>
                    <div class="fill-line">{{$empleado["talla_overol"]}}</div>
                </td>
                <td colspan="2" class="text-bold">TALLA ZAPATOS</td>
                <td>
                    <div class="fill-line">{{$empleado["talla_botas"]}}</div>
                </td>
            </tr>
        </table>
        <br>
        <br>
        <table class="table" width="100%" style="margin-left: 3rem; margin-right: 3rem;">
            <tr>
                <td width="20%" class="text-bold">BENEFICIARIO:</td>
                <td>
                    <div class="fill-line">
                        {{$empleado["beneficiario_nombre"]}}
                    </div>
                </td>
            </tr>
            <tr>
                <td width="20%" class="text-bold">PARENTESCO:</td>
                <td>
                    <div class="fill-line">
                        {{$empleado["beneficiario_parentesco"]}}
                    </div>
                </td>
            </tr>
            <tr>
                <td width="20%" class="text-bold">NO. TELEFONO:</td>
                <td>
                    <div class="fill-line">
                        {{$empleado["beneficiario_telefono"]}}
                    </div>
                </td>
            </tr>
        </table>
        <br>
        <br>
        <table class="table border-all1" width="100%">
            <tr>
                <td colspan="2">
                    <span class="nomina">
                        NÓMINA QUINCENAL
                    </span>
                </td>
                <td colspan="2">
                    <span class="nomina">
                        NÓMINA SEMANAL
                    </span>
                </td>
            </tr>
            <tr>
                <td colspan="4">&nbsp;</td>
            </tr>
            <tr>
                <td colspan="2" class="sueldo">
                    SUELDO QUINCENAL
                </td>
                <td colspan="2" class="sueldo">
                    SUELDO SEMANAL
                </td>
            </tr>
            <tr>
                <td width="15%" class="sueldo">NETO:</td>
                <td class="sueldo">
                    <div class="fill-line">$ &nbsp;&nbsp;&nbsp;{{$empleado["neto_quincenal"]}}</div>
                </td>
                <td width="15%" class="sueldo">NETO:</td>
                <td class="sueldo">
                    <div class="fill-line">$ &nbsp;&nbsp;&nbsp;{{$empleado["neto_semanal"]}}</div>
                </td>
            </tr>
            <tr>
                <td colspan="4">
                    <div style="height: 2rem;">&nbsp;</div>
                </td>
            </tr>
            <tr>
                <td width="15%" class="sueldo">BANCO:</td>
                <td class="sueldo">
                    <div class="fill-line"> &nbsp;&nbsp;&nbsp;{{$empleado["banco"]}}</div>
                </td>
                <td width="15%" class="sueldo">NO. CUENTA:</td>
                <td class="sueldo">
                    <div class="fill-line"> &nbsp;&nbsp;&nbsp;{{$empleado["numero_cuenta"]}}</div>
                </td>
            </tr>
            <tr>
                <td width="15%" class="sueldo">NO. TARJETA:</td>
                <td class="sueldo">
                    <div class="fill-line"> &nbsp;&nbsp;&nbsp;{{$empleado["numero_tarjeta"]}}</div>
                </td>
                <td width="15%" class="sueldo">CLABE INT.</td>
                <td class="sueldo">
                    <div class="fill-line"> &nbsp;&nbsp;&nbsp;{{$empleado["clabe"]}}</div>
                </td>
            </tr>
        </table>
        <br>
        <table width="100%" class="border-all1 table">
            <tr>
                <td colspan="2">
                    <p class="text-bold">TEHUACÁN, PUEBLA {{$empleado["fecha_ingreso"]}}</p>
                </td>
            </tr>
            <tr>
                <td style="height: 5rem;">&nbsp;</td>
            </tr>
            <tr>
                <td width="50%" class="text-bold">
                    <div style="max-width: 12rem; margin-left: 5rem;">
                        <p style="border-top: 1px solid black;">
                            NOMBRE Y FIRMA DEL TRABAJADOR</P>
                    </div>
                </td>
                <td width="50%" class="text-bold">
                    <div style="max-width: 12rem; margin-left: 5rem;">
                        <p style="border-top: 1px solid black;">
                            VO. BO.<br> RECURSOS HUMANOS </p>
                    </div>
                </td>
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