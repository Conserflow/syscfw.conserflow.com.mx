<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PartidasFacturasPagos extends Model
{
    protected $fillable = [
        'factura_id',
        'num_parcialidad',
        'saldo_anterior',
        'importe_pagado',
        'saldo_insoluto',
        'partidas_facturas_id',
        'uuid',
        'serie',
        'folio',
        "total_usd",
        "tipo_cambio_dr",
        "metodo_pago_dr",
        "importe_pagado_mx",
        "pago_dolares",
        "obj_imp"
    ];
    protected $table = 'partidas_facturas_pagos';
    public $timestamps = false;
}
