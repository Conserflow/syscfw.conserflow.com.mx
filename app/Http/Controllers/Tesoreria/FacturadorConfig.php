<?php

namespace App\Http\Controllers\Tesoreria;

class FacturadorConfig
{
    // TODO: Borrar _ST, ya no se usan
    /** Prueba */
    // Ruta de lib de facturación
    public static $PATH_PRUEBA = "/home/conserflow/cfdi/cfw";
    public static $PATH_PRUEBA_ST = "/home/conserflow/cfdi/cfw";
    // Archivo de facturación
    public static $CFDI_PRUEBA = "./cfdi_sellartimbrar_prueba.sh";
    public static $CFDI_CANCELAR_PRUEBA_ST = "./cfdi_cancelar_prueba.sh";
    // Storage para facturas de prueba
    public static $PATH_FACTURAS_PRUEBA = "Facturas2"; // Para Timbrar
    public static $PATH_FACTURAS_PRUEBA_ST = "Facturas2"; // Descargar xml, pdf y cancelar 

    /** Prod */
    // Ruta de lib de facturación
    public static $PATH_PROD = "/home/conserflow/cfdi/cfw";
    // Archivo de facturación
    public static $CFDI_PROD = "./cfdi_sellartimbrar_prod.sh";
    public static $CFDI_CANCELAR_PROD_ST = "./cfdi_cancelar_prueba.sh";
    // Storage para facturas de prueba
    public static $PATH_FACTURAS_PROD = "Facturas"; // Para timbrar
    public static $PATH_FACTURAS_PROD_ST = "Facturas"; // Descargar xml, pdf y cancelar 
}

