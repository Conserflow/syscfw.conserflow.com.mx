<?php

namespace App\TIModels;

use Illuminate\Database\Eloquent\Model;

class ConsumibleMtto extends Model
{
    protected $fillable = [
        "tipo_mtto", // 1. Preventivo 2. Correctivo
        "consumible_id", // Id consumible
        "mtto_id" // Id de mmto
    ];
    protected $table = 'ti_consumible_mtto';
}
