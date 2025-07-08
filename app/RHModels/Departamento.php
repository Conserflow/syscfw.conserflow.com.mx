<?php

namespace App\RHModels;

use Illuminate\Database\Eloquent\Model;

class Departamento extends Model
{
    protected $fillable = [
        "nombre",
		"direccion_administrativa_id"		
    ];
    protected $table = "departamentos";
    public $timestamps = true;
}

    