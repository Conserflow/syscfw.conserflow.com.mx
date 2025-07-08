<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class UserController extends Controller
{
    /**
     * Obtener todos los usuarios
     */
    public function index()
    {
        try
        {
            $usuarios = DB::table("users as u")
                ->select(
                    'u.id',
                    'u.id as u_id',
                    'u.name_user',
                    'u.condicion',
                    'u.email',
                    "u.empleado_id as e_id",
                    'u.tipo_ubicacion_id',
                    DB::raw("CONCAT_ws(' ',e.nombre,e.ap_paterno,e.ap_materno) as name")
                )
                ->where("u.condicion", 1)
                ->leftJoin('empleados as e', 'e.id', 'u.empleado_id')
                ->orderBy("name")
                ->get();
            return Status::Success("usuarios", $usuarios);
        }
        catch (Exception $e)
        {
            return Status::Error($e, "obtener los usuarios");
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try
        {
            if ($request->id == null)
            {
                $usuario = new User();
                $usuario->name = $request->name; // Nombre completo
                $usuario->name_user = $request->name_user; // Usuario
                $usuario->email = $request->email;
                $usuario->password = bcrypt($request->password);
                $usuario->tipo_ubicacion_id = $request->tipo_ubicacion_id;
                $usuario->condicion = 1;
                $usuario->session_id = 0;
                $usuario->empleado_id = $request->empleado_id;
                $usuario->navegador = "";
                // $usuario->validar_nav = '0';
                $usuario->save();
                // Guardar Correo TODO:
                Auditoria::AuditarCambios($usuario);
                // Enviar Correo
                $data = [
                    "nombre" => $request->name,
                    "usuario" => $request->name_user,
                    "email" => $request->email,
                    "contra" => $request->password,
                ];
                Mail::send('emails.sistema.registrousuario', $data, function ($message) use ($data)
                {
                    $message->to($data["email"], $data["nombre"])
                        ->subject("BIENVENIDO A CONSERFLOW | DATOS DE ACCESO");
                    $message->from('webmaster@conserflow.com', 'Conserflow');
                });
            }
            else
            {
                $usuario = User::find($request->id);
                $usuario->name = $request->name; // Nombre completo
                $usuario->name_user = $request->name_user; // Usuario
                $usuario->email = $request->email;
                $usuario->empleado_id = $request->empleado_id;
                $usuario->tipo_ubicacion_id = $request->tipo_ubicacion_id;
                Auditoria::AuditarCambios($usuario);
                $usuario->update();
            }
            return Status::Success();
        }
        catch (Exception $e)
        {
            return Status::Error($e, "guardar el usuario");
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        /*Cambia el campo session_id del registro en la BD*/
        $usuario = User::findOrFail($id);
        $usuario->session_id = '0';
        $usuario->save();
        /*************************************************/
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        /*Habilita o Inhabilita un usuario del sistema*/
        $usuario = User::findOrFail($id);
        if ($usuario->condicion == 0)
        {
            $usuario->condicion = 1;
        }
        else
        {
            $usuario->condicion = 0;
        }
        $usuario->update();
        return $usuario;
        /**********************************************/
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        /*Actualiza los registros del usuario indicado*/
        $usuario = User::findOrFail($id);
        $usuario->name = $request->name;
        $usuario->name_user = $request->name_user;
        $usuario->email = $request->email;
        if (isset($request->password))
            $usuario->password = bcrypt($request->password);
        $usuario->tipo_ubicacion_id = $request->tipo_ubicacion_id;
        $usuario->empleado_id = $request->empleado_id == 0 ? null : $request->empleado_id;
        $usuario->save();

        return response()->json(array(
            'status' => (isset($request->password))
        ));
    }
}
