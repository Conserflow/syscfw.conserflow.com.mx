<?php

namespace App\Http\Controllers\SGI;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Status;
use Exception;
use Illuminate\Http\Request;

class ProcedimientosController extends Controller
{

    public function ObtenerDirectorios(Request $request)
    {
        $ruta = $request->path;
        $PATH = "app/SGI/" . $ruta;
        try
        {
            // Obtener los directoios y archivos del root;
            $path = storage_path($PATH);
            $dir = scandir($path);
            unset($dir[0]); // Eliminar .
            unset($dir[1]); // Eliminar ..
            $files = [];
            foreach ($dir as $f)
            {
                // Ignorar .ini
                if ((bool)strpos($f, ".ini")) continue;
                // Ignorar temp 
                if ((bool)str_starts_with($f, "~$")) continue;
                $type = "dir";
                $isFile = (bool) (strpos($f, ".pdf") or
                    strpos($f, ".xlsx") or
                    strpos($f, ".docx") or
                    strpos($f, ".jpg")
                );

                $len = strlen($f);
                $punto = strpos(strrev($f), ".");
                $sub = substr($f, ($len - $punto));
                $type = $isFile ? $sub : "dir";

                $files[] = [
                    "file" => $isFile,
                    "path" => $ruta . DIRECTORY_SEPARATOR . $f,
                    "name" => strlen($f) >= 75 ? substr($f, 0, 75) . "..." : $f,
                    "full_name" => $f,
                    "type" => $type
                ];
            }
            return Status::Success("files", $files);
        }
        catch (Exception $e)
        {
            return Status::Error($e, "obtener los documentos");
            // dd($e);
        }
    }
}
