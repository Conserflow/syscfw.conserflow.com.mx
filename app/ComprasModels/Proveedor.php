<?php

namespace App\ComprasModels;

use Illuminate\Database\Eloquent\Model;


class Proveedor extends Model
{
    protected $fillable = [
        "razon_social",
        "categoria",
        "rfc",
        "ciudad",
        "estado",
        "regimen_fiscal",
        "nombre", "razon_social", "giro", "nacionalidad", "pagina",
        "calle", "no_exterior", "no_interior", "cp", "colonia", "municipio",
        "ventas_contacto", "ventas_telefono", "ventas_celular", "ventas_correo",
        "facturacion_contacto", "facturacion_telefono", "facturacion_celular",
        "facturacion_correo",
        "taxid",
        "tipos_modificacion","tipos_documentos"
    ];
    protected $table = "proveedores";
    public $timestamps = true;
}
