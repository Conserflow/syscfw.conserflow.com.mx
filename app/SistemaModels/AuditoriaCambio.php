<?php

namespace App\SistemaModels;

use Illuminate\Database\Eloquent\Model;

class AuditoriaCambio extends Model
{
    protected $fillable =
    [
        "model_id",
        "fecha_hora",
        "usuario",
        "modelo",
        "nuevo",
        "cambios"
    ];
    protected $table = "sistema_auditoria_registros";
    public $timestamps = false;
}
