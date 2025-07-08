<?php

namespace App\RHModels;

use Illuminate\Database\Eloquent\Model;

class Referencia extends Model
{
    protected $fillable = [
        "nombre",
        "ocupacion",
        "direccion",
        "telefono",
        "empleado_id",
        "condicion"
    ];
    protected $table = "referencias_conocidos";
    public $timestamps = true;
}
