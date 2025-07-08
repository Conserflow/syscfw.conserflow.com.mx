<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SatCatProdSer extends Model
{
      protected $table = 'sat_cat_prodser';
      protected $fillable = [
            "clave",
            "descripcion",
            "fecha_ini_vig",
            "fecha_fin_vig",
            "incluir_iva_tras",
            "incluir_ieps_tras",
            "complemento",
      ];

      public $timestamps = true;
}
