<template>
<main>
    <vue-element-loading :active="isExpedienteLoading" />
    <div class="form-group row">
        <label class="col-md-3 form-control-label" for="folio">Folio</label>
        <div class="col-md-9">
            <input type="text" v-validate="'required'" name="folio" v-model="expediente.folio" class="form-control" placeholder="Folio" autocomplete="off" id="folio">
            <span class="text-danger">{{ errors.first("folio") }}</span>
        </div>
    </div>
    <div class="form-group row">
        <div class="col-md-4">
            <div class="form-check checkbox">
                <input class="" value="" id="check1" type="checkbox" v-model="expediente.solicitud">
                <label class="form-check-label" for="check1">
                    Solicitud
                </label>
            </div>
            <div class="form-check checkbox">
                <input class="" value="" id="check2" type="checkbox" v-model="expediente.evaluacion_psicolaboral">
                <label class="form-check-label" for="check2">
                    Evaluación Psicolaboral
                </label>
            </div>
            <div class="form-check checkbox">
                <input class="" value="" id="check3" type="checkbox" v-model="expediente.evaluacion_hab_tecnicas">
                <label class="form-check-label" for="check3">
                    Evaluación hab. técnicas
                </label>
            </div>
            <div class="form-check checkbox">
                <input class="" value="" id="check4" type="checkbox" v-model="expediente.comprobante_domicilio">
                <label class="form-check-label" for="check4">
                    Comprobante de domicilio
                </label>
            </div>
            <div class="form-check checkbox">
                <input class="" value="" id="check5" type="checkbox" v-model="expediente.cartilla_servicio_militar">
                <label class="form-check-label" for="check5">
                    Cartilla servicio militar
                </label>
            </div>
            <div class="form-check checkbox">
                <input class="" value="" id="check6" type="checkbox" v-model="expediente.vales_resguardo">
                <label class="form-check-label" for="check6">
                    Vales de resguardo
                </label>
            </div>
            <div class="form-check checkbox">
                <input class="" value="" id="check7" type="checkbox" v-model="expediente.comprobante_estudios">
                <label class="form-check-label" for="check7">
                    Comprobante de estudios
                </label>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-check checkbox">
                <input class="" value="" id="check8" type="checkbox" v-model="expediente.foto">
                <label class="form-check-label" for="check8">
                    Foto
                </label>
            </div>
            <div class="form-check checkbox">
                <input class="" value="" id="check9" type="checkbox" v-model="expediente.acta_nacimiento">
                <label class="form-check-label" for="check9">
                    Acta de nacimiento
                </label>
            </div>
            <div class="form-check checkbox">
                <input class="" value="" id="check10" type="checkbox" v-model="expediente.credencial_elector">
                <label class="form-check-label" for="check10">
                    Credencial de elector
                </label>
            </div>
            <div class="form-check checkbox">
                <input class="" value="" id="check11" type="checkbox" v-model="expediente.licencia_manejo">
                <label class="form-check-label" for="check11">
                    Licencia manejo
                </label>
            </div>
            <div class="form-check checkbox">
                <input class="" value="" id="check12" type="checkbox" v-model="expediente.acta_matrimonio">
                <label class="form-check-label" for="check12">
                    Acta de matrimonio
                </label>
            </div>
            <div class="form-check checkbox">
                <input class="" value="" id="check13" type="checkbox" v-model="expediente.carta_recomendacion">
                <label class="form-check-label" for="check13">
                    Carta de recomendación
                </label>
            </div>
            <div class="form-check checkbox">
                <input class="" value="" id="check14" type="checkbox" v-model="expediente.retencion_credito_infonavit">
                <label class="form-check-label" for="check14">
                    Retención crédito infonavit
                </label>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-check checkbox">
                <input class="" value="" id="check15" type="checkbox" v-model="expediente.curp">
                <label class="form-check-label" for="check15">
                    CURP
                </label>
            </div>
            <div class="form-check checkbox">
                <input class="" value="" id="check16" type="checkbox" v-model="expediente.rfc">
                <label class="form-check-label" for="check16">
                    RFC
                </label>
            </div>
            <div class="form-check checkbox">
                <input class="" value="" id="check17" type="checkbox" v-model="expediente.nss_imss">
                <label class="form-check-label" for="check17">
                    NSS IMSS
                </label>
            </div>
            <div class="form-check checkbox">
                <input class="" value="" id="check18" type="checkbox" v-model="expediente.carta_infonavit">
                <label class="form-check-label" for="check18">
                    Carta infonavit
                </label>
            </div>
            <div class="form-check checkbox">
                <input class="" value="" id="check19" type="checkbox" v-model="expediente.certificado_medico">
                <label class="form-check-label" for="check19">
                    Certificado médico
                </label>
            </div>
            <div class="form-check checkbox">
                <input class="" value="" id="check20" type="checkbox" v-model="expediente.carta_no_enfermedades">
                <label class="form-check-label" for="check20">
                    Carta de no enfermedades
                </label>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <vue-element-loading :active="isExpedienteLoading" />
        <div>
            <button v-show="empleado_activo" type="button" class="btn btn-secondary" @click="GuardarExpediente()">
                <i class="fas fa-save"></i>&nbsp;Actualizar
            </button>
        </div>
    </div>
