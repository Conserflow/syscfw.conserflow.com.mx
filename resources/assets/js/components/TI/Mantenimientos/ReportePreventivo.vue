<template>
<div class='main'>
    <!-- Card Inicio-->
    <div class='card'>
        <!-- Inicio card-->
        <div class='card-header'>
            <i class='fa fa-align-justify'></i> Reporte de Mantenimiento Preventivo
            <button type='button' class='btn btn-dark float-sm-right' @click='AbrirModalMtto(true)'>
                <i class='fas fa-plus'>&nbsp;</i>Nuevo
            </button>
        </div>
        <div class='card-body'>
            <div class=''>
                <!-- Tabla de Mantenimiento-->
                <div class=''>
                    <v-client-table :columns='columns_mtto' :data='list_mtto' :options='options_mtto' ref='tbl_mtto'>
                        <template slot="mtto.tipo_equipo" slot-scope="props">
                            <p v-if="props.row.mtto.tipo_equipo==1">Cómputo</p>
                            <p v-else-if="props.row.mtto.tipo_equipo==2">Impresión</p>
                            <p v-else-if="props.row.mtto.tipo_equipo==3">Red</p>
                            <p v-else>WFT??</p>
                        </template>
                        <template slot='mtto.id' slot-scope='props'>
                            <div class='btn-group' role='group'>
                                <button id='btn_id' type='button' class='btn btn-outline-dark dropdown-toggle' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false'>
                                    <i class='fas fa-grip-horizontal'></i>&nbsp; Acciones
                                </button>
                                <div class='dropdown-menu'>
                                    <template>
                                        <button type='button' @click='AbrirModalMtto(false, props.row)' class='dropdown-item'>
                                            <i class='fas fa-edit'></i>&nbsp;Detalles
                                        </button>
                                        <button type='button' @click='Reporte(props.row.mtto)' class='dropdown-item'>
                                            <i class='fas fa-file-pdf'></i>&nbsp;Descargar
                                        </button>
                                    </template>
                                </div>
                            </div>
                        </template>
                    </v-client-table>
                </div>
                <!--Card body -->
            </div> <!-- card-->
        </div>
    </div>

    <!-- Modal Mantenimiento -->
    <div class='modal fade' tabindex='-1' :class="{'mostrar' : ver_modal_mtto}" role='dialog' aria-labelledby='myModalLabel' style='display: none;' aria-hidden='true'>
        <div class='modal-dialog modal-dark modal-lg' role='document'>
            <div class='modal-content'>
                <div class='modal-header'>
                    <h4 class='modal-title' v-text='titulo_modal_mtto'></h4>
                    <button type='button' class='close' @click='CerrarModalMtto()' aria-label='Close'>
                        <span aria-hidden='true'>x</span>
                    </button>
                </div>
                <div class='modal-body'>
                    <div class='form-group row'>

                        <label class='col-md-2 form-control-label'>Tipo</label>
                        <div class='col-md-4'>
                            <select class="form-control" v-model="tipo_equipo_buscar">
                                <option value="1">Cómputo</option>
                                <option value="2">Impresión</option>
                                <option value="3">Red</option>
                            </select>
                        </div>
                        <button :disabled="!editar" class="btn btn-dark btn-sm" @click="AbrirModalEquipos()"> Buscar
                            <i class="fas fa-button"></i>
                        </button>
                    </div>

                    <div class='form-group row'>
                        <label class='col-md-2 form-control-label'>No. Serie</label>
                        <div class='col-md-6'>
                            <input type='text' v-validate="'required'" name="no_serie" :disabled=true class='form-control' v-model='mtto_modal.equipo.no_serie'>
                        </div>
                    </div>

                    <div class='form-group row'>
                        <label class='col-md-2 form-control-label'>Marca/Modelo</label>
                        <div class='col-md-6'>
                            <input type='text' :disabled=true v-validate='"required"' class='form-control' v-model='mtto_modal.equipo.marca_modelo' data-vv-name='No. Serie'>
                            <span class="text-danger">{{errors.first('No. Serie')}}</span>
                        </div>
                    </div>

                    <div class='form-group row'>
                        <label class='col-md-2 form-control-label'>Descripción</label>
                        <div class='col-md-8'>
                            <textarea :disabled=true cols="4" class='form-control' v-model='mtto_modal.equipo.descripcion'> </textarea>
                        </div>
                    </div>

                    <div class='form-row'>
                        <div class="col-md-3 mb-3">
                            <label class='form-control-label'>Fecha</label>
                            <input type='date' v-validate='"required"' class='form-control' v-model='mtto_modal.fecha' data-vv-name='Fecha'>
                            <span class="text-danger">{{errors.first('Fecha')}}</span>
                        </div>

                        <div class='col-md-3 mb-3'>
                            <label class='form-control-label'>Hora Inicio</label>
                            <input type="time" min="08:00" max="18:00" v-validate='"required"' class='form-control' v-model='mtto_modal.hora_inicio' data-vv-name='Hora Inicio'>
                            <span class="text-danger">{{errors.first('Hora Inicio')}}</span>
                        </div>

                        <div class='col-md-3 mb-3'>
                            <label class=' form-control-label'>Hora Termino</label>
                            <input type="time" min="08:00" max="18:00" v-validate='"required"' class='form-control' v-model='mtto_modal.hora_termino' data-vv-name='Hora Termino'>
                            <span class="text-danger">{{errors.first('Hora Termino')}}</span>
                        </div>
                    </div>

                    <div class='form-group row'>
                        <label class='col-md-2 form-control-label'>Persona asignada</label>
                        <div class='col-md-9'>
                            <v-select v-validate='"required"' label="nombre" :options="list_empleados" v-model='mtto_modal.empleado_realizo' data-vv-name='Persona asignada'></v-select>
                            <span class="text-danger">{{errors.first('Persona asignada')}}</span>
                        </div>
                    </div>

                    <div class='form-group row'>
                        <label class='col-md-2 form-control-label'>Actividades</label>
                        <div class='col-md-10'>
                            <textarea cols="4" rows="5" v-validate='"required"' class='form-control' v-model='mtto_modal.activades' data-vv-name='Actividades'> </textarea>
                            <span class="text-danger">{{errors.first('Actividades')}}</span>
                        </div>
                    </div>

                    <div class='form-group row'>
                        <label class='col-md-2 form-control-label'>Observaciones</label>
                        <div class='col-md-10'>
                            <textarea cols="4" rows="5" v-validate='"required"' class='form-control' v-model='mtto_modal.observaciones' data-vv-name='Observaciones'> </textarea>
                            <span class="text-danger">{{errors.first('Observaciones')}}</span>
                        </div>
                    </div>

                    <div class='form-group row'>
                        <label class='col-md-2 form-control-label'>Refacciones</label>
                        <vue-element-loading :active="isConsumibles_loading" />
                        <div class='col-md-10'>
                            <!-- Mostrar todos -->
                            <div class="row" v-if="tipoAccion_modal_mtto==1">
                                <div v-for="(consumible,i) in list_consumibles" :key="consumible.id" class="col col-sm-4">
                                    <input class="" type="checkbox" v-model="list_consumibles_checked[i].checked">
                                    <label class="form-control-label">{{consumible.nombre}}</label>
                                </div>
                            </div>
                            <!-- Mostrar solo los checados -->
                            <div class="row" v-if="tipoAccion_modal_mtto==2">
                                <div v-for="(consumible,i) in list_consumibles_checked" :key="consumible.id" class="col col-sm-4">
                                    <input class="" type="checkbox" disabled v-model="list_consumibles_checked[i].checked">
                                    <label class="form-control-label">{{consumible.nombre}}</label>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col text-right mr-2">
                                    <button :disabled="!editar" class="btn btn-sm btn-dark" @click="NuevoConsumible">
                                        <i class="fas fa-plus"></i> Nuevo
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class='form-group row'>
                        <div class='col-md-6'>
                            <label class='col-md-3 form-control-label'>Realizó</label>
                            <v-select label="nombre" :options="list_empleados" v-model='mtto_modal.empleado_realizo' data-vv-name='Realizó' v-validate='"required"'></v-select>
                            <span class="text-danger">{{errors.first('Realizó')}}</span>
                        </div>

                        <div class='col-md-6'>
                            <label class='control-label'>Validó</label>
                            <v-select label="nombre" :options="list_empleados" v-validate='"required"' v-model='mtto_modal.empleado_valido' data-vv-name='Validó'></v-select>
                            <span class="text-danger">{{errors.first('Validó')}}</span>
                        </div>
                    </div>
                </div>
                <div class='modal-footer'>
                    <button type='button' class='btn btn-outline-dark' @click='CerrarModalMtto()'><i class='fas fa-times'></i>&nbsp;Cerrar</button>
                    <button type='button' v-if='tipoAccion_modal_mtto== 1' class='btn btn-secondary' @click='GuardarMtto(1)'><i class='fas fa-save'></i>&nbsp;Guardar</button>
                    <!-- <button type='button' v-if='tipoAccion_modal_mtto==2' class='btn btn-secondary' @click='GuardarMtto(0)'><i class='fas fa-save'></i>&nbsp;Actualizar</button> -->
                </div>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>

    <!-- Modal Equipos -->
    <div class='modal fade' tabindex='-1' :class="{'mostrar' : ver_modal_equipos}" role='dialog' aria-labelledby='myModalLabel' style='display: none;' aria-hidden='true'>
        <div class='modal-dialog modal-dark modal-lg' role='document'>
            <div class='modal-content'>
                <div class='modal-header'>
                    <h4 class='modal-title'>Buscar equipos</h4>
                    <button type='button' class='close' @click='CerrarModalEquipos()' aria-label='Close'>
                        <span aria-hidden='true'>x</span>
                    </button>
                </div>
                <div class='modal-body'>
                    <v-client-table @row-click="SeleccionarEquipo" :columns='columns_equipos' :data='list_equipos' :options='options_equipos'>
                    </v-client-table>
                </div>
                <div class='modal-footer'>
                    <button type='button' class='btn btn-outline-dark' @click='CerrarModalEquipos()'><i class='fas fa-times'></i>&nbsp;Cerrar</button>
                </div>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>

