<?php

namespace App\VehiculosModels;

use Illuminate\Database\Eloquent\Model;

class Proveedor extends Model
{
    protected $fillable = [
        "nombre",
		"razon_social",
		"rfc",		
		"taxid",		
    ];
    protected $table = "vehiculos_proveedores";
    public $timestamps = true;
}

    