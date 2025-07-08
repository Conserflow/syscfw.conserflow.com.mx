<?php

namespace App\RequisicionModels;

use Illuminate\Database\Eloquent\Model;

class UnidadMedida extends Model
{
    protected $fillable = ["nombre"];
    protected $table = "articulos_unidades_medida";

    /************* SCOPES ************ */

    public function scopeSinServicio($query)
    {
        return $query->where("id", ">", 1);
    }
}
