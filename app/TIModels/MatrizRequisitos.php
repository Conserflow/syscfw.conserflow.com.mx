<?php

namespace App\TIModels;

use Illuminate\Database\Eloquent\Model;

class MatrizRequisitos extends Model
{
    protected $fillable = [
		"puesto",
		"puesto_jefe_id",
		"software",
		"equipo",
		"accesorios",
		"impresora",
		"red",
		"otro"
    ];
    protected $table = "ti_matriz_requisitos";
    public $timestamps = true;
}
    