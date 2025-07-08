<?php

namespace App\RHModels;

use Illuminate\Database\Eloquent\Model;

class Puesto extends Model
{
    protected $fillable =
    [
        "nombre",
        "area",
        "departamento_id",
        "nivel_o"
    ];
    protected $table = 'puestos';
    public $timestamps = false;
}
