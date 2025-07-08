<template>
<main>
    <vue-element-loading :active="isLoading" />
    <div class="form">
        <div class="form-group row">
            <label class="col-md-1 " for="text-input">Nombre</label>
            <div class="col-md-3">
                <input type="text" v-validate="'required|max:60'" name="nombre" data-toggle="tooltip" v-model="empleado.nombre" class="form-control" autocomplete="off">
                <span class="text-danger">{{ errors.first("nombre") }}</span>
            </div>
            <label class="col-md-1 " for="text-input">Apellido Paterno</label>
            <div class="col-md-3">
                <input type="text" v-validate="'required|max:40'" name="paterno" data-toggle="tooltip" v-model="empleado.ap_paterno" class="form-control" autocomplete="off">
                <span class="text-danger">{{ errors.first("paterno") }}</span>
            </div>
            <label class="col-md-1 " for="text-input">Apellido Materno</label>
            <div class="col-md-3">
                <input type="text" v-validate="'required|max:40'" name="materno" data-toggle="tooltip" v-model="empleado.ap_materno" class="form-control" autocomplete="off">
                <span class="text-danger">{{ errors.first("materno") }}</span>
            </div>
        </div>
        <div class="form-group row">
            <label class="col-md-1" for="text-input">Fecha de alta IMSS</label>
            <div class="col-md-3">
                <input type="date" v-model="empleado.fech_alta_imss" name="fech_alta_imss" v-validate="'required'" class="form-control" placeholder="Fecha de Alta IMSS">
                <span class="text-danger">{{ errors.first("fech_alta_imss") }}</span>
            </div>
            <label class="col-md-1" for="text-input">Fecha Real de Ingreso</label>
            <div class="col-md-3">
                <input type="date" v-model="empleado.fech_ing" name="fech_ing" v-validate="'required'" class="form-control" placeholder="Fecha Real de Ingreso">
                <span class="text-danger">{{ errors.first("fech_ing") }}</span>
            </div>
            <label class="col-md-1 " for="text-input">CURP</label>
            <div class="col-md-3">
                <input type="text" minlength="18" maxlength="18" v-validate="'required'" name="curp" data-toggle="tooltip" title="Completa los 18 dígitos de la CURP" v-model="empleado.curp" class="form-control" placeholder="Curp" autocomplete="off">
                <span class="text-danger">{{ errors.first("curp") }}</span>
            </div>
            <label class="col-md-1 " for="text-input">RFC:</label>
            <div class="col-md-3">
                <input type="text" v-validate="'required'" maxlength="13" minlength="13" name="rfc" data-toggle="tooltip" title="Completa los 13 dígitos del RFC" v-model="empleado.rfc" class="form-control" placeholder="Rfc" autocomplete="off">
                <span class="text-danger">{{ errors.first("rfc") }}</span>
            </div>
            <label class="col-md-1 " for="text-input">NSS</label>
            <div class="col-md-3">
                <input type="text" v-validate="'required'" minlength="11" maxlength="11" name="nss_imss" data-toggle="tooltip" title="Completa los 11 dígitos del N° de seguro" v-model="empleado.nss_imss" class="form-control" placeholder="NSS" autocomplete="off">
                <span class="text-danger">{{ errors.first("nss_imss") }}</span>
            </div>
        </div>
        <div class="form-group row">
            <label class="col-md-1 " for="text-input">Lugar de Nacimiento</label>
            <div class="col-md-3">
                <input type="text" v-model="empleado.lug_nac" v-validate="'required|max:45'" class="form-control" id="lug_nac" name="lug_nac" placeholder="Lugar de nacimiento" autocomplete="off" required data-vv-as="lugar de nacimiento">
                <span class="text-danger">{{ errors.first("lug_nac") }}</span>
            </div>
            <label class="col-md-1" for="text-input">Fecha de Nacimiento</label>
            <div class="col-md-3">
                <input type="date" v-model="empleado.fech_nac" name="fech_nac" v-validate="'required'" class="form-control" placeholder="Fecha de Nacimiento">
                <span class="text-danger">{{ errors.first("fech_nac") }}</span>
            </div>

            <label class="col-md-1 " for="text-input">Sexo</label>
            <div class="col-md-3">
                <p>
                    <select name="text-input" id="text-input" v-model="empleado.sexo" data-toggle="tooltip" title="Selecciona sexo" class="form-control" v-validate="'required'" data-vv-as="sexo">
                        <option value="1">MASCULINO</option>
                        <option value="0">FEMENINO</option>
                    </select>
                    <span class="text-danger">{{ errors.first("text-input") }}</span>
                </p>
            </div>
        </div>
        <hr>
        <p class="h6">Situación Fiscal</p>
        <div class="form-group row">
            <label class="col-md-1 " for="text-input">C.P.</label>
            <div class="col-md-3">
                <input type="text" minlength="5" maxlength="5" v-model="empleado.fiscal_cp" v-validate="'required'" class="form-control" autocomplete="off" required data-vv-name="C.P.">
                <span class="text-danger">{{ errors.first("C.P.") }}</span>
            </div>
            <label class="col-md-1" for="text-input">Estado</label>
            <div class="col-md-3">
                <input type="text" min="3" maxlength="50" v-model="empleado.fiscal_estado" v-validate="'required'" class="form-control" placeholder="Estado" data-vv-name="Estado">
                <span class="text-danger">{{ errors.first("Estado") }}</span>
            </div>
        </div>

        <hr>
        <div class="form-group row">
            <label class="col-md-1 " for="text-input">Edo. Civil</label>
            <div class="col-md-3">
                <select class="form-control" id="edo_civil_id" name="edo_civil_id" data-toggle="tooltip" title="Selecciona estado civil" v-model="empleado.edo_civil_id" v-validate="'excluded:0'" data-vv-as="estado civil">
                    <option v-for="item in list_estadosciviles" :value="item.id" :key="item.id">{{ item.nombre }}</option>
                </select>
                <span class="text-danger">{{ errors.first("edo_civil_id") }}</span>
            </div>
            <label class="col-md-1 " for="text-input">Tipo de sangre</label>
            <div class="col-md-3">
                <select v-model="empleado.tipo_sangre" class="form-control">
                    <option value="S/I">SIN INFORMACIÓN</option>
                    <option value="O+">O+</option>
                    <option value="O-">O-</option>
                    <option value="A+">A+</option>
                    <option value="A-">A-</option>
                    <option value="B+">B+</option>
                    <option value="B-">B-</option>
                    <option value="AB+">AB+</option>
                    <option value="AB-">AB-</option>
                </select>
            </div>
        </div>

        <div class="form-group row">
            <label class="col-md-1 " for="text-input">Overol</label>
            <div class="col-md-3">
                <input v-validate="'required'" type="number" min="0" step="1" v-model="empleado.talla_overol" data-toggle="tooltip" title="Inserta talla de overol" class="form-control" placeholder="Talla overol" data-vv-name="Talla overol">
                <span class="text-danger">{{errors.first("Talla overol")}}</span>
            </div>
            <label class="col-md-1 " for="text-input">Camisa</label>
            <div class="col-md-3">
                <input type="text" v-validate="'required'" v-model="empleado.talla_camisa" class="form-control" placeholder="Talla Camisa" data-vv-name="Talla Camisa">
                <span class="text-danger">{{errors.first("Talla Camisa")}}</span>
            </div>
            <label class="col-md-1 " for="text-input">Botas</label>
            <div class="col-md-2">
                <input type="number" v-validate="'required'" min="0" step="0.5" v-model="empleado.talla_botas" data-toggle="tooltip" title="Inserta talla de botas" class="form-control" placeholder="Talla botas" data-vv-name="Talla Botas">
                <span class="text-danger">{{errors.first("Talla Botas")}}</span>
            </div>
        </div>

        <hr>
        <div class="form-group row">
            <label class="col-md-1" for="text-input">Salario Neto</label>
            <div class="col-md-3">
                <input type="number" v-validate="'required'" v-model="empleado.salario_neto" class="form-control" data-vv-name="Salario Neto" placeholder="Salario Neto">
                <span class="text-danger">{{ errors.first("Salario Neto") }}</span>
            </div>
            <label class="col-md-1 " for="text-input">Amortización</label>
            <div class="col-md-3">
                <input type="text" v-validate="'required'" v-model="empleado.amortizacion" class="form-control" placeholder="Amortización" data-vv-name="Amortización">
                <span class="text-danger">{{ errors.first("Amortización") }}</span>
            </div>

            <label class="col-md-1 " for="text-input">Número Credito</label>
            <div class="col-md-3">
                <input type="number" v-validate="'required'" v-model="empleado.numero_credito" class="form-control" data-toggle="tooltip" data-vv-name="Numero de Credito" placeholder="Numero de Credito">
                <span class="text-danger">{{ errors.first("Numero de Credito") }}</span>
            </div>
        </div>
        <hr>
        <div class="form-group row">
            <label class="col-md-1 form-control-label" for="puesto_id">Puesto</label>
            <div class="col-md-4">
                <v-select :options="list_puestos" v-model="empleado.puesto_id" data-vv-name="puesto" v-validate="'required'" label="nombre"></v-select>
                <span class="text-danger">{{ errors.first("puesto_id") }}</span>
            </div>
        </div>
        <div class="form-group row">
            <label class="col-md-2 " for="text-input">Ubicación de planta</label>
            <div class="col-md-4">
                <select class="form-control" id="ubicacion_id" name="ubicacion_id" data-toggle="tooltip" title="Selecciona la ubicación del empleado" v-model="empleado.ubicacion_id" v-validate="'excluded:0'" data-vv-as="Ubicacion">
                    <option v-for="item in list_ubicaciones" :value="item.id" :key="item.id">{{ item.nombre }}</option>
                </select>
                <span class="text-danger">{{ errors.first("ubicacion_id") }}</span>
            </div>
            <label class="col-md-1 " for="text-input">Empresa</label>
            <div class="col-md-2">
                <select class="form-control" v-model="empleado.id_checador" v-validate="'required'" data-vv-name="empresa">
                    <option value="1">Conserflow Semanal</option>
                    <option value="2">Conserflow Quincenal</option>
                    <option value="3">CSCT Semanal</option>
                    <option value="4">CSCT Quincenal</option>
                </select>
                <span class="text-danger">{{ errors.first("empresa") }}</span>
            </div>
        </div>
    </div>
    <div class="modal-footer" ref="finAlta">
        <template v-if="tipoAccion == 1">
            <button type="button" class="btn btn-secondary" @click="Guardar(true)">
                <i class="fas fa-save"></i>&nbsp;Guardar
            </button>
        </template>
        <template v-if="tipoAccion == 2">
            <button v-show="empleado_activo" type="button" class="btn btn-secondary" @click="Guardar(false)">
                <i class="fas fa-save"></i>&nbsp;Actualizar
            </button>
        </template>
    </div>
