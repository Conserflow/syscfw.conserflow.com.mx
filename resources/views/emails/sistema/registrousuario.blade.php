<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Registro</title>
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 14px;
        }

        .card {
            background-color: #EBEBEB;
            padding: 1rem;

        }

        .mensaje {
            color: white;
            background-color: #de3c3f;
        }

        .contenido {
            font-size: 16px;
            margin-left: 2rem;
        }

        .contacto {
            font-size: 8;
            text-align: center;
        }

        .text-bold {
            font-weight: bold;
        }

        .text-normal-dx {
            font-weight: normal;
        }

        .ingresar {
            margin-left: auto;
            margin-right: auto;
            display: block;
            text-decoration: none;
            width: 115px;
            height: 25px;
            background: #0070c0;
            padding-bottom: 10px;
            padding-top: 10px;
            text-align: center;
            border-radius: 8px;
            color: white !important;
            font-weight: bold;
            line-height: 25px;
        }

        .center {
            margin-left: auto;
            margin-right: auto;
        }
    </style>

<body>
    <div class="card">
        <span class="mensaje">
            Este mensaje se dirige exclusivamente a su destinatario. Contiene información CONFIDENCIAL
            o cuya divulgación está prohibida. Si ha recibido este mensaje por error, debe saber que su
            lectura, copia y uso están prohibidos.
        </span>
        <br>
        <div>
            <br>
            <p>Buen día</p>
            <p>Por medio del presente comparto los datos de acceso al sistema.
                Los accesos al sistema son personales y de completa responsabilidad del usuario. </p>
            <p>Sus datos de acceso son: </p>
            <br>
            <div class="contenido">
                <p class="text-bold">Usuario: <span class="text-normal-dx">{{$usuario}}</span></p>
                <p class="text-bold">Contraseña: <span class="text-normal-dx">{{$contra}}</span></p>
            </div>

            <br>
            <a href="http://syscfw.conserflow.com.mx" class="ingresar">Ingresar</a>
            <br>
            <p class="contacto">Cualquier duda o comentario puede comunicarse al departamento de Tecnologías de la Información</p>
            <div class="text-center" style="text-align:center">
                <img src="http://syscfw.conserflow.com.mx/img/conserflow.png" class="center" height="50" width="auto" alt="CONSERFLOW">
            </div>
        </div>
    </div>
</body>

</html>