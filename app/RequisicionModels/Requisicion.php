<?php

namespace App\RequisicionModels;

use App\Proyecto;
use App\RequisicionModels\Tipo;
use App\RHModels\Empleado;
use App\SGIModels\Departamento;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Requisicion extends Model
{
    public static $ELIMINADO = 0;
    public static $NUEVO = 1;
    public static $VALIDACION_ALMACEN = 2;
    public static $RECHAZADO_ALMACEN = 3;
    public static $VALIDACION_SUPERVISA = 4;
    public static $RECHAZADO_SUPERVISA = 5;
    public static $APROBADO = 6;
    public static $NO_APROBADO = 7;

    use  SoftDeletes;

    protected $table = "requisiciones2";

    protected $fillable = [
        "area_id",
        "lugar_entrega",
        "fecha_entrega",
        "empleado_solicita_id",
        "empleado_aprueba_id",
    ];


    /************* RELACIONES *************/
    public function tipo()
    {
        return $this->belongsTo(Tipo::class, "tipo_id");
    }

    public function proyecto()
    {
        return $this->belongsTo(Proyecto::class, "proyecto_id");
    }

    public function solicita()
    {
        return $this->belongsTo(Empleado::class, "empleado_solicita_id");
    }

    public function aprueba()
    {
        return $this->belongsTo(Empleado::class, "empleado_aprueba_id");
    }

    public function area()
    {
        return $this->belongsTo(Departamento::class, "area_id");
    }

    /************* SCOPES *************/
    public function scopeWithRelations($query)
    {
        return $query->with([
            "tipo:id,nombre,ruta",
            "proyecto:id,nombre_corto",
            "solicita" => function ($query)
            {
                $query->selectRaw(
                    "id,CONCAT_WS(' ',nombre,ap_paterno,ap_materno) as raw_nom_solicita,nombre,ap_paterno,ap_materno"
                );
            },
            "area:id,nombre",
            "aprueba" => function ($query)
            {
                $query->selectRaw("id,CONCAT_WS(' ',nombre,ap_paterno,ap_materno) as raw_nom_solicita,nombre,ap_paterno,ap_materno");
            }
        ]);
    }

    public function scopeById($query, $id)
    {
        return $query->where("id", $id);
    }

    public function scopeByProyecto($query, $proyecto_id)
    {
        return $query->where("proyecto_id", $proyecto_id);
    }

    /**
     * Aumentar el numero de la revision actual
     */
    public function CambiarRevision()
    {
        $nuevo = intval($this->revision) + 1;
        $this->revision = str_pad($nuevo, 2, "0", STR_PAD_LEFT);
    }

    /**
     * Obtener las requisiciones pendientes por aprobar de almacen
     */
    public function scopePendientesAlmacen($query)
    {
        return $query->where("requisiciones2.condicion", Requisicion::$VALIDACION_ALMACEN);
    }

    /**
     * Obtener las requisiciones pendientes por aprobar de almacen
     */
    public function scopePendientesSupervisor($query)
    {
        return $query->where("requisiciones2.condicion", Requisicion::$VALIDACION_SUPERVISA);
    }
}
