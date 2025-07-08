<?php

namespace App\RHModels;

use Illuminate\Database\Eloquent\Model;

class Correo extends Model
{
    protected $fillable = [
        "nombre",
        "empleado_id"
    ];
    protected $table = "correos_corporativos";
    public $timestamps = true;
}
