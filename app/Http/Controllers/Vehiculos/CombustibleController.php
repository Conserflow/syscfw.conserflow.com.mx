<?php

namespace App\Http\Controllers\Vehiculos;

use Illuminate\Http\Request;
use App\Exports\CombustibleExport;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Status;
use App\Http\Helpers\Utilidades;
use App\Proyecto;
use App\VehiculosModels\Combustible;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class CombustibleController extends Controller
{
  private $LIMIT = 3000;

  /**
   * Obtener los registros del combustible de la empresa seleccionada
   */
  public function Obtener($emp_id)
  {
    try
    {
      $combustible = DB::table("combustible as c")
        ->select(
          "c.*",
          "p.id as p_id",
          "p.nombre_corto",
          "u.id as u_id",
          "u.unidad",
          "u.placas",
          "u.modelo",
          "e.id as empleado_id",
          DB::raw("concat_ws(' ',e.nombre,e.ap_paterno,e.ap_materno) as operador"),
          "vp.id as vp_id",
          "vp.razon_social as vp_nombre"
        )
        // ->where("c.empresa", $emp_id)
        ->leftJoin("proyectos as p", "p.id", "c.proyecto_id")
        ->leftJoin("vehiculos_proveedores as vp", "vp.id", "c.proveedor_id")
        ->leftJoin("unidades as u", "u.id", "c.unidad_id")
        ->leftJoin("empleados as e", "e.id", "c.operador_id")
        ->orderBy("tipo_deposito")
        ->orderBy("folio", "desc")
        ->get();

      return Status::Success("combustible", $combustible);
    }
    catch (Exception $e)
    {
      return Status::Error($e, "obtener los registros de combustible");
    }
  }

  /**
   * Obtener las unidades
   */
  public function ObtenerUnidades()
  {
    try
    {
      $unidades = DB::table("unidades")
        ->select("id", "unidad", "placas")
        ->where("condicion", "!=", 0)
        ->get();
      return Status::Success("unidades", $unidades);
    }
    catch (Exception $e)
    {
      return Status::Error($e, "obtener las unidades");
    }
  }

  public function guardar(Request $request)
  {
    // Obtener último vale registrado
    $siguiente_folio = $request->folio;
    if ($siguiente_folio >= $this->LIMIT)
      return Status::Error2("Límite de folios alcanzado");
    try
    {
      if (!$request->ajax()) return redirect('/');
      if ($request->id == null) // Nuevo
      {
        // Buscar duplicados
        $existe = Combustible::where("folio", $request->folio)->first();

        if ($existe) return Status::Error2("El folio ya existe");
        $combustible = new Combustible($request->all());
        $combustible->empleado_registra_id = Auth::user()->empleado_id;
        $combustible->save();
      }
      else // Actu
      {
        $combustible = Combustible::find($request->id);
        $combustible->fill($request->all());
        $combustible->update();
      }

      if ($request->adjunto != null)
      {
        $combustible->adjunto = $this->guardarImagen($request->adjunto);
        $combustible->update();
      }
      return Status::Success();
    }
    catch (Exception $e)
    {
      return Status::Error($e, "guardar el registro");
    }
  }

  /**
   * Eliminar un vale de combustible
   */
  public function eliminar($id)
  {
    try
    {
      $combustible = Combustible::find($id);
      $combustible->condicion = 0;
      $combustible->update();
      return Status::Success();
    }
    catch (Exception $e)
    {
      return Status::Error($e, "eliminar el registro");
    }
  }

  /**
   * guardar imagen subida en el vale
   */
  public function guardarImagen($image)
  {
    try
    {
      // return response()->json($request);

      $image_64 = $image; //your base64 encoded data

      $extension = explode('/', explode(':', substr($image_64, 0, strpos($image_64, ';')))[1])[1];   // .jpg .png .pdf

      $replace = substr($image_64, 0, strpos($image_64, ',') + 1);

      // find substring fro replace here eg: data:image/png;base64,

      $image = str_replace($replace, '', $image_64);

      $image = str_replace(' ', '+', $image);

      $imageName = uniqid() . '.' . $extension;


      Storage::disk('local')->put('Trafico/' . $imageName, base64_decode($image));

      // return response()->json(['status' => true]);
      return $imageName;
    }
    catch (\Throwable $e)
    {
      Utilidades::errors($e);
    }
  }

  public function deleteImg($id)
  {
    try
    {
      $img = Combustible::where('id', $id)->first();
      $img->adjunto = '';

      $img_u = Storage::disk('local')->delete('Trafico/' . $img->adjunto);

      $img->save();
      return response()->json(['status' => true]);
    }
    catch (\Throwable $e)
    {
      Utilidades::errors($e);
    }
  }

  /**
   * Obtener la imagen del vale seleccionado
   */
  public function getImg($id)
  {
    try
    {
      $img = Combustible::where('id', $id)->first();
      $arreglo = [];
      // foreach ($img as $key => $value) {
      if (isset($img) == true)
      {
        if ($img->adjunto != '')
        {
          // code...
          $img_u = Storage::disk('local')->get('Trafico/' . $img->adjunto);
          $type = explode('.', $img->adjunto);
          $base64 = 'data:image/' . $type[1] . ';base64,' . base64_encode($img_u);

          $arreglo = [
            'id' => $id,
            'img' => $base64,
          ];
        }
      }

      return response()->json($arreglo);
    }
    catch (\Throwable $e)
    {
      Utilidades::errors($e);
    }
  }

  /**
   * Genera reporte en excel de combustible
   */
  public function Reporte($data)
  {
    try
    {
      $dts = explode("&", $data);
      $inicio = $dts[0];
      $fin = $dts[1];
      $ubicacion = $dts[2];
      ob_end_clean();
ob_start();
      $nombre = "Comsumo Combustible - " . ($ubicacion == 1 ? "Tehuacan" : "Coatzacoalcos") . ".xlsx";
      return Excel::download(
        new CombustibleExport($inicio, $fin, $ubicacion),
        $nombre
      );
    }
    catch (Exception $e)
    {
      Utilidades::errors($e);
      return view("errors.500");
    }
  }
}
