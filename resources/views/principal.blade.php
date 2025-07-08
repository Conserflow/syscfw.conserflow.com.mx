<!DOCTYPE html>
<?php
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header('Content-Type: text/html');
?>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Sistema">
    <meta name="author" content="">
    <meta name="keyword" content="">
    <title>Sistema de Gestion Conserflow</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
    <meta http-equiv="Pragma" content="no-cache" />
    <meta http-equiv="Expires" content="0" />

    <!--Evita ver contenido despues de cerrar sesión-->
    <meta http-equiv="Expires" content="0" />
    <meta http-equiv="Pragma" content="no-cache" />

    <link rel="stylesheet" href="css/tablesfixed.css">
    <link href="css/plantilla.css" rel="stylesheet">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.2/css/all.css" integrity="sha384-fnmOCqbTlWIlj8LyTjo7mOUStjsKC4pOpQbqyi7RrhN7udi9RwhKkMHpvLbHG9Sr" crossorigin="anonymous">
</head>

<body class="app header-fixed sidebar-fixed aside-menu-fixed aside-menu-hidden">
    <div id="app">
        <header class="app-header navbar">
            <button class="navbar-toggler mobile-sidebar-toggler d-lg-none mr-auto" type="button">
                <span class="navbar-toggler-icon"></span>
            </button>
            <a class="navbar-brand" href="#" @click="abrirModalModulos()"></a>
            <button class="navbar-toggler sidebar-toggler d-md-down-none" type="button">
                <span class="navbar-toggler-icon"></span>
            </button>
	    <span class="text-danger text-center"></span>
            <ul class="nav navbar-nav ml-auto">
                <a class="nav-link nav-link" href="#" @click="abrirModalModulos">
                    <i class="fas fa-th icon-menu"></i>
                </a>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle mr-2 nav-link" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">
                        <img src="img/user2.png" class="img-avatar">
                        <span class="d-md-down-none">{{ auth()->user()->name }}</span>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right">
                        <div class="dropdown-header text-center">
                            <strong>Cuenta</strong>
                        </div>

                        <form method="POST" action="{{ route('logout') }}">
                            {{ csrf_field() }}
                            <button class="dropdown-item" @click="cerrarSesion()"><i class="fa fa-lock"></i>Cerrar Sesión</button>
                        </form>
                        <form method="POST" action="{{ route('privacidad') }}" target="_blank">
                            {{ csrf_field() }}
                            <button class="dropdown-item"><i class="fas fa-user-secret"></i>Privacidad</button>
                        </form>
                    </div>
                </li>
            </ul>
        </header>

        <div class="app-body">

            @include('plantilla.sidebar')
            <!-- Contenido Principal -->
            @yield('contenido')
            <!-- /Fin del contenido principal -->
        </div>
    </div>
    <footer class="d-none d-lg-block app-footer fixed-bottom text-right">
        Sistema de Gestion Conserflow &copy; - {{date("Y")}}
    </footer>

    <script src="{{ mix('js/app.js') }}"></script>

    <script src="js/plantilla.js"></script>

    <style>
        .icon-menu {
            font-size: 1.5rem;
            padding-right: 15px;
            border-right: 1px solid gray;
            line-height: 2rem;
        }
    </style>
</body>

</html>
