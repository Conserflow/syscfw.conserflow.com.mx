<?php

namespace App\AlmaceModels;

use Illuminate\Database\Eloquent\Model;

class GrupoInventario  extends Model
{
    protected $fillable = [
        "nombre",
        "emp_registra_id"
    ];
    protected $table = "grupos_inventario";
}
