<?php

namespace App\VehiculosModels;

use Illuminate\Database\Eloquent\Model;

class CatalogoTrafico extends Model
{
    //
    protected $fillable = ["operacion_id", "nombre"];
    protected $table = "catalogo_trafico";
    public $timestamps = false;
}
