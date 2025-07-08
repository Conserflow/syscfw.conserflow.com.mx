<?php

namespace App\Http\Controllers\Auth;

use App\User;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;

class LoginController extends Controller
{
    public function __construct()
    {
        $this->middleware('guest', ['only' => 'showLoginForm']);
    }




    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login()
    {
        $browser = $_SERVER['HTTP_USER_AGENT'];

        $credentials = $this->validate(request(), [
            $this->username() => 'required|string',
            'password' => 'required|string'
        ]);

        //Valida el campo para mostrar el error específico
        $user1 = User::orderBy('id', 'ASC')
            ->select('users.*')
            ->where("condicion", ">", 0)
            ->where('users.name_user', '=', $credentials['name_user'])
            ->first();

        if ($user1 == '')
        {
            return back()->with('error', 'Usuario y/o contraseña incorrectos')
                ->withInput(request([$this->username()]));
        }

        if (Auth::attempt([
            'name_user' => $credentials['name_user'],
            'password' => $credentials['password'],
        ]))
        {
            //obtenemos el Id del usuario logueado
            $iduser = auth()->id();

            //Se asigna el valor inicial del campo validar_nav
            $usuario = User::findOrFail($iduser);
            $usuario->navegador = $browser;
            $usuario->session_id = '1';
            $usuario->save();

            return redirect()->route('dashboard');
        }

        return Redirect::back()->withErrors([$this->username() => 'Usuario y/o contraseña incorrectos'])
            ->withInput(request([$this->username()]));
    }

    public function logout()
    {
        $iduser = auth()->id();
        //Fin
        $usuario = User::findOrFail($iduser);
        $usuario->session_id = '0';
        $usuario->navegador = '';
        $usuario->save();
        //Fin

        Auth::logout();
        Session::flush();
        return redirect('/');
    }

    public function username()
    {
        return 'name_user';
    }

    public function cerrarnavegador()
    {
        //obtenemos el Id del usuario logueado
        $iduser = auth()->id();
        //Fin

        //Al cerrar el navegador cambiamos el valor de validar_nav a 0
        $usuario = User::findOrFail($iduser);
        $usuario->session_id = '0';
        $usuario->navegador = '0';
        $usuario->save();
    }

    public function actualizarnavegador()
    {
        $browser = $_SERVER['HTTP_USER_AGENT'];
        //obtenemos el Id del usuario logueado
        $iduser = auth()->id();
        //Fin

        //Al actualizar el navegador cambiamos el valor de validar_nav a 1
        $usuario = User::findOrFail($iduser);
        $usuario->session_id = '1';
        $usuario->navegador = $browser;
        $usuario->save();
    }

    public function bloquearacceso()
    {
        $userlogueado = Auth::user();
        $sesion = $userlogueado->session_id;

        if ($sesion == 1)
        {

            $iduser = auth()->id();

            $usuario = User::findOrFail($iduser);
            $usuario->session_id = '1';
            $usuario->save();
            Auth::logout();
            Session::flush();
            return Redirect::to('dashboard');
        }
        else
        {
            return Redirect::to('Exit');
        }
    }

    public function inactividad()
    {
        //obtenemos el Id del usuario logueado
        $iduser = auth()->id();
        //Fin

        //Retorna la sesion para poder ser utilizada nuevamente
        $usuario = User::findOrFail($iduser);
        $usuario->session_id = '0';
        $usuario->navegador = '';
        $usuario->save();
        //Fin

        Auth::logout();
        Session::flush();
        return Redirect::to('/')->with('error', 'Su sesión ha expirado debido a inactividad en el sistema.');
    }
}
