<?php

namespace App\TIModels;

use Illuminate\Database\Eloquent\Model;

class MttoImpresora extends Model
{
    protected $fillable =
    [
        "impresora_id",
        "ubicacion",
        "fecha",
        "c",
        "m",
        "y",
        "k",
        "observaciones",
        "total_hojas"
    ];
    protected $table = "ti_impresoras_mtto";
}
