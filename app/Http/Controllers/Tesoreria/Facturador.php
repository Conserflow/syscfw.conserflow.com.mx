<?php

namespace App\Http\Controllers\Tesoreria;

use Barryvdh\DomPDF\Facade as PDF;
use App\DatosGenerales;
use App\Factura;
use App\Http\Helpers\Utilidades;
use App\PartidasFactura;
use App\PartidasFacturasPagos;
use App\Relacionados;
use App\VentasModels\Clientes;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class Facturador
{
    private $ES_PRUEBA = true;

    // Propiedades del comprobante
    private $id = 0;
    private $factura = null;
    private $partidas = null;
    private $partidas_pagos = null;

    // El valor reportado no coincide con el esperado
    private $error_suma_impuesto = 0.0;
    private $correccion_total = 0.0;
    private $correccion_impuesto = 0.0;

    public function __construct($id, $prueba = true)
    {
        $this->id = $id;
        // FIXME: cambiar
        $this->ES_PRUEBA = $prueba;
        $this->factura = Factura::where('factura.id', $id)
            ->join('clientes AS c', 'c.id', 'factura.cliente_id')
            ->join('datosgenerales AS dg', 'dg.id', 'factura.emisor_id')
            ->join('sat_cat_formapago AS scfp', 'scfp.id', 'factura.formapago')
            ->join('sat_cat_metodopago AS scmp', 'scmp.id', 'factura.metodopago')
            ->join('sat_cat_monedas AS scm', 'scm.id', 'factura.moneda_id')
            ->join('sat_cat_tipofactura AS sctf', 'sctf.id', 'factura.tipo_factura_id')
            ->join('sat_cat_usocfdi AS scuc', 'scuc.id', 'factura.uso_factura')
            ->leftJoin('sat_cat_tiporelacion AS sctr', 'sctr.id', 'factura.tipo_relacion')
            ->leftJoin('factura AS f', 'f.id', 'factura.factura_id')
            ->select(
                'factura.*',
                'c.nombre AS c_nombre',
                'c.rfc AS c_rfc',
                'c.codigo_postal as c_codigo_postal',
                'c.regimen_fiscal as c_regimen_fiscal',
                'dg.rfc AS dg_rfc',
                'dg.razon_social AS dg_razon_social',
                'dg.regimenfiscal AS dg_regimenfiscal',
                'scfp.clave AS scfp_clave',
                'scmp.c_metodopago AS scmp_c_metodopago',
                'scm.c_moneda AS scm_c_moneda',
                'sctf.c_tipofactura AS sctf_c_tipofactura',
                'scuc.c_uso AS scuc_c_uso',
                'sctr.c_tiporelacion AS tiporelacion_cod',
                'f.uuid AS uuid_relacionado_f'
            )
            ->first();
        // Partidas
        $this->partidas = PartidasFactura::where('factura_id', $id)
            ->leftJoin('sat_cat_prodser AS scps', 'scps.id', 'partidas_factura.productos_servicios_id')
            ->leftJoin('sat_cat_unidades AS scu', 'scu.id', 'partidas_factura.unidad_id')
            ->select('partidas_factura.*', 'scps.clave AS scps_clave', 'scu.c_unidad AS scu_c_unidad')
            ->get();
        $this->partidas_pagos = DB::table("partidas_facturas_pagos as pfp")
            ->where('pfp.partidas_facturas_id', $this->partidas[0]->id)
            ->leftJoin("sat_cat_metodopago as scm", "scm.id", "pfp.metodo_pago_dr")
            ->select(
                'pfp.*',
                "scm.c_metodopago"
            )
            ->get();
    }

    /**
     * Timbrar factura
     */
    public function Timbrar()
    {
        try
        {
            //FIXME: Usar datos globales, no por parametro
            $factura = $this->factura;
            $partidas = $this->partidas;
            $partidas_pagos = $this->partidas_pagos;

            $path_facturas = $this->ES_PRUEBA ? FacturadorConfig::$PATH_FACTURAS_PRUEBA : FacturadorConfig::$PATH_FACTURAS_PROD;
            $path_cdfi = $this->ES_PRUEBA ? FacturadorConfig::$PATH_PRUEBA : FacturadorConfig::$PATH_PROD;
            $file_cdfi = $this->ES_PRUEBA ? FacturadorConfig::$CFDI_PRUEBA : FacturadorConfig::$CFDI_PROD;

            $date = date("m.d.y");
            $file = $this->id . "-$date"; // Nombre del comprobante
            $ini_file_str = $this->Principal($factura, $partidas, $partidas_pagos);
            // dd($ini_file_str);
            Storage::disk('local')->put($path_facturas . '/' . $file . ".ini", $ini_file_str);
            $dir_file = storage_path("app/" . $path_facturas); // Ruta de los comprobantes
            $output = shell_exec(
                "cd $path_cdfi;$file_cdfi $dir_file $file 2>error.txt;cat error.txt"
            );
            if ($output != null) return ['status' => false, 'mensaje' =>  $output]; // ERROR
            // Guardar resultado
            $this->GuardarFactura($date); // Prueba

            return ['status' => true];
        }
        catch (Exception $e)
        {
            dd($e);
        }
    }

    public function GuardarFactura($date)
    {
        try
        {
            $estado = $this->ES_PRUEBA ? 9 : 1; // Prueba|Prod
            $suma_subtotal = 0;
            $suma_descuento = 0;
            $suma_impuesto = 0;
            // FIXME: Calcular impuestos, total al inicio
            foreach ($this->partidas as $p)
            {
                $suma_subtotal += $p->importe;
                $suma_descuento += $p->descuento;
                $suma_impuesto += ($p->importe * $p->impuesto_iva);
            }
            $total_pagos_f = 0;
            foreach ($this->partidas_pagos as $pp)
            {
                $total_pagos_f += $pp->importe_pagado;
            }
            $facturas = Factura::where('id', $this->id)->first();
            $facturas->timbrado = $estado; // Timbrado normal o prueba
            $facturas->archivo = $this->id . '-' . $date;
            $facturas->total = $this->factura->tipo_factura_id == 4 ?
                $total_pagos_f : (($suma_subtotal - $suma_descuento) + $suma_impuesto);
            $facturas->update();

            $path_facturas = $this->ES_PRUEBA ? FacturadorConfig::$PATH_FACTURAS_PRUEBA :
                FacturadorConfig::$PATH_FACTURAS_PROD;
            // Buscar el uuid y se agreaga en la base de datos
            $xml_string = Storage::disk('local')->get($path_facturas . '/' . $facturas->archivo . 'tim.xml');
            $xml = simplexml_load_string($xml_string);
            $ns = $xml->getNamespaces(true);
            $xml->registerXPathNamespace('c', $ns['cfdi']);
            $xml->registerXPathNamespace('t', $ns['tfd']);

            foreach ($xml->xpath('//t:TimbreFiscalDigital') as $tfd)
            {
                $uuid = $tfd['UUID'];
            }
            $facturas->uuid = $uuid;
            $facturas->update();
        }
        catch (Exception $e)
        {
            Utilidades::errors($e);
            dd($e);
        }
    }
    /**
     * Creación del archivo para timbrar la factura
     * @param object $factura Factura del comprobante
     * @param array $partida Partidas de la factura
     * @param array $partidas_pagos Partidas de pago de la factura
     */
    private function Principal($factura, $partida, $partidas_pagos)
    {
        if ($factura->sctf_c_tipofactura == 'E')
            $val = $this->ComprobanteRelacionados($factura);
        else if ($factura->tipo_relacion != '')
            $val = $this->ComprobanteRelacionados($factura);
        else if ($factura->tipo_relacion != '')
            $val = $this->ComprobanteRelacionados($factura);
        else $val = "";
        $val .= PHP_EOL;

        $variable  = ';ini' . PHP_EOL . ';encoding=utf8' . PHP_EOL . PHP_EOL .
            '[cfdi:Comprobante]' . PHP_EOL . $this->Comprobante($factura, $partida) . PHP_EOL .
            $val . PHP_EOL .
            '[cfdi:Comprobante/cfdi:Emisor]' . PHP_EOL .
            $this->ComprobanteEmisor($factura->emisor_id) . PHP_EOL .
            '[cfdi:Comprobante/cfdi:Receptor]' . PHP_EOL .
            $this->ComprobanteReceptor($factura->cliente_id, $factura->scuc_c_uso) . PHP_EOL .
            '[cfdi:Comprobante/cfdi:Conceptos]' . PHP_EOL .
            $this->ComprobanteConceptos($factura, $partida) . PHP_EOL . PHP_EOL .
            ($factura->sctf_c_tipofactura == 'P' ?
                '[cfdi:Comprobante/cfdi:Complemento]' . PHP_EOL .
                $this->ComprobanteComplemento($factura, $partidas_pagos) :
                '[cfdi:Comprobante/cfdi:Impuestos]' . PHP_EOL . PHP_EOL .
                $this->TotalImpuestos($partida)) . PHP_EOL . PHP_EOL .
            ($factura->adenda == NULL ? '' : $this->Adenda($factura, $partida));

        return $variable;
    }

    /**
     * [cfdi:Comprobante] Comprobante de la factura
     * @param object $factura Factura
     * @param array $partidas Partidas de la factura
     */
    private function Comprobante($factura, $partidas)
    {
        $suma_subtotal = 0;
        $suma_descuento = 0;
        $suma_impuesto = 0;
        $suma_retencion = 0;
        $to = $factura->tipo_cambio == 1.00 ? '1' : $factura->tipo_cambio;
        foreach ($partidas as $p)
        {
            // Si es obj de imp. suma impuestos al subtotal
            if ($p->obj_imp == "02")
            {
                $suma_impuesto += ($p->importe * $p->impuesto_iva);
            }
            $suma_subtotal += $p->importe;
            $suma_descuento += $p->descuento;
            if ($p->retencion > 0)
            {
                $suma_retencion += ($p->importe * $p->retencion);
            }
        }

        // El valor reportado no coincide con el esperado
        $suma_impuesto += $this->error_suma_impuesto;
        $fecha_e = substr($factura->fecha_hora, 0, 4) . '-' .
            substr($factura->fecha_hora, 5, 2) . '-' .
            substr($factura->fecha_hora, 8, 2) . 'T' .
            substr($factura->fecha_hora, 11, 8);
        $t = ((($suma_subtotal - $suma_descuento) + round($suma_impuesto, 2)) - round($suma_retencion, 2));
        $t += $this->correccion_total;
        $comprobante = "xmlns:cfdi = http://www.sat.gob.mx/cfd/4" . PHP_EOL .
            "xmlns:xsi = http://www.w3.org/2001/XMLSchema-instance" . PHP_EOL .
            "xmlns:pago20=http://www.sat.gob.mx/Pagos20" . PHP_EOL .
            "xsi:schemaLocation = http://www.sat.gob.mx/cfd/4 " .
            "http://www.sat.gob.mx/sitio_internet/cfd/4/cfdv40.xsd " .
            "http://www.sat.gob.mx/Pagos20 " .
            "http://www.sat.gob.mx/sitio_internet/cfd/Pagos/Pagos20.xsd" . PHP_EOL .
            "Version = 4.0" . PHP_EOL .
            "Serie = " . $factura->serie . PHP_EOL .
            "Folio = " . $factura->folio . PHP_EOL .
            "Fecha = " . $fecha_e . PHP_EOL .
            ($factura->sctf_c_tipofactura == "P" ? "" : "FormaPago = " . $factura->scfp_clave) . PHP_EOL .
            "CondicionesDePago = " . $factura->condicion_pago . PHP_EOL . //
            "SubTotal = " . $suma_subtotal . PHP_EOL .
            ($factura->sctf_c_tipofactura == "P" ? "" : "Descuento = " . $suma_descuento) . PHP_EOL .
            ($factura->sctf_c_tipofactura == "P" ? "Moneda = XXX" : "Moneda = " . $factura->scm_c_moneda) . PHP_EOL .
            ($factura->sctf_c_tipofactura == "P" ? "" : "TipoCambio = " . $to) . PHP_EOL .
            // TODO: Corregir
            "Total = " . $t . PHP_EOL .
            "TipoDeComprobante = " . $factura->sctf_c_tipofactura . PHP_EOL .
            ($factura->sctf_c_tipofactura == "P" ? "" : "MetodoPago = " . $factura->scmp_c_metodopago) . PHP_EOL .
            "LugarExpedicion = " . $factura->codigo_postal . PHP_EOL .
            "Exportacion =" . $factura->exportacion;
        return $comprobante;
    }

    /**
     * [cfdi:CfidRelacionados] Documentos relacionados del Comprobante
     * @param object $factura Factura
     */
    private function ComprobanteRelacionados($factura)
    {
        $relacionados = "";
        $relacionados_f = DB::table("facturas_relacionadas")
            ->where("factura_id", $factura->id)->get();
        if (count($relacionados_f) > 0)
        {
            $relacionados = "[cfdi:Comprobante/cfdi:CfdiRelacionados]" . PHP_EOL .
                "TipoRelacion =" . $factura->tiporelacion_cod . PHP_EOL;
            foreach ($relacionados_f as $r)
            {
                $relacionados .=  "[cfdi:Comprobante/cfdi:CfdiRelacionados/cfdi:CfdiRelacionado]" .
                    PHP_EOL . "UUID =" . $r->uuid . PHP_EOL;
            }
        }
        return $relacionados;
    }

    /**
     * [cfdi:Emisor] Datos del Emisor
     * @param int $id Id del emisor
     */
    private function ComprobanteEmisor($id)
    {
        // TODO:
        if ($this->ES_PRUEBA) // Datos de prueba
        //if (true) // Datos de prueba
        {
            $emisor = DatosGenerales::find($id);
            $razon = "XENON INDUSTRIAL ARTICLES";
            $rfc = "XIA190128J61";
            $regimen = "601";
        }
        else
        {
            $emisor = DatosGenerales::find($id);
            $razon = $emisor->razon_social;
            $rfc = $emisor->rfc;
            $regimen = explode(' ', $emisor->regimenfiscal)[0]; // Obtener solo el codigo
        }
        $emisor_s =
            "Rfc = " . $rfc . PHP_EOL .
            "Nombre = " . $razon . PHP_EOL .
            "RegimenFiscal = " . $regimen;

        return $emisor_s;
    }

    /**
     * [cfdi:Receptor] Datos del Receptor
     * @param int $id Id del Receptor
     * @param int $uso_cfdi Id del uso de CFDI
     */
    private function ComprobanteReceptor($id, $uso_cfdi)
    {
        $cliente = Clientes::where("id", $id)->first();
        $receptor =
            "Rfc = " . $cliente->rfc . PHP_EOL .
            "Nombre = " . $cliente->nombre . PHP_EOL .
            "DomicilioFiscalReceptor = " . $cliente->codigo_postal . PHP_EOL .
            "RegimenFiscalReceptor = " . $cliente->regimen_fiscal . PHP_EOL .
            "UsoCFDI = " . $uso_cfdi;
        return $receptor;
    }

    /**
     * [cfdi:Conceptos] Conceptos de la facutura
     * @param  object  $factura 
     * @param  Array  $partidas  [Array recibido en la función]
     * @return String $conceptos [Cadena concatenada]
     */
    private function ComprobanteConceptos($factura, $partidas)
    {
        $conceptos = "";
        foreach ($partidas as $p)
        {
            // Para pagos debe ser 0
            $valor_unitario = $factura->sctf_c_tipofactura == "P" ? 0 : $p->valor_unitario;
            $importe = $factura->sctf_c_tipofactura == "P" ? 0 : $p->importe;
            $obj_imp = $p->obj_imp;
            $descripcion = $factura->sctf_c_tipofactura == "P" ? "Pago" : str_replace("\n", " ", $p->descripcion);
            $conceptos .=
                "[cfdi:Comprobante/cfdi:Conceptos/cfdi:Concepto]" . PHP_EOL .
                "ClaveProdServ = " . $p->scps_clave . PHP_EOL .
                "Cantidad = " . ($factura->sctf_c_tipofactura == 'P' ? '1' : $p->cantidad) . PHP_EOL .
                "ClaveUnidad = " . $p->scu_c_unidad . PHP_EOL .
                ($factura->sctf_c_tipofactura == "P" ? "" : "Unidad = " . $p->unidad) . PHP_EOL .
                "Descripcion = " . $descripcion . PHP_EOL .
                // 'Comentario = Comentario pendiente' . PHP_EOL .
                "ValorUnitario = " . $valor_unitario . PHP_EOL .
                "Importe = " . $importe . PHP_EOL;
            // Pagos no llevan impuestos FIXME: Comprobar
            if ($factura->sctf_c_tipofactura == "P")
                $conceptos .= "ObjetoImp = 01" . PHP_EOL; // Sin objeto de impuestos
            else $conceptos .= "Descuento = " . $p->descuento . PHP_EOL . "ObjetoImp = $obj_imp" . PHP_EOL;
            // ($factura->sctf_c_tipofactura == "P" ? "ObjetoImp = 01" :
            //     "Descuento = " . $p->descuento . PHP_EOL . "ObjetoImp = $obj_imp" . PHP_EOL);
            // Si es obj de impuesto (02|03) se agrega nodo impuestos
            if ($obj_imp != "01")
                $conceptos .= $this->ConceptosImpuestos($p->impuesto_iva, $p->importe, $p->retencion);
        }
        return $conceptos;
    }

    /**
     * [cfdi:Impuestos] Impuestos de los cocneptos
     * @param  double  $iva Tasa o cuota del IVA (0.16)
     * @param  double  $importe Importe
     * @param  string  $retencion Indica si lleva retención
     */
    private function ConceptosImpuestos($iva, $importe, $retencion)
    {
        // Impuestos trasladados
        $impuestos = "[cfdi:Comprobante/cfdi:Conceptos/cfdi:Concepto/cfdi:Impuestos]" . PHP_EOL .
            "[cfdi:Comprobante/cfdi:Conceptos/cfdi:Concepto/cfdi:Impuestos/cfdi:Traslados]" . PHP_EOL .
            "[cfdi:Comprobante/cfdi:Conceptos/cfdi:Concepto/cfdi:Impuestos/cfdi:Traslados/cfdi:Traslado]" . PHP_EOL .
            "Base = " . $importe . PHP_EOL .
            "Impuesto = 002" . PHP_EOL . // IVA
            "TipoFactor = Tasa" . PHP_EOL .
            "TasaOCuota = " . $iva . PHP_EOL .
            "Importe = " . number_format(($importe * $iva), 2, ".", "") . PHP_EOL;

        // Agregar Retencion
        if ($retencion > 0)
            $impuestos .= "[cfdi:Comprobante/cfdi:Conceptos/cfdi:Concepto/cfdi:Impuestos/cfdi:Retenciones]" . PHP_EOL .
                "[cfdi:Comprobante/cfdi:Conceptos/cfdi:Concepto/cfdi:Impuestos/cfdi:Retenciones/cfdi:Retencion]" . PHP_EOL .
                "Base = " . $importe . PHP_EOL .
                "Impuesto = 002" . PHP_EOL .
                "TipoFactor = Tasa" . PHP_EOL .
                "TasaOCuota = " . $retencion . PHP_EOL .
                "Importe = " . number_format(($importe * $retencion), 2, ".", "") . PHP_EOL;
        return $impuestos;
    }

    /**
     * [Impuestos] Creación de los impuestos del comprobante
     * @param  arrray $partidas Partidas de la factura
     */
    private function TotalImpuestos($partidas)
    {
        $suma_impuesto = 0;
        $suma_retencion = 0;
        $total_base = 0;
        foreach ($partidas as $p)
        {
            // Si es objeto de impuesto, se suman sus impuestos
            if ($p->obj_imp == "02")
            {
                $total_base += $p->importe;
                $suma_impuesto += ($p->importe * $p->impuesto_iva);
                if ($p->retencion > 0)
                {
                    $suma_retencion += ($p->importe * $p->retencion);
                }
            }
        }

        // El valor reportado no coincide con el esperado
        $suma_impuesto += $this->error_suma_impuesto;
        $totalimpuestos = "";//$partidas[0]->retencion == '' ? '' :
	  //'TotalImpuestosRetenidos = ' . round($suma_retencion, 2);

        $suma_impuesto += $this->correccion_impuesto;;
        $totalimpuestos .= PHP_EOL .
            'TotalImpuestosTrasladados = ' . round($suma_impuesto, 2) . PHP_EOL .
            ($partidas[0]->retencion == 0 ? '' :
                '[cfdi:Comprobante/cfdi:Impuestos/cfdi:Retenciones]' . PHP_EOL .
                '[cfdi:Comprobante/cfdi:Impuestos/cfdi:Retenciones/cfdi:Retencion]' . PHP_EOL .
                'Impuesto = 002' . PHP_EOL .
                'Importe = ' . round($suma_retencion, 2) . PHP_EOL);
        $totalimpuestos .=
            '[cfdi:Comprobante/cfdi:Impuestos/cfdi:Traslados]' . PHP_EOL .
            '[cfdi:Comprobante/cfdi:Impuestos/cfdi:Traslados/cfdi:Traslado]' . PHP_EOL .
            'Impuesto = 002' . PHP_EOL . // 002: IVA
            "Base = $total_base" . PHP_EOL .
            'TipoFactor = Tasa' . PHP_EOL .
            'TasaOCuota = ' . $partidas[0]->impuesto_iva . PHP_EOL .
            'Importe = ' . number_format($suma_impuesto, 2, ".", "") . PHP_EOL;

        return $totalimpuestos;
    }

    /**
     * [Complemento/pago20:Pagos] Complemento de Pago del comprobante
     * @param object $factura Factura
     * @param array $partidas_pagos Pagos del comprobante
     */
    private function ComprobanteComplemento($factura, $partidas_pagos)
    {
        $fecha_p = substr($factura->fecha_pago, 0, 4) . '-' . substr($factura->fecha_pago, 5, 2) . '-' . substr($factura->fecha_pago, 8, 2) . 'T' . substr($factura->fecha_pago, 11, 8);

        $complemento_header = '[cfdi:Comprobante/cfdi:Complemento/pago20:Pagos]' . PHP_EOL .
            'Version = 2.0' . PHP_EOL;

        $monto_total_pagos = 0; // Monto total de todos los pagos
        $acum_pagos = "";
        $total_impuesto_iva = 0;
        $total_base = 0;
        foreach ($partidas_pagos as $p)
        {
            $complemento_pagos = ""; // Aux para el contenido de los pagos
            $aux_tipo_cambio = "";
            if ($p->tipo_cambio_dr != 0) // USD a MX
            {
                $monto = $p->importe_pagado;
                //$monto = round($p->importe_pagado / $p->tipo_cambio_dr, 2);
                $moneda = "USD";
                $tipo_cambio = $p->tipo_cambio_dr;
                // $aux_tipo_cambio = 'TipoCambioDR =' . $p->tipo_cambio_dr;
		// $aux_tipo_cambio = 'TipoCambioDR =' . number_format(1/$tipo_cambio,6,".","");
                $metodo_pago = $p->c_metodopago;
            }
            else
            {
                $metodo_pago = $factura->scmp_c_metodopago;
                $tipo_cambio = $factura->scm_c_moneda;
                $moneda = $factura->scm_c_moneda;
                $monto = $p->importe_pagado;
            }
            $monto_total_pagos += $monto;
            // En Moneda=MXN, TipoCambioP debe ser 1
            $tipo_cambio = $factura->scm_c_moneda == "MXN" ? 1 : $factura->tipo_cambio;
	    //$monto_total_pagos += number_format($monto*$tipo_cambio,2,".","");
            $equivalencia = $factura->scm_c_moneda == $moneda ? 1 : $tipo_cambio;
            $complemento_pagos .= '[cfdi:Comprobante/cfdi:Complemento/pago20:Pagos/pago20:Pago]' . PHP_EOL .
                'FechaPago =' . $fecha_p . PHP_EOL .
                'FormaDePagoP =' . $factura->scfp_clave . PHP_EOL .
                'MonedaP =' . $factura->scm_c_moneda . PHP_EOL .
                'TipoCambioP =' . $tipo_cambio . PHP_EOL .
                'Monto = ' . $monto . PHP_EOL .
                'NumOperacion = 0' . PHP_EOL .
                ';RfcEmisorCtaBen =' . $factura->rfc_cuenta_beneficiario . PHP_EOL .
                ';RfcEmisorCtaOrd =' . $factura->rfc_cuenta_ordenante . PHP_EOL .
                ';NomBancoOrdExt =' . $factura->ban_ordenante . PHP_EOL .
                ';CtaBeneficiario =' . $factura->cuenta_ordenante . PHP_EOL . PHP_EOL .
                '[cfdi:Comprobante/cfdi:Complemento/pago20:Pagos/pago20:Pago/pago20:DoctoRelacionado]' . PHP_EOL .
                'IdDocumento =' . $p->uuid . PHP_EOL .
                'Serie =' . $p->serie . PHP_EOL .
                'Folio =' . $p->folio . PHP_EOL .
                'MonedaDR = ' . $moneda . PHP_EOL .
                'EquivalenciaDR = ' . $equivalencia . PHP_EOL .
                $aux_tipo_cambio . PHP_EOL .
                'NumParcialidad = ' . $p->num_parcialidad . PHP_EOL .
                'ImpSaldoAnt = ' . $p->saldo_anterior . PHP_EOL .
                'ImpPagado =' . $p->importe_pagado . PHP_EOL .
                'ImpSaldoInsoluto =' . $p->saldo_insoluto . PHP_EOL .
                'ObjetoImpDR = ' . $p->obj_imp   . PHP_EOL;

            if ($p->obj_imp == "02")
            {
                $base_dr = round($monto / 1.16, 2); // Obtener base
                $importe_dr = round($base_dr * .16, 2); // iva
                $complemento_pagos .=
                    "[cfdi:Comprobante/cfdi:Complemento/pago20:Pagos/pago20:Pago/pago20:" .
                    "DoctoRelacionado/pago20:ImpuestosDR/pago20:TrasladosDR/pago20:TrasladoDR]" . PHP_EOL .
                    "TipoFactorDR=Tasa" . PHP_EOL .
                    "TasaOCuotaDR=0.160000" . PHP_EOL .
                    "BaseDR= " . $base_dr . PHP_EOL . // Importe
                    "ImpuestoDR = 002"  . PHP_EOL . // IVa
                    "ImporteDR= " . $importe_dr . PHP_EOL .
                    "; ----------------------" . PHP_EOL;
                $total_impuesto_iva += $importe_dr;
                $total_base += $base_dr;
		//$total_base += number_format($base_dr*$tipo_cambio,2,".","");
		//$total_impuesto_iva += number_format($importe_dr *$tipo_cambio,2,".","");
                $complemento_pagos .=
                    "[cfdi:Comprobante/cfdi:Complemento/pago20:Pagos/pago20:Pago/pago20:ImpuestosP/" .
                    "pago20:TrasladosP/pago20:TrasladoP]" . PHP_EOL .
                    "BaseP=" . $base_dr . PHP_EOL .
                    "ImpuestoP=002" . PHP_EOL . // IVA
                    "TipoFactorP=Tasa" . PHP_EOL .
                    "TasaOCuotaP=0.160000" . PHP_EOL . // IVA
                    "ImporteP=" . $importe_dr . PHP_EOL;
                }
                $acum_pagos .= $complemento_pagos; // Guardar el pago actual
        }

        $complemento_pagos_totales =
            "[cfdi:Comprobante/cfdi:Complemento/pago20:Pagos/pago20:Totales]" . PHP_EOL .
            "TotalTrasladosBaseIVA16 = " . $total_base . PHP_EOL .
            "TotalTrasladosImpuestoIVA16 =" . $total_impuesto_iva . PHP_EOL .
            "MontoTotalPagos = $monto_total_pagos"  . PHP_EOL;

        $complemento =
            $complemento_header .
            $complemento_pagos_totales .
            $acum_pagos;
        // $complemento_pagos;
        return $complemento;
    }

    /**
     * [cfdi:Addenda] Adenda del Comprobante
     * @param object $factura Factura
     */
    private function Adenda($factura, $partida)
    {
        // $tipo_cambio = $factura->tipo_cambio == 1.00 ? "1" : $factura->tipo_cambio;
        $adenda = "[cfdi:Comprobante/cfdi:Addenda]" . PHP_EOL .
            "[cfdi:Comprobante/cfdi:Addenda/cfdi:" . $factura->adenda . "]" . PHP_EOL .
            "[cfdi:Comprobante/cfdi:Addenda/cfdi:" . $factura->adenda . "/cfdi:Factura]" . PHP_EOL .
            "Moneda =" . $factura->scm_c_moneda . PHP_EOL .
            "TipoCambio = " . $factura->tipo_cambio . PHP_EOL . PHP_EOL .
            "[cfdi:Comprobante/cfdi:Addenda/cfdi:" . $factura->adenda . "/cfdi:Informacion]" . PHP_EOL .
            "Proveedor =" . $factura->proveeade . PHP_EOL .
            "NoRecepcion =" . $factura->clave_ob . PHP_EOL .
            "OrdenCompra =" . $factura->orden_ob . PHP_EOL;

        foreach ($partida as $p)
        {
            if ($p->comentario != "")
            {
                $adenda .=  "DescripcionCompleta" . $p->scps_clave . " = " .
                    str_replace("\n", " ", $p->descripcion) .
                    " " . str_replace("\n", " ", $p->comentario) . PHP_EOL;
            }
        }
        return $adenda;
    }

    /**
     * Cancelar la factura
     */
    public static function Cancelar($id, $motivo, $uuid_reemplazo, $prueba)
    {
        try
        {
            $fac = Factura::join("datosgenerales as d", "d.id", "factura.emisor_id")
                ->where("factura.id", $id)
                ->select("factura.id", "factura.uuid", "d.rfc")
                ->first();
            $fac->timbrado = 0;
            $fac->total = 0;
            $fac->save();
            // TODO: Cancelar en produccion
            if ($prueba)
                return ['status' => true];
            if ($motivo == "01")
                $motivo .= ";FolioSustitucion=$uuid_reemplazo";
            $ex = "cd " . FacturadorConfig::$PATH_PRUEBA_ST . " " . FacturadorConfig::$CFDI_CANCELAR_PRUEBA_ST . " " .
                $fac->rfc . " " .
                $fac->uuid . " " .
                $motivo  .
                " 2>error.txt;cat error.txt";
            // dd($ex);
            $output = shell_exec(
                "cd " . FacturadorConfig::$PATH_PRUEBA_ST . FacturadorConfig::$CFDI_CANCELAR_PRUEBA_ST . "2>error.txt;cat error.txt"
            );
            if ($output != null) return ['status' => false, 'mensaje' =>  $output]; // ERROR
            // Guardar resultado

            return ['status' => true];
        }
        catch (Exception $e)
        {
            dd($e);
        }
    }


    /**
     * Descarga el archivo xml de prueba, generado
     * @param string $id Id de la factura
     * @param string $file Nombre del xml
     */
    public static function DescargarXmlPrueba($id, $file)
    {
        try
        {
            // Buscar factura
            $fac = Factura::find($id);
            if ($fac == null) return null; // No encontrado

            $nombre = $fac->serie . "-" . $fac->folio . "_PRUEBA";
            $file = FacturadorConfig::$PATH_FACTURAS_PRUEBA_ST . "/" . $file . "tim.xml"; // ruta del app al xml
            $path_to_file = storage_path("app/") . $file; // Completa

            if (!file_exists($path_to_file)) return null;
            $file = Storage::disk('local')->get($file);
            $data = (object)["xml" => $file, "nombre" => $nombre];
            return $data; // Datos del xml
        }
        catch (Exception $e)
        {
            Utilidades::errors($e);
        }
    }

    public static function DescargarFactura($id)
    {
        try
        {
            $valores = explode('-', $id);
            $uuid = '';
            $rest = '';
            $total = '';
            $cadenaoriginal = '';
            $rfc = '';
            $url = '';
            $numcersat = '';
            $sellocfd = '';
            $sellosat = '';
            $fecha_emision = '';
            $tasa = '';
            $importe = '';
            $impuesto = '';


            $factura = Factura::where('factura.id', $valores[0])
                ->join('clientes AS c', 'c.id', '=', 'factura.cliente_id')
                ->join('datosgenerales AS dg', 'dg.id', '=', 'factura.emisor_id')
                ->join('sat_cat_usocfdi AS scuc', 'scuc.id', '=', 'factura.uso_factura')
                ->join('sat_cat_formapago AS scfp', 'scfp.id', '=', 'factura.formapago')
                ->join('sat_cat_metodopago AS scmp', 'scmp.id', '=', 'factura.metodopago')
                ->join('sat_cat_monedas AS scm', 'scm.id', '=', 'factura.moneda_id')
                ->join('sat_cat_tipofactura AS sctf', 'sctf.id', '=', 'factura.tipo_factura_id')
                ->leftJoin('sat_cat_tiporelacion AS sctr', 'sctr.id', '=', 'factura.tipo_relacion')
                ->leftJoin('factura AS f', 'f.id', '=', 'factura.factura_id')
                ->select(
                    'factura.*',
                    'sctf.descripcion AS tipo_factura_d',
                    'c.rfc AS rfc_c',
                    'dg.rfc AS rfc_e',
                    DB::raw("scuc.descripcion AS uso_cfdi"),
                    DB::raw("CONCAT(scfp.clave,' ',scfp.descripcion) AS forma_pago"),
                    DB::raw("CONCAT(scmp.c_metodopago,' ',scmp.descripcion) AS metodo_pago"),
                    'scm.c_moneda AS scm_c_moneda',
                    'sctr.c_tiporelacion AS tiporel_cod',
                    'sctr.descripcion AS tiporel_desc',
                    'f.serie AS fr_serie',
                    'f.folio AS fr_folio',
                    'f.uuid AS fr_uuid',
                    'f.total AS fr_total'
                )->first();
            $partidas_factura = PartidasFactura::where('factura_id', $id)
                ->join('sat_cat_unidades AS scu', 'scu.id', '=', 'partidas_factura.unidad_id')
                ->select('partidas_factura.*', 'scu.c_unidad as cla')->get();

            $arreglo_pf = [];
            $prueba = $factura->timbrado == 9; // Si es factura de prueba o real
            foreach ($partidas_factura as $key_pf => $value_pf)
            {
                $ps = DB::table('sat_cat_prodser')->where('id', $value_pf->productos_servicios_id)->first();
                if (isset($ps) == false)
                {
                    $ps = DB::table('sat_cat_prodser_big')->where('id', $value_pf->productos_servicios_id)->first();
                }
                $arreglo_pf[] = [
                    'partidas' => $value_pf,
                    'sat_ps' => $ps->clave,
                ];
            }
            $a = Relacionados::where('factura_id', $id)->get();
            $partidas_pagos = PartidasFacturasPagos::where('partidas_facturas_id', '=', $partidas_factura[0]->id)
                ->select('partidas_facturas_pagos.*')->get();


            $emisor = DatosGenerales::where('id', $factura->emisor_id)->first();
            $receptor = Clientes::where('id', $factura->cliente_id)->first();
            $path_xml = $prueba ? FacturadorConfig::$PATH_FACTURAS_PRUEBA_ST : FacturadorConfig::$PATH_FACTURAS_PROD_ST;
            $xml_string = Storage::disk('local')->get($path_xml . '/' . $id . 'tim.xml');
            $xml = simplexml_load_string($xml_string);
            $ns = $xml->getNamespaces(true);
            $xml->registerXPathNamespace('c', $ns['cfdi']);
            $xml->registerXPathNamespace('t', $ns['tfd']);

            foreach ($xml->xpath('//t:TimbreFiscalDigital') as $tfd)
            {
                $uuid = $tfd['UUID'];
                $sellosat = $tfd['SelloSAT'];
                $sellocfd = $tfd['SelloCFD'];
                $fecha_cer = $tfd['FechaTimbrado'];
                $rfc = $tfd['RfcProvCertif'];
                $numcersat = $tfd['NoCertificadoSAT'];
                $rest = substr($tfd['SelloCFD'], -8);
            }
            foreach ($xml->xpath('//cfdi:Comprobante') as $cfdiComprobante)
            {
                $total = $cfdiComprobante['Total'];
                $certificado_emisor = $cfdiComprobante['NoCertificado'];
            }
            $factura_actualizar = Factura::where('id', $valores[0])->first();
            $factura_actualizar->uuid = $uuid;
            $factura_actualizar->save();

            $cadenaoriginal = '||1.1|' . $uuid . '|' . $fecha_cer . '|' . $rfc . '|' . $sellocfd . '|' . $numcersat . '||';
            $suma_subtotal = 0;
            $suma_descuento = 0;
            $suma_impuesto = 0;
            $suma_retencion = 0;
            $total = 0;
            $suma_partidas_pagos = 0;
            foreach ($partidas_factura as $key => $value)
            {
                $suma_subtotal += $value->importe;
                $suma_descuento += $value->descuento;
                $suma_impuesto += ($value->importe * $value->impuesto_iva);
                if ($value->retencion != '')
                {
                    $suma_retencion += ($value->importe * $value->retencion);
                }
            }

            foreach ($partidas_pagos as $key => $value)
            {
                // Si es pago en dolares, obtiene la conversion guardada que se pagó en pesos
                // (importe*tipo_cambio_dr)
                $monto = $value->pago_dolares == 1 ? $value->importe_pagado_mx : $value->importe_pagado;
                $suma_partidas_pagos += $monto;
            }
            $suma_impuesto = round($suma_impuesto, 2);


            $total = ((($suma_subtotal - $suma_descuento) + $suma_impuesto) - $suma_retencion);
            $cambio = Utilidades::valorEnLetrasFactura(round($total, 2), $factura->moneda_id);
            $v_l_total_pagos = Utilidades::valorEnLetrasFactura(round($suma_partidas_pagos, 2), $factura->moneda_id);

            $url = 'https://verificacfdi.facturaelectronica.sat.gob.mx/default.aspx?id=' . $uuid . '&re=' . $factura->rfc_e . '&rr=' . $factura->rfc_c . '&tt=' . round($total, 2) . '&fe=' . $rest;
            $qr = QrCode::size(300)
                ->format('png')
                ->generate($url, public_path('img/' . $id . '.png'));

            $mes = array('Ene.', 'Feb.', 'Mar.', 'Abr.', 'May.', 'Jun.', 'Jul.', 'Ago.', 'Sep.', 'Oct.', 'Nov.', 'Dic.');
            $num_mes = intval(substr($factura->fecha_hora, 5, 2));
            $num_mes_cer = intval(substr($fecha_cer, 5, 2));

            $fecha_emision = substr($factura->fecha_hora, 8, 2) . ' ' . $mes[$num_mes - 1] . ' ' . substr($factura->fecha_hora, 0, 4) . ' - ' . substr($factura->fecha_hora, 11);
            $fecha_certificacion = substr($fecha_cer, 8, 2) . ' ' . $mes[$num_mes_cer - 1] . ' ' . substr($fecha_cer, 0, 4) . ' - ' . substr($fecha_cer, 11);

            $tipo = $prueba == 9 ? "_PRUEBA" : "";
            $nombre = $factura->serie . "-" . $factura->folio  . $tipo . ".pdf";
            error_reporting(E_ALL ^ E_DEPRECATED);
            $pdf = PDF::loadView('pdf.tesoreria.facturav4', compact(
                "prueba",
                'factura',
                'arreglo_pf',
                'partidas_factura',
                'uuid',
                'sellosat',
                'rest',
                'total',
                'certificado_emisor',
                'id',
                'fecha_emision',
                'emisor',
                'receptor',
                'suma_subtotal',
                'suma_descuento',
                'suma_impuesto',
                'suma_retencion',
                'cambio',
                'numcersat',
                'sellocfd',
                'a',
                'cadenaoriginal',
                'fecha_certificacion',
                'partidas_pagos',
                'v_l_total_pagos',
                'suma_partidas_pagos'
            ));
            $pdf->setPaper("letter", "portrait");
            return $pdf->stream($nombre);
        }
        catch (Exception $e)
        {
            dd($e);
        }
    }
}

