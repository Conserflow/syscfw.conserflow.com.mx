<?php

namespace App\RHModels;

use Illuminate\Database\Eloquent\Model;

class Contacto extends Model
{
    protected $fillable = [
        "empleado_id",
        "correo_electronico",
        "tel_celular",
        "tel_casa",
        "tel_emergencia",
        "contacto_emergencia",
    ];
    public $timestamps = false;
    protected $table = "contacto_empleados";
}
