<?php

namespace App\SGIModels;

use Illuminate\Database\Eloquent\Model;

class Departamento extends Model
{
    protected $table = "sgi_salidas_deptos";
    protected $fillable =
    [
        "nombre"
    ];
}
