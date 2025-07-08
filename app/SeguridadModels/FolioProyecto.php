<?php

namespace App\SeguridadModels;

use Illuminate\Database\Eloquent\Model;

class FolioProyecto extends Model
{
    protected $fillable = [
        "id",
        "proyecto_id",
        "nombre"
    ];
    protected $table = "seguridad_folios_proyectos";
    public $timestamps = true;
}
