<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PartidaRe extends Model
{
    protected $fillable = [
        'requisicione_id',
        'articulo_id',
        'servicio_id',
        'peso',
        'unidad_peso',
        'cantidad',
        'equivalente',
        'fecha_requerido',
        'comentario',
        'cantidad_compra',
        'cantidad_almacen',
        'condicion',
        'pda',
        'produccion',
        'vehiculo_id',
        'precio_compras',
        "empleado_registra_id"
    ];
    protected $table = 'partidas_requisiciones';
    public $timestamps = true;
}
