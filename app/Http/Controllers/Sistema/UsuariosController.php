<?php

namespace App\Http\Controllers\Sistema;

use App\ElementosMenu;
use App\ElementosSubmenu;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Status;
use Exception;
use Illuminate\Support\Facades\DB;

class UsuariosController extends Controller
{
    /**
     * Obtener los usuarios actvios
     */
    public function GetUsers()
    {
        try
        {
            $users = DB::table("users as u")
                ->select(
                    "u.id",
                    "u.name",
                    "u.name_user",
                    "u.condicion"
                )
                ->where("condicion", 1)
                ->orderBy("u.name_user")
                ->get();
            return Status::Success("usuarios", $users);
        }
        catch (Exception $e)
        {
            return Status::Error($e, "mensaje");
        }
    }

    /**
     * Desactivar el menu seleccionado
     */
    public function DesactivarMenu(Request $request)
    {
        try
        {
            if ($request->t == 1) // Menu
            {
                $menu = ElementosMenu::find($request->id);
                $menu->tipo = 2;
                $menu->update();
            }
            else
            {
                $menu = ElementosSubmenu::find($request->id);
                $menu->tipo = 2;
                $menu->update();
            }
            return Status::Success();
        }
        catch (Exception $e)
        {
            return Status::Error($e, "-");
        }
    }
}
