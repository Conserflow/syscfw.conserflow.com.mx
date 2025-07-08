<?php

namespace App\TIModels;

use Illuminate\Database\Eloquent\Model;

class HistoricoServicioTI extends Model
{
    protected $table = 'ti_historico_servicio';
    protected $fillable = [
        "tipo",
        "empleado_id",
        "nombre_usuario",
        "problema_servicio",
        "fecha_reporte",
        "solucion",
        "fecha_solucion",
        "reincidencia",
        "condicion",
        "empleado_realiza_id"
    ];
    public $timestamps = true;
}
