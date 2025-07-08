<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Proyecto extends Model
{
	protected $fillable = [
		'nombre',
		'nombre_corto',
		"cotizacion",
		'monto_total',
		'clave',
		'ciudad',
		'fecha_inicio',
		'fecha_fin',
		'adicional',
		'pm_cliente',
		'pm_interno',
		'cliente_id',
		'proyecto_id',
		'proyecto_subcategorias_id',
		'documento_po',
		"moneda"
	];

	/********************************
	 * SCOPES
	 *******************************/
	public function scopeActivos($query)
	{
		return $query->where("proyectos.condicion", 1);
	}
}
