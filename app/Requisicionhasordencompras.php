<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Requisicionhasordencompras extends Model
{
     protected $fillable = [
       'requisicione_id',
      'orden_compra_id',
      'articulo_id',
      'servicio_id',
      'cantidad',
      'precio_unitario',
      'condicion',
      'comentario',
      'antigua',
      'vehiculo_id',
      'imagen','asociada'
    ];
    protected $table = 'requisicion_has_ordencompras';
    public $timestamps = false;
}
