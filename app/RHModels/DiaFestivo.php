<?php

namespace App\RHModels;

use Illuminate\Database\Eloquent\Model;

class DiaFestivo extends Model
{
    protected $fillable = [
        "dia",
        "descripcion"
    ];
    protected $table = "rh_dias_festivos";
    public $timestamps = true;
}
