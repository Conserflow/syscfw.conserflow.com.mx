<?php

namespace App\Http\Controllers\APISistema;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Helpers\Utilidades;
use Exception;
use Illuminate\Support\Facades\DB;

class ControlW extends Controller
{
    /**
     * Comprueba la contraseña del control de USB
     */
    public function Auth(Request $request)
    {
        try
        {
            $user = DB::table("api_control_auth as a")
                ->first();
            $chec_auth = $this->GetCheckSum($user->password); // DB
            $chec_pass = $this->GetCheckSum($request->password); // Req
            $valid = $chec_auth == $chec_pass;
            return response()->json([
                "status" => true,
                "checksum" => $chec_pass,
                "valid" => $valid,
                "message" => $valid ? "Ok" : "Contraseña incorrecta"
            ]);
        }
        catch (Exception $e)
        {
            Utilidades::errors($e, 2, "SUB");
            return response()->json([
                "status" => false,
                "checksum" => 0,
                "valid" => false,
                "message" => $e->getMessage()
            ]);
        }
    }

    /**
     * Validar pass
     */
    public function GetCheckSum($string)
    {
        try
        {
            $n = 0;
            $arr = str_split($string);
            foreach ($arr as $s)
            {
                $n += ord($s);
            }
            return $n;
        }
        catch (Exception $e)
        {
            dd($e);
        }
    }
}
