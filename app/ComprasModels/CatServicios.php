<?php

namespace App\ComprasModels;

use Illuminate\Database\Eloquent\Model;

class CatServicios extends Model
{
    protected $fillable = [
        'nombre_servicio',
        'proveedor_marca',
        'unidad_medida',
        'centro_costos_id'
    ];
    protected $table = 'servicios';
    public $timestamps = true;
}
