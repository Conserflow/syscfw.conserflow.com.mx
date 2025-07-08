<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PRH-01 F-09 Cuestionario de Infraestructura</title>
</head>

<style type="text/css">
    @page {
        margin-top: 3cm;
        margin-left: 2cm;
        margin-right: 2cm;
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

    .h-2 {
        padding-top: .5rem;
        padding-bottom: .5rem;
    }

    .h-1 {
        padding-top: .3rem;
        padding-bottom: .3rem;
    }

    .text-bold {
        font-weight: bold;
    }

    .fill-line {
        border-bottom: 1px solid black;
        width: 100%;
    }

    .text-left {
        text-align: left;
    }

    .text-end {
        text-align: right;
    }

    body {
        font-family: Arial, Helvetica, sans-serif;
    }
</style>

<body>
    <footer>
        <div>
            <span style="color: #0070c0;font-weight: bold;font-size: 10;">CONSERFLOW S.A. DE C.V.</span>
            <span style="color:white;">mmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmm</span>
            <span style="color: #0070c0; font-weight: bold;"> Página 1 de 1</span>
        </div>

    </footer>

    <header>

        <table width="100%" class="table border-all">
            <tr>
                <td colspan="4">
                    <div class="text-blue text-bold">CONSERFLOW S.A. DE C.V.</b></div>
                </td>
            </tr>
            <tr>
                <td rowspan="3" width="20%"><img src="img/conserflow.png" width="120"></td>
                <td rowspan="3"><b>CUESTIONARIO DE INFRAESTRUCTURA</b> </td>
                <td width="15%" class="text-bold"> CÓDIGO</td>
                <td width="15%">PSGI-05/F-09</td>
            </tr>
            <tr>
                <td class="text-bold">REVISIÓN</td>
                <td>00</td>
            </tr>
            <tr>
                <td class="text-bold">EMISIÓN</td>
                <td>01.ABR.20</td>
            </tr>

        </table>

        <br>
        <table class="table text-left" width="100%" style="font-size: 12;">
            <tr>
                <td width="10%">Nombre: </td>
                <td width="90%" colspan="5">
                    <div class="fill-line">&nbsp;</div>
                </td>
            </tr>
            <tr>
                <td width="10%">Área:</td>
                <td>
                    <div class="fill-line">&nbsp;</div>
                </td>
                <td class="text-end">Puesto: &nbsp;</td>
                <td>
                    <div class="fill-line">&nbsp;</div>
                </td>
                <td class="text-end">Fecha &nbsp;</td>
                <td>
                    <div class="fill-line">&nbsp;</div>
                </td>
            </tr>
        </table>
        <br>
        <ol>
            @foreach($puntos as $p)
            <li> {{$p}}</li>
            <br>
            <br>
            <div class="fill-line"></div> <br>
            <div class="fill-line"></div><br>
            <div class="fill-line"></div> <br>
            @endforeach
        </ol>
</body>

</html>