<?php

namespace App\TIModels;

use Illuminate\Database\Eloquent\Model;

class TiConsumible extends Model
{
    protected $fillable = ["nombre"];
    protected $table = "ti_consumibles";
    public $timestamps=false;
}
