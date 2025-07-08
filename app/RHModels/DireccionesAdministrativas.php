<?php

namespace App\RHModels;

use Illuminate\Database\Eloquent\Model;

class DireccionesAdministrativas extends Model
{
    protected $fillable = ["nombre"];
    protected $table = "direcciones_administrativas";
    public $timestamps = true;
}
