<?php

namespace App;

use Illuminate\Database\Eloquent\Model;


class PartidaEntrada extends Model
{
    protected $fillable =['entrada_id','req_com_id','articulo_id','validacion_calidad','caducidad','cantidad','stocke_id','almacene_id','nivel_id',
    'stand_id','pendiente','status','precio_unitario','factura_id','numero_serie','cantidad_rechazo'];
    protected $table = 'partidas_entradas';
    public $timestamps = false;


}