</main>
</template>

<script>
export default
{
    data()
    {
        return {
            url: "rh/expediente",
            empleado_activo: false,
            expediente:
            {
                id: 0,
                solicitud: 0,
                evaluacion_psicolaboral: 0,
                evaluacion_hab_tecnicas: 0,
                foto: 0,
                acta_nacimiento: 0,
                credencial_elector: 0,
                curp: 0,
                rfc: 0,
                nss_imss: 0,
                comprobante_domicilio: 0,
                cartilla_servicio_militar: 0,
                licencia_manejo: 0,
                acta_matrimonio: 0,
                carta_infonavit: 0,
                certificado_medico: 0,
                carta_no_enfermedades: 0,
                vales_resguardo: 0,
                comprobante_estudios: 0,
                carta_recomendacion: 0,
                retencion_credito_infonavit: 0,
                folio: "",
                empleado_id: 0,
            },
            isExpedienteLoading: false,
        }
    },
    methods:
    {
        /**
         * Guarda el expediente 
         */
        async GuardarExpediente()
        {
            let isValid = await this.$validator.validate();
            if (!isValid) return;

            this.isExpedienteLoading = true;

            let data = new FormData();
            data.append("id", this.expediente.id);
            data.append("folio", this.expediente.folio);
            data.append("empleado_id", this.expediente.empleado_id);
            data.append("solicitud", this.expediente.solicitud?1:0);
            data.append("evaluacion_psicolaboral", this.expediente.evaluacion_psicolaboral?1:0);
            data.append("evaluacion_hab_tecnicas", this.expediente.evaluacion_hab_tecnicas?1:0);
            data.append("foto", this.expediente.foto?1:0);
            data.append("acta_nacimiento", this.expediente.acta_nacimiento?1:0);
            data.append("credencial_elector", this.expediente.credencial_elector?1:0);
            data.append("curp", this.expediente.curp?1:0);
            data.append("rfc", this.expediente.rfc?1:0);
            data.append("nss_imss", this.expediente.nss_imss?1:0);
            data.append("comprobante_domicilio", this.expediente.comprobante_domicilio?1:0);
            data.append("cartilla_servicio_militar", this.expediente.cartilla_servicio_militar?1:0);
            data.append("licencia_manejo", this.expediente.licencia_manejo?1:0);
            data.append("acta_matrimonio", this.expediente.acta_matrimonio?1:0);
            data.append("carta_infonavit", this.expediente.carta_infonavit?1:0);
            data.append("certificado_medico", this.expediente.certificado_medico?1:0);
            data.append("carta_no_enfermedades", this.expediente.carta_no_enfermedades?1:0);
            data.append("vales_resguardo", this.expediente.vales_resguardo?1:0);
            data.append("comprobante_estudios", this.expediente.comprobante_estudios?1:0);
            data.append("carta_recomendacion", this.expediente.carta_recomendacion?1:0);
            data.append("retencion_credito_infonavit", this.expediente.retencion_credito_infonavit?1:0);

            axios.post(this.url + "/guardar", data).then(res =>
            {
                this.isExpedienteLoading = false;
                if (res.data.status)
                {
                    toastr.success("Expediente Guardado Correctamente");
                }
                else
                {
                    toastr.error(res.data.mensaje);
                }
            })
        },

        /**
         * Obtiene el expediente del empleado seleccionado
         */
        CargarExpediente(dataEmpleado = [])
        {
            this.empleado_activo = dataEmpleado.condicion;
            this.isExpedienteLoading = true;
            axios.get("rh/expediente/obtener/" + dataEmpleado.id).then(res =>
            {
                this.isExpedienteLoading = false;
                if (res.data.status)
                {
                    this.expediente = res.data.expediente;
                }
                else
                {
                    toastr.error(res.data.mensaje);
                }
            });
        }
    },
    mounted()
    {}
}
</script>
