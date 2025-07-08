<?php

namespace App\RHModels;

use Illuminate\Database\Eloquent\Model;

class TipoNomina extends Model
{
    protected $fillable = ["nombre"];
    protected $table = "tipo_nomina";
}
