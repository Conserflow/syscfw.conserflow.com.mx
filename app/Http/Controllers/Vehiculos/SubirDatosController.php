<?php

namespace App\Http\Controllers\Vehiculos;

use App\Http\Controllers\Controller;
use App\VehiculosModels\Combustible;
use App\VehiculosModels\Unidades;
use App\VehiculosModels\UnidadPropio;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Rap2hpoutre\FastExcel\FastExcel;

class SubirDatosController extends Controller
{
    private $TOKEN = "Sur^gtWCD*u";
    /**
     * Carga los datos de combustible desde xls
     */
    public function CargarCombustible(Request $request)
    {
        $i = 0;
        try
        {
            DB::beginTransaction();
            // Cargar archivo
            $datos = (new FastExcel)->import($request->file("datos_combustible")->getRealPath());
            // dd(count($datos));
            foreach ($datos as $index => $r)
            {
                $i = $index;
                // Llenar datos
                // Cancelado
                if ($r["condicion"] == 0)
                {
                    $combustible = new Combustible($r);
                    $combustible->fecha = date("Y-m-d");
                    $combustible->empleado_registra_id = 0;
                    $combustible->producto_id = 1;
                    $combustible->unidad_id = -99; // CANCELADO
                    $combustible->save();
                }
                else
                {
                    if ($r["placas"] === 0)
                    {
                        // Bidon
                        $unidad_id = -1;
                    }
                    else
                    {
                        $unidad = Unidades::where("placas", $r["placas"])->first();
                        $unidad_id = $unidad->id;
                    }
                    $combustible = new Combustible($r);
                    if ($r["modelo"] === "BIDON")
                    {
                        $combustible->cantidad_bidones = 1;
                    }
                    $combustible->empleado_registra_id = 0;
                    $combustible->unidad_id = $unidad_id;
                    $combustible->save();
                }
            }

            // DB::rollBack();
            DB::commit();
            echo "Ok";
        }
        catch (Exception $e)
        {
            DB::rollBack();
            echo $i;
            dd($e);
        }
    }

    /**
     * Actualizar la fecha de los vales cancelados
     */
    public function RegistrarCancelados(Request $request)
    {
        try
        {
            DB::beginTransaction();
            $datos = (new FastExcel)->import($request->file("datos_combustible")->getRealPath());
            foreach ($datos as $r)
            {
                // comprobar que el folio exista
                $vale = Combustible::where("folio", $r["folio"])->first();
                if ($vale != null)
                {
                    $vale->fecha = $r["fecha"];
                    $vale->update();
                }
            }
            DB::commit();
            echo "ok";
        }
        catch (Exception $e)
        {
            DB::rollBack();
            dd($e);
        }
    }

    /**
     * Cargar las unidades 
     */
    public function CargarUnidades(Request $request)
    {
        try
        {
            $asd = ($request->TOKEN === $this->TOKEN);
            if (!$asd) return "nel prro";
            DB::beginTransaction();
            $unidades = (new FastExcel)->import($request->file("unidades")->getRealPath());
            $placas_existe = "";
            $aux = [];
            $si = 0;
            $no = 0;
            foreach ($unidades as $i => $u)
            {
                if ($i >= 139) break;
                $aux = $i;
                // Comprobar que no exista
                $existe = DB::table("unidades as u")->where("placas", $u["PLACAS"])->first();
                if ($existe != null) // Ya existe
                {
                    $si++;
                    continue;
                }
                else
                {
                    $placas_existe .= $u["PLACAS"] . "<br>";
                    $no++;
                }
                $unidad = new Unidades();
                $unidad->unidad = $u["UNIDAD"];
                $unidad->marca = $u["MARCA"];
                $unidad->modelo = $u["MODELO"];
                $unidad->color = $u["COLOR"];
                $unidad->no_motor = $u["NO. MOTOR"];
                $unidad->capacidad = $u["CAPACIDAD DE CARGA"];
                $unidad->anio = $u["ANIO"];
                $unidad->placas = $u["PLACAS"];
                $unidad->estado = $u["ESTADO"];
                $unidad->comentarios = $u["COMENTARIOS"];
                $unidad->tipo = 1; // Propio
                $unidad->clase_tipo = $u["CLASE Y TIPO"];
                $unidad->cilindros = $u["CILINDROS"];
                $unidad->combustible = $u["COMBUSTIBLE"];
                $unidad->numero_tarjeta_circulacion = $u["NO TARJETA CIRCULACION"];
                $unidad->tarjeta = "-"; // PDF
                $unidad->numero_serie = $u["NO SERIE"];

                $unidad->excento = 1; // 
                $unidad->primer_semestre = "Enero-Febrero";
                $unidad->segundo_semestre = "Julio-Agosto";
                $unidad->save();
                // Unidades propio
                $unidad->empresa = $u["EMPRESA"];
                $propietario = new UnidadPropio();
                $propietario->unidad_id = $unidad->id;
                $propietario->propietario = $u["PROPIETARIO"];
                $propietario->save();
            }
            // DB::rollBack();
            DB::commit();
            echo $placas_existe . "<br>";
            echo "Si: $si <br>No: $no <br>";
            return "ok";
        }
        catch (Exception $e)
        {
            print_r($aux);
            print_r($u);
            DB::rollBack();
            dd($e);
        }
    }
}