</main>
</template>

<script>
export default
{
    data()
    {
        return {
            url: "/rh/empleados",
            PermisosCrud:
            {},
            empleado_activo: 0,
            empleado:
            {
                id: 0,
                nombre: "",
                ap_paterno: "",
                ap_materno: "",
                sexo: 1,
                lug_nac: "",
                fech_nac: "",
                fech_alta_imss: "",
                fech_ing: "",
                tipo_sangre: "S/I",
                talla_overol: 0,
                talla_botas: 0,
                talla_camisa: 0,
                amortizacion: 0,
                numero_credito: 0,
                edo_civil_id: 1,
                puesto_id:
                {},
                curp: "",
                rfc: "",
                nss_imss: "",
                ubicacion_id: 1,
                id_checador: 1,
                fiscal_cp: "",
                fiscal_estado: "",
                salario_neto: 0,
            },
            tipoAccion: 1,
            error: 0,
            list_ubicaciones: [],
            list_estadosciviles: [],
            list_puestos: [],
            errorMostrarMsj: [],
            isLoading: false,
        }
    },
    methods:
    {
        /**
         * Obtener los catalos para el registro del empleado
         */
        ObtenerCatalogos()
        {
            axios.get("rh/catalogos/estadosciviles").then(res =>
            {
                if (res.data.status)
                    this.list_estadosciviles = res.data.estados;
                else toastr.error(res.data.mensaje);
            });
            axios.get("rh/catalogos/tipoubicacion").then(res =>
            {
                if (res.data.status)
                    this.list_ubicaciones = res.data.ubicaciones;
                else toastr.error(res.data.mensaje);
            })
            axios.get("rh/catalogos/puestosnombre").then(res =>
            {
                if (res.data.status)
                    this.list_puestos = res.data.puestos;
                else toastr.error(res.data.mensaje);
            })
        },

        /**
         * Guarda el empleado
         */
        async Guardar(nuevo)
        {
            let isValid = await this.$validator.validate();
            if (!isValid) return;

            if (this.empleado.puesto_id == null)
            {
                toastr.warning("Seleccione el puesto");
                return;
            }
            if (this.empleado.puesto_id.id == null)
            {
                toastr.warning("Seleccione el puesto");
                return;
            }
            this.isLoading = true;
            let data = new FormData();
            if (!nuevo)
                data.append("id", this.empleado.id);
            data.append("nombre", this.empleado.nombre);
            data.append("ap_paterno", this.empleado.ap_paterno);
            data.append("ap_materno", this.empleado.ap_materno);
            data.append("lug_nac", this.empleado.lug_nac);
            data.append("fech_nac", this.empleado.fech_nac);
            data.append("fech_alta_imss", this.empleado.fech_alta_imss);
            data.append("fech_ing", this.empleado.fech_ing);
            data.append("curp", this.empleado.curp);
            data.append("rfc", this.empleado.rfc);
            data.append("nss_imss", this.empleado.nss_imss);
            data.append("sexo", this.empleado.sexo);
            data.append("edo_civil_id", this.empleado.edo_civil_id);
            data.append("tipo_sangre", this.empleado.tipo_sangre);
            data.append("talla_overol", this.empleado.talla_overol);
            data.append("talla_botas", this.empleado.talla_botas);
            data.append("talla_camisa", this.empleado.talla_camisa);
            data.append("amortizacion", this.empleado.amortizacion);
            data.append("numero_credito", this.empleado.numero_credito);
            data.append("puesto_id", this.empleado.puesto_id.id);
            data.append("ubicacion_id", this.empleado.ubicacion_id);
            data.append("id_checador", this.empleado.id_checador);
            data.append("fiscal_cp", this.empleado.fiscal_cp);
            data.append("fiscal_estado", this.empleado.fiscal_estado);
            data.append("salario_neto", this.empleado.salario_neto);
            axios.post(this.url, data).then(res =>
            {
                this.isLoading = false;
                if (res.data.status)
                {
                    toastr.success("Empleado " + (nuevo ? "Registrado" : "Actualizado") + " Correctamente");
                    this.$emit("regresar", res.data.emp);
                }
                else
                {
                    toastr.error(res.data.mensaje);
                }
            })
        },

        /**
         * Cargar los datos del empleado
         */
        CargarEmpleado(empleado, PermisosCrud, reciente)
        {
            this.PermisosCrud = PermisosCrud;
            this.tipoAccion = 2;
            this.empleado_activo = empleado.condicion;
            this.isLoading = true;
            if (reciente) // 
            {
                // Desplazar hacia los contratos
                setTimeout(() =>
                {
                    const el = this.$refs.finAlta;
                    if (el)
                    {
                        el.scrollIntoView(
                        {
                            behavior: 'smooth'
                        });
                    }
                }, 50);

            }
            axios.get("rh/empleados/" + empleado.id).then(res =>
            {
                this.isLoading = false;
                if (res.data.status)
                {
                    this.empleado = {
                        ...res.data.empleado
                    };
                    this.empleado.puesto_id = {
                        id: res.data.empleado.puesto_id,
                        nombre: res.data.empleado.puesto_nombre
                    };
                }
                else
                    toastr.error(res.data.mensaje);
            })
        },

        Limpiar()
        {
            this.empleado = {};
            this.tipoAccion = 1;
        }
    },
    mounted()
    {
        this.ObtenerCatalogos();
    }
}
</script>
