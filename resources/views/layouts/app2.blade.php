<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="utf-8">
	<title>Inicio de Sesión</title>
	<link rel="stylesheet" href="/css/plantilla.css">
	<link rel="shortcut icon" href="favicon.ico">

	<!--Evita ver contenido despues de cerrar sesión-->
	<meta http-equiv="Expires" content="0" />
	<meta http-equiv="Pragma" content="no-cache" />
	<!--Fin-->
</head>

<body>
	<div class="container">

		@if (session()->has('flash'))
		<div class="alert alert-info">{{ session('flash') }}</div>
		@endif

		@yield('content')
	</div>
	<footer class="app-footer" align="center">
	</footer>
</body>

</html>

<script type="text/javascript">
	if (history.forward(1)) {
		location.replace(history.forward(1));
	}
</script>