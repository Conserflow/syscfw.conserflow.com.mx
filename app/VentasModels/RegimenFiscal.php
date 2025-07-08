<?php

namespace App\VentasModels;

use Illuminate\Database\Eloquent\Model;

class RegimenFiscal extends Model
{
    protected $table = "ventas_catalogos_regimen_fiscal";
    protected $fillable = [
        "clave",
        "nombre"
    ];
    public $timestamps = false;
}
