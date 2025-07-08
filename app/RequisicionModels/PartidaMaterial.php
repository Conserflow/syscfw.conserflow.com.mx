<?php

namespace App\RequisicionModels;

use App\RequisicionModels\UnidadMedida;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PartidaMaterial extends Model
{
    use SoftDeletes;

    protected $fillable =
    [
        "requi_id",
        "concepto",
        "comentarios",
        "marca",
        "tipo",
        "cantidad",
        "um_id",
        "documentos_requeridos",
    ];

    protected $table = "requisicion_materiales_partidas";


    /************* RELACIONES *************/
    public function unidadMedida()
    {
        return $this->belongsTo(UnidadMedida::class, "um_id");
    }

    /************* SCOPES *************/

    /**
     * Buscar por Id de requisicion
     * @param $query Query
     * @param $requi_id Number Id de la requisicion
     */
    public function scopeByRequisicion($query, $requi_id)
    {
        return $query->where("requi_id", $requi_id);
    }

    /**
     * Obtener solo los que son de tipo material
     */
    public function scopeSoloMateriales($query)
    {
        return $query->where("tipo", 1);
    }

    /**
     * Obtener los que estan pendientes de cantidad de almacen
     */
    public function scopePendienteAlmacen($query)
    {
        return $query->whereNull("cantidad_almacen");
    }
}
