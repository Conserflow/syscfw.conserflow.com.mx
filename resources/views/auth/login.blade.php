<meta name="viewport" content="width=device-width, initial-scale=1.0" />
@extends('layouts.app2')
<div class="py-5">
	<div class="container pt-5">
		<div class="row justify-content-center">
			<div class="col-xl-5">
				<div class="card" style="border: none;">
					<div class="card-body p-0">
						<div class="row no-gutters">
							<div class="col">
								<div class="mx-2 my-2">
								@include('plantilla.flash')
								</div>
								<div class="p-5">
									<div class="mb-4">
										<p class="text-center">
											<img class="img-logo" src="img/conserflow.png" alt="Conserflow-logo">
										</p>
									</div>
									<form action="{{ route('login') }}" method="POST">
									{{ csrf_field() }}
										<div class="form-group">
											<label for="name_user">Usuario</label>
											<input type="text" required name="name_user" class="form-control" id="name_user">
										</div>
										<div class="form-group mb-4">
											<label for="password">Contraseña</label>
											<input type="password" required name="password" class="form-control" id="password">
										</div>
										<button type="submit" class="btn btn-block btn-login">Ingresar</button>
									</form>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<style>
	body
	{
		background-image: url(img/fondo.jpg);
		/* background-image: url(img/1357.jpeg); */
		background-size: cover;
		height: 100vh;
		background-repeat: no-repeat;
	}

	.btn-login {
		background-color: #015d93;
		color: #fff;
	}

	.img-logo {
		height: 80px;
		width: auto;
	}
</style>