</div> <!-- Main -->
</template>

<script>
/* CAMBIAR UBICACIÓN  */
var config = require('../../Herramientas/config-vuetables-client').call(this);
export default
{
    data()
    {
        return {
            url: "ti/mtto/preventivo",
            list_empleados: [],
            editar:true,            // Tabla 
            ver_modal_mtto: 0,
            tipo_equipo_buscar: 1,
            columns_mtto: [
                "mtto.id",
                "mtto.tipo_equipo",
                "equipo.no_serie",
                "mtto.fecha",
                "mtto.actividades",
                "mtto.empleado_sigado",
                "mtto.empelado_revisa"
            ],
            list_mtto: [],
            options_mtto:
            {
                headings:
                {
                    "mtto.id": "Acciones",
                    "mtto.tipo_equipo": "Tipo",
                    "equipo.no_serie": "No. Serie",
                    "mtto.fecha": "Fecha",
                    "mtto.actividades": "Actividades",
                    "mtto.empleado_sigado": "Responsable",
                    "mtto.empelado_revisa": "Autorizó"
                }, // Headings,
                perPage: 10,
                perPageValues: [],
                skin: config.skin,
                sortIcon: config.sortIcon,
                filterByColumn: true,
                texts: config.texts
            }, //options 
            // Modal
            titulo_modal_mtto: '',
            tipoAccion_modal_mtto: 0,
            mtto_modal:
            {
                equipo:
                {},
            },

            // Modal equipos
            list_consumibles_checked: [],
            list_consumibles: [],
            isConsumibles_loading: false,
            ver_modal_equipos: false,
            columns_equipos: ["no_serie", "marca_modelo", "descripcion", "tipo"],
            list_equipos: [],
            options_equipos:
            {
                perPage: 10,
                perPageValues: [],
                skin: config.skin,
                sortIcon: config.sortIcon,
                filterByColumn: true,
                texts: config.texts,
                listColumns:
                {
                    'tipo': [
                    {
                        id: 1,
                        text: 'Cómputo'
                    },
                    {
                        id: 2,
                        text: 'Impresión'
                    },
                    {
                        id: 3,
                        text: 'Red'
                    }]

                },
            }, //options 
        } // return
    }, //data
    computed:
    {},
    methods:
    {
        AbrirModalMtto(nuevo, model = [])
        {
            // Cargar empleados
            axios.get(this.url + "/obtenerpersonal").then(res =>
            {
                if (res.data.status)
                {
                    this.list_empleados = res.data.empleados;
                }
                else
                {
                    toastr.error(res.data.mensaje);
                }
            });

            this.ver_modal_mtto = true;
            if (nuevo)
            {
                this.editar=true;
                this.ObtenerConsumibles();
                // Mostrar todos los consumibles sin checar
                // Crear nuevo
                this.titulo_modal_mtto = 'Registrar Mantenimiento';
                this.tipoAccion_modal_mtto = 1;
            }
            else
            {
                this.editar=false;
                // Actualizar
                this.titulo_modal_mtto = 'Actualizar Mantenimiento';
                this.tipoAccion_modal_mtto = 2;

                this.mtto_modal.equipo.no_serie = model.equipo.no_serie;
                this.mtto_modal.equipo.marca_modelo = model.equipo.marca_modelo;
                this.mtto_modal.equipo.descripcion = model.equipo.descripcion;
                this.mtto_modal.fecha = model.mtto.fecha;
                this.mtto_modal.hora_inicio = model.mtto.hora_inicio;
                this.mtto_modal.hora_termino = model.mtto.hora_final;
                this.mtto_modal.empleado_realizo = {
                    id: model.mtto.empleado_asignado,
                    nombre: model.mtto.empleado_sigado
                };
                this.mtto_modal.activades = model.mtto.actividades;
                this.mtto_modal.observaciones = model.mtto.observaciones;
                this.mtto_modal.empleado_valido = {
                    id: model.mtto.empleado_autoriza,
                    nombre: model.mtto.empelado_revisa
                };
                // Activar consumibles.  CAMBIAR
                this.list_consumibles_checked = model.consumibles;

            }
        },

        CerrarModalMtto()
        {
            this.ver_modal_mtto = false;
            this.mtto_modal = {
                equipo:
                {},
            };
        },

        /**
         * Cargar los consumibles de los mantenimientos
         */
        ObtenerConsumibles(list_checked = [])
        {
            this.isConsumibles_loading = true;
            axios.get(this.url + "/obtenerconsumibles").then(res =>
            {
                this.isConsumibles_loading = false;
                if (res.data.status)
                {
                    let aux = [];
                    this.list_consumibles = res.data.consumibles;

                    this.list_consumibles.forEach(c =>
                    {
                        aux.push(
                        {
                            "id": c.id,
                            "checked": false,
                        });
                    });
                    this.list_consumibles_checked = aux;
                }
                else
                {
                    toast.errror(res.data.mensaje);
                }
            })
        },

        /**
         * Muestra todos los equipos para su búqueda
         */
        AbrirModalEquipos()
        {
            this.ver_modal_equipos = true;
            // Buscar equipos
            axios.get(this.url + "/obtenerequipos/" + this.tipo_equipo_buscar).then(res =>
            {
                if (res.data.status)
                {
                    this.list_equipos = res.data.equipos;
                }
                else
                {
                    toast.error(res.data.mensaje);
                }
            });
        },

        CerrarModalEquipos()
        {
            this.ver_modal_equipos = 0;
        },

        /**
         * Selecciona el equipo
         */
        SeleccionarEquipo(data)
        {
            let equipo = data.row;
            console.error(data.row);
            this.mtto_modal.equipo = {
                id: equipo.id,
                no_serie: equipo.no_serie,
                marca_modelo: equipo.marca_modelo,
                descripcion: equipo.descripcion,
                tipo: equipo.tipo
            };
            this.ver_modal_equipos = false;
        },

        /**
         * Guardar mantenimiento
         */
        GuardarMtto(nuevo)
        {
            this.$validator.validate().then(isValid =>
            {
                if (!isValid)
                {
                    Swal.fire(
                    {
                        title: 'Llena todos los campos, padre santo',
                        text: '¿Acaso eres de #Cantidad?',
                        imageUrl: 'https://i.insider.com/5d9b47375d21aa31517a1c09?width=1000&format=jpeg&auto=webp',
                        imageWidth: 300,
                        imageHeight: 150,
                        imageAlt: 'No se ve dx',
                    });
                    return;
                }
                if (this.mtto_modal.equipo.id == null)
                {
                    toastr.warning("Weeee, te falta el equipo");
                    return;
                }
                this.isMtto_loading = true;

                //Obtener Refacciones
                let aux_ids = this.list_consumibles_checked.filter(c => c.checked);
                let ms = "";
                aux_ids.forEach(c =>
                {
                    ms += c.id + "&";
                });

                let data = new FormData();
                if (!nuevo)
                    data.append("id", this.mtto_modal.id);
                data.append("fecha", this.mtto_modal.fecha);
                data.append("tipo_equipo", this.mtto_modal.equipo.tipo);
                data.append("equipo_id", this.mtto_modal.equipo.id);
                data.append("hora_inicio", this.mtto_modal.hora_inicio);
                data.append("hora_final", this.mtto_modal.hora_termino);
                data.append("empleado_asignado", this.mtto_modal.empleado_realizo.id);
                data.append("empleado_autoriza", this.mtto_modal.empleado_valido.id);
                data.append("actividades", this.mtto_modal.activades);
                data.append("observaciones", this.mtto_modal.observaciones);
                data.append("list_consumibles", ms);

                axios.post(this.url + "/guardar", data).then(res =>
                {
                    this.isMtto_loading = false;
                    if (res.data.status)
                    {
                        toastr.success("Registrado correctamente");
                        this.CerrarModalMtto();
                        this.ObtenerMttos();
                    }
                    else
                    {
                        toastr.error(res.data.mensaje);
                    }
                });
            })
        },

        /**
         * Obtiene los mantenimientos
         */
        ObtenerMttos()
        {
            this.isMttos_loading = true;
            axios.get(this.url + "/obtener").then(res =>
            {
                this.isMttos_loading = true;
                if (res.data.status)
                {
                    this.list_mtto = res.data.mantenimientos;
                }
                else
                {
                    toastr.error(res.data.mensaje);
                }
            });
        },

        /**
         * Registra un nuevo cosumible
         */
        NuevoConsumible()
        {
            Swal.fire(
            {
                title: "Registrar Consumible/Refacción",
                input: "text",
                showCancelButton: true,
                confirmButtonText: "Guardar",
                showLoaderOnConfirm: true,
            }).then(result =>
            {
                if (result.value != null)
                {
                    if (result.value.length >= 3)
                    {
                        axios.post(this.url + "registrarconsumible",
                        {
                            nombre: result.value
                        }).then(res =>
                        {
                            if (res.data.status)
                            {
                                toastr.success("Registrado");
                                this.ObtenerConsumibles();
                            }
                            else
                            {
                                toast.error(res.data.mensaje);
                            }
                        })
                    }
                }
            });
        },

        /**
         * Generar Reporte en PDF
         */
        Reporte(mtto)
        {
            window.open(this.url + "/reporte/" + mtto.id, "_blank");
        }

    }, // Fin metodos
    mounted()
    {
        this.ObtenerMttos();
    }
}
</script>
