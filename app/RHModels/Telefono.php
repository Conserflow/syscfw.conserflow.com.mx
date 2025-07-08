<?php

namespace App\RHModels;

use Illuminate\Database\Eloquent\Model;

class Telefono extends Model
{
    protected $fillable = ["nombre", "empleado_id", "condicion"];
    protected $table = "telefonos_corporativos";
    public $timestamps = true;
}
