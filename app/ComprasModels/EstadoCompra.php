<?php

namespace App\ComprasModels;

use Illuminate\Database\Eloquent\Model;

class EstadoCompra extends Model
{
    protected $fillable = ["nombre", "empleado_registra_id"];
    protected $table = 'estado_compras';
    public $timestamps = true;
}
