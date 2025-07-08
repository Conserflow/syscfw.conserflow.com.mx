<template>
<main>
    <vue-element-loading :active="isGuardar_loading" />
    <div class="row">
        <div class="col-md-6">
            <div class="form-group row">
                <label class="col-md-3 form-control-label" for="correo_electronico">Correo electrónico</label>
                <div class="col-md-9">
                    <input type="email" size="60" v-validate="'required|max:60'" name="correo_electronico" v-model="contacto.correo_electronico" class="form-control" placeholder="Correo electrónico" autocomplete="off" id="correo_electronico" data-vv-as="Correo electronico">
                    <span class="text-danger">{{ errors.first("correo_electronico") }}</span>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-md-3 form-control-label" for="tel_celular">Teléfono celular</label>
                <div class="col-md-9">
                    <input type="text" minlength="10" maxlength="10" v-validate="'required|numeric|max:10'" name="tel_celular" v-model="contacto.tel_celular" class="form-control" placeholder="Tel. celular" autocomplete="off" id="tel_celular" data-vv-as="Tel. celular">
                    <span class="text-danger">{{ errors.first("tel_celular") }}</span>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-md-3 form-control-label" for="tel_casa">Teléfono casa</label>
                <div class="col-md-9">
                    <input type="text" minlength="10" maxlength="10" v-validate="'required|max:13'" name="tel_casa" v-model="contacto.tel_casa" class="form-control" placeholder="Tel. casa" autocomplete="off" id="tel_casa" data-vv-as="Tel. casa">
                    <span class="text-danger">{{ errors.first("tel_casa") }}</span>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group row">
                <label class="col-md-3 form-control-label" for="contacto_emergencia">Contacto emergencia</label>
                <div class="col-md-9">
                    <input type="text" v-validate="'required|max:50'" name="contacto_emergencia" v-model="contacto.contacto_emergencia" class="form-control" placeholder="Contacto emergencia" autocomplete="off" id="contacto_emergencia" data-vv-as="Contacto emergencia">
                    <span class="text-danger">{{ errors.first("contacto_emergencia") }}</span>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-md-3 form-control-label" for="tel_emergencia">Telefono emergencia</label>
                <div class="col-md-9">
                    <input type="text" minlength="10" maxlength="10" v-validate="'required|numeric'" name="tel_emergencia" v-model="contacto.tel_emergencia" class="form-control" placeholder="Tel. emergencia" autocomplete="off" id="tel_emergencia" data-vv-as="Tel. emergencia">
                    <span class="text-danger">{{ errors.first("tel_emergencia") }}</span>
                </div>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button v-show="PermisosCrud.Update && empleado_activo" type="button" class="btn btn-secondary" @click="GuardarContacto()">
            <i class="fas fa-save"></i>&nbsp;Actualizar
        </button>
    </div>
</main>
</template>

<script>
export default
{
    data()
    {
        return {
            PermisosCrud:
            {},
            empleado_activo: false,
            contacto:
            {
                tel_celular: "",
                tel_casa: "",
                correo_electronico: "",
                contacto_emergencia: "",
                tel_emergencia: "",
                empleado_id: 0,
            },
            isGuardar_loading: false,
        }
    },
    computed:
    {},
    methods:
    {
        /**
         * Guardar el contacto
         */
        async GuardarContacto()
        {
            let isValid = await this.$validator.validate();
            if (!isValid) return;
            this.isGuardar_loading = true;
            let data = new FormData();
            if (this.contacto.id != null)
                data.append("id", this.contacto.id);
            data.append("empleado_id", this.contacto.empleado_id);
            data.append("tel_celular", this.contacto.tel_celular);
            data.append("tel_casa", this.contacto.tel_casa);
            data.append("contacto_emergencia", this.contacto.contacto_emergencia);
            data.append("correo_electronico", this.contacto.correo_electronico);
            data.append("tel_emergencia", this.contacto.tel_emergencia);
            axios.post("rh/empleados/contacto/guardar", data).then(res =>
            {
                this.isGuardar_loading = false;
                if (res.data.status)
                {
                    toastr.success("Contacto Actualizado Correctamente.");
                }
                else
                {
                    toastr.error(res.data.mensaje);
                }
            })
        },

        /**
         * Cargar el contacto del empleado
         */
        CargarContacto(empleado, PermisosCrud)
        {
            this.isGuardar_loading = true;
            // Asignar los permisos
            this.empleado_activo = empleado.condicion;
            this.PermisosCrud = PermisosCrud;
            axios.get("rh/empleados/contacto/obtener/" + empleado.id).then(res =>
            {
                this.isGuardar_loading = false;
                if (res.data.status)
                {
                    if (res.data.contacto == null)
                    {
                        this.nuevo = true;
                        this.contacto = {
                            empleado_id: empleado.id
                        };
                    }
                    else
                    {
                        this.nuevo = true;
                        this.contacto = res.data.contacto;
                    }
                }
                else toastr.error(res.data.mensaje);
            });
        }
    },
}
</script>
