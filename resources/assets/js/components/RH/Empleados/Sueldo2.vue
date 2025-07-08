<template>
<main>
    <div>
        <br>
        <h5>Salario</h5>
        <template v-if="!this.nuevo">
            <button style="margin-top:-2rem" class="btn btn-dark float-sm-right" @click="NuevoSalario">
                <i class="fas fa-plus"></i>
            </button>
        </template>
    </div>
    <vue-element-loading :active="isSueldos_loading" />
    <table class="table">
        <thead style="border-top:1px solid white">
            <tr>
                <th>SDI</th>
                <th>Salario Diario</th>
                <th>Salario {{ tipo_nomina }}</th>
                <th>Fecha de Actualización</th>
            </tr>
        </thead>
        <tbody>
            <tr :key="i" v-for="(s, i) in list_sueldos">
                <td>
                    <span class="sdi" @click="ActualizarSDI(s.id)">
                        {{ s.sueldo_diario_integral }}
                    </span>
                </td>
                <td>{{ s.sueldo_diario_neto }}</td>
                <td>{{ s.sueldo_diario_real }}</td>
                <td>{{ s.fecha_act }}</td>
            </tr>
        </tbody>
    </table>
</main>
</template>

<style>
.sdi {
    cursor: pointer;
    padding: 10px;
    border: 1px solid white;
}

.sdi:hover {
    border: 1px solid black;
}
</style>

<script>
export default
{
    data()
    {
        return {
            nuevo: true,
            contrato_id: 0,
            nomina_id: 0,
            salario_neto: 0,
            tipo_nomina: "",
            desabilitado: 0,
            isSueldos_loading: false,
            sueldo:
            {
                id: 0,
                infonavit: 0,
                sueldo_diario_integral: 0,
                sueldo_mensual: 0,
                viaticos_mensuales: 0,
                sueldo_diario_neto: 0,
                contrato_id: 0,
                sueldo_diario_real: 0,
                fecha_act: "",
            },
            list_sueldos: [],
        };
    },
    computed:
    {},
    methods:
    {
        /**
         * Obtener los sueldos del contrato seleccionado
         * @nuevo bool Nuevo contrato o actualización
         * @contrato_id int Id del contrato
         * @nomina_id int Id de tipo nomina
         * @salario_neto Int Salario neto (S|Q)
         */
        CargarSueldos(nuevo, contrato_id, nomina_id, salario_neto)
        {
            this.isSueldos_loading;
            this.nuevo = nuevo;
            this.nomina_id = nomina_id;
            this.salario_neto = salario_neto;
            this.contrato_id = contrato_id;
            this.tipo_nomina = nomina_id == 1 ? "Semanal" : "Quincenal";
            if (nuevo)
            {
                this.list_sueldos = [];
            }
            else
            {
                axios.get("rh/sueldos/obtener/" + contrato_id).then((res) =>
                {
                    if (res.data.status)
                    {
                        this.list_sueldos = res.data.sueldos;
                    }
                    else
                    {
                        toastr.error(res.data.mensaje);
                    }
                });
            }
        },

        /**
         * Ingresar un nuevo salario
         */
        async NuevoSalario()
        {
            let dias = this.nomina_id == 1 ? 7 : 15;
            let aux_diario = this.salario_neto / dias;
            var diario = aux_diario.toString().match(/^-?\d+(?:\.\d{0,2})?/)[0];
            let val = await Swal.fire(
            {
                title: "Registar salario",
                html: `<span class='mt-5 mb-3 font-weight-bold h5'>Salario Diario:
                </span> &nbsp;&nbsp;$ ${Intl.NumberFormat().format(diario)}
                        <br><span class='font-weight-bold h5'>Salario ${
                          this.tipo_nomina
                        }
                        </span>&nbsp;&nbsp; $ ${Intl.NumberFormat().format(
                          this.salario_neto
                        )}`,
                input: "text",
                confirmButtonText: "Guardar",
                cancelButtonText: "Cancelar",
                inputLabel: "Cantidad",
                inputPlaceholder: "Ingrese el SDI",
                showCancelButton: true,
                inputValidator: (value) =>
                {
                    if (!value)
                    {
                        return "Escriba una cantidad";
                    }
                    if (value == 0)
                    {
                        return "Ingrese una cantidad mayor a 0";
                    }
                },
            });
            if (val == null) return;
            if (val.value == null) return;
            // asd
            let data = new FormData();
            data.append("contrato_id", this.contrato_id);
            // SDI
            data.append("sueldo_diario_integral", val.value);
            data.append("sueldo_diario_neto", diario);
            data.append("sueldo_diario_real", this.salario_neto);
            this.isSueldos_loading = true;
            axios.post("rh/sueldos/guardar", data).then(res =>
            {
                this.isSueldos_loading = false;
                if (res.data.status)
                {
                    this.sueldo = {};
                    this.CargarSueldos(
                        0,
                        this.contrato_id,
                        this.nomina_id,
                        this.salario_neto
                    );
                    toastr.success("Sueldo Registrado Correctamente");
                }
                else
                {
                    toastr.error(res.data.mensaje);
                }
            });
        },

        /**
         * Actualizar el SDI
         */
        async ActualizarSDI(id)
        {
            let val = await Swal.fire(
            {
                title: "Actualizar SDI",
                input: "text",
                confirmButtonText: "Guardar",
                cancelButtonText: "Cancelar",
                inputLabel: "Cantidad",
                inputPlaceholder: "Ingrese el SDI",
                showCancelButton: true,
                inputValidator: (value) =>
                {
                    if (!value)
                    {
                        return "Escriba una cantidad";
                    }
                    if (value == 0)
                    {
                        return "Ingrese una cantidad mayor a 0";
                    }
                },
            });
            if (val == null) return;
            if (val.value == null) return;
            let data = new FormData();
            data.append("id", id); // ID del sueldo
            data.append("sueldo_diario_integral", val.value);
            axios.post("rh/sueldos/actualizarsdi", data).then(res =>
            {
                if (res.data.status)
                {
                    this.sueldo = {};
                    this.CargarSueldos(
                        0,
                        this.contrato_id,
                        this.nomina_id,
                        this.salario_neto
                    );
                    toastr.success("Sueldo Actualizado Correctamente");
                }
                else toastr.error(res.data.mensaje);
            })
        },
    },
    mounted()
    {},
};
</script>
