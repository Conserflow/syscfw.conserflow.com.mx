<?php

namespace App\VehiculosModels;

use Illuminate\Database\Eloquent\Model;

class ConductoresImg extends Model
{
    protected $table = 'conductores_img';
    protected $fillable = [
        "conductor_id",
        "nombre",
        "condicion",
    ];
    public $timestamps = true;
}
