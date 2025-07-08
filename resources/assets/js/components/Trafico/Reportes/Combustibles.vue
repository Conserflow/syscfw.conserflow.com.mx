<template>
<main class="main">
    <div class="container-fluid">

        <div class="card">
            <div class="card-header">
                <i class="fa fa-align-justify"></i> Combustible
                <!-- Seleccionar por empresa -->
                <!-- <div class="dropdown float-sm-right">
                    <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenu2" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        Empresa
                    </button>
                    <div class="dropdown-menu" aria-labelledby="dropdownMenu2">
                        <button class="dropdown-item" type="button" @click="MostrarEmpresa(1)">Conserflow</button>
                        <button class="dropdown-item" type="button" @click="MostrarEmpresa(2)">Constructora</button>
                        <button class="dropdown-item" type="button" @click="MostrarEmpresa(3)">Pendiente</button>
                    </div>
                </div> -->
                <button v-show="PermisosCRUD.Create" type="button" @click="abrirModal(1)" class="btn btn-dark float-sm-right mr-1">
                    <i class="fas fa-plus mr-1"></i>Nuevo
                </button>
            </div>
            <div class="card-body">
                <p>
                    <a class="btn btn-secondary" data-toggle="collapse" href="#multiCollapseExample1" role="button" aria-expanded="false" aria-controls="multiCollapseExample1">
                        Generar reporte
                    </a>
                </p>
                <div class="row">
                    <div class="col">
                        <div class="collapse multi-collapse" id="multiCollapseExample1">
                            <div class="container row">
                                <div class="col-md-6">
                                    <div class="">
                                        <label>Inicio</label>
                                        <input type="date" v-model="reporte.inicio" class="form-control">
                                        <label>Fin</label>
                                        <input type="date" v-model="reporte.fin" class="form-control">
                                        <select class="mt-1 form-control" v-model="ubicacion_formato">
                                            <option value="1">Tehuacán</option>
                                            <option value="2">Coatzacoalcos</option>
                                        </select>
                                        <button @click="Reporte" class="btn btn-dark mt-2">Generar</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <br>

                <vue-element-loading :active="isObtener_loading" />
                <v-client-table :data="list_combustible" :columns="columns" :options="options">
                    <template slot='id' slot-scope='props'>
                        <div class='btn-group' role='group'>
                            <button id='btn_id' type='button' class='btn btn-outline-dark dropdown-toggle' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false'>
                                <i class='fas fa-grip-horizontal'></i>
                            </button>
                            <div class='dropdown-menu'>
                                <template>
                                    <button type='button' v-if="props.row.condicion==1" @click='abrirModal(2, props.row)' class='dropdown-item'>
                                        <i class='fas fa-edit'></i>Actualizar
                                    </button>
                                    <button type='button' v-if="props.row.condicion==1" @click='eliminar(props.row.id)' class='dropdown-item'>
                                        <i class='fas fa-trash'></i>Cancelar
                                    </button>
                                </template>
                            </div>
                        </div>
                    </template>
                    <template slot='condicion' slot-scope='props'>
                        <button v-if="props.row.condicion==1" class="btn btn-outline-success">Activo</button>
                        <button v-else class="btn btn-outline-danger">Cancelado</button>
                    </template>
                    <template slot='vp_nombre' slot-scope='props'>
                        <p v-if="props.row.vp_nombre!=null">{{props.row.vp_nombre}}</p>
                        <p v-else class="text-warning">{{props.row.proveedor}}</p>
                    </template>
                    <template slot='unidad' slot-scope='props'>
                        <p v-if="props.row.unidad_id==-1">{{props.row.cantidad_bidones}} Bidones</p>
                        <p v-else-if="props.row.unidad_id==-99">CANCELADO</p>
                        <p v-else>{{props.row.unidad}} - {{props.row.modelo}} </p>
                    </template>

                    <template slot='nombre_corto' slot-scope='props'>
                        <p v-if="props.row.unidad_id==-99">CANCELADO</p>
                        <p v-else>{{props.row.nombre_corto}}</p>
                    </template>
                    <template slot='operador' slot-scope='props'>
                        <p v-if="props.row.unidad_id==-99">CANCELADO</p>
                        <p v-else>{{props.row.operador}}</p>
                    </template>
                    <template slot='tipo_deposito' slot-scope='props'>
                        <p v-if="props.row.tipo_deposito==1">VALE</p>
                        <p v-else-if="props.row.tipo_deposito==2">TRANSFERENCIA</p>
                        <p v-else>-</p>
                    </template>
                    <template slot='ubicacion' slot-scope='props'>
                        <p v-if="props.row.ubicacion==1">TEHUACÁN</p>
                        <p v-else-if="props.row.ubicacion">COATZACOALCOS</p>
                        <p v-else>-</p>
                    </template>
                </v-client-table>
            </div>
        </div>

        <!-- Modal -->
        <div class='modal fade' tabindex='-1' :class="{'mostrar' : modal}" role='dialog' aria-labelledby='myModalLabel' style='display: none;' aria-hidden='true'>
            <div class='modal-dialog modal-dark modal-lg' role='document'>
                <div class='modal-content'>
                    <div class='modal-header'>
                        <h4 class='modal-title' v-text='titulo'></h4>
                        <button type='button' class='close' @click='CerrarModal()' aria-label='Close'>
                            <span aria-hidden='true'>×</span>
                        </button>
                    </div>
                    <div class='modal-body'>

                        <div class="form-row">
                            <div class="col-md-6 mb-3">
                                <label>Proveedor</label>
                                <v-select :options="list_proveedores" v-model="proveedor" v-validate="'required'" data-vv-name="Proveedor" label="razon_social"></v-select>
                                <span class="text-danger">{{errors.first('Proveedor')}}</span>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="col-md-4 mb-3">
                                <label>Ubicación</label>
                                <select class="form-control" v-model="ubicacion">
                                    <option value="1">TEHUACÁN</option>
                                    <option value="2">COATZACOALCOS</option>
                                </select>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label>Tipo</label>
                                <select class="form-control" v-model="tipo_deposito" @input="CambiarTipoDeposito">
                                    <option value="1">VALE</option>
                                    <option value="2">TRANSFERENCIA</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="col-md-3 mb-3">
                                <label>Folio</label>
                                <input v-if="tipo_deposito==1" type="number" min="1" step="1" max="3000" class="form-control" v-validate="'required'" data-vv-name="Folio" v-model="folio">
                                <input v-if="tipo_deposito==2" disabled type="text" class="form-control" v-validate="'required'" data-vv-name="Folio" v-model="folio">
                                <span class="text-danger">{{errors.first('Folio')}}</span>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label>Fecha</label>
                                <input type="date" class="form-control" v-validate="'required'" data-vv-name="Fecha" v-model="fecha">
                                <span class="text-danger">{{errors.first('Fecha')}}</span>
                            </div>
                            <div class="col-md-2 mb-3">
                                <label>Factura</label>
                                <input :disabled="cancelado" type="text" class="form-control" v-validate="'required'" data-vv-name="Factura" v-model="factura">
                                <span class="text-danger">{{errors.first('Factura')}}</span>
                            </div>
                            <div class="col-md-2 mb-3">
                                <div class="form-check form-inline">
                                    <label class="form-check-label">
                                        ¿Cancelado?
                                    </label>
                                    <label class="switch switch-default switch-pill switch-dark">
                                        <input type="checkbox" class="switch-input" v-model="cancelado" @input="CancelarVale">
                                        <span class="switch-label"></span>
                                        <span class="switch-handle"></span>
                                    </label>
                                    <span class="ml-3">{{cancelado?"Sí":"No"}}</span>
                                </div>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="col-md-6 mb-3">
                                <label>Proyecto</label>
                                <v-select :disabled="cancelado" :options="listaProyectos" v-model="proyecto" label="nombre_corto" name="name" data-vv-name="Proyecto" v-validate="'required'"></v-select>
                                <span class="text-danger">{{errors.first('Proyecto')}}</span>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Operador</label>
                                <v-select :disabled="cancelado" :options="listaEmpleados" label="nombre" v-model="operador" data-vv-name="operador" v-validate="'required'"></v-select>
                                <span class="text-danger">{{errors.first('operador')}}</span>
                            </div>
                        </div>
                        <hr>

                        <div class="form-row">
                            <div class="col-md-3 mb-3">
                                <template v-if="tipo_vale==0">
                                    <label>Tipo</label>
                                    <select :disabled="cancelado" class="form-control" v-model="tipo_vale">
                                        <option value="1">Vehículo</option>
                                        <option value="2">Bidón</option>
                                    </select>
                                </template>
                                <template v-if="tipo_vale==1">
                                    <label>Unidad</label>
                                    <v-select :disabled="cancelado" :options="list_unidades" label="placas" v-model="unidad" data-vv-name="unidad" v-validate="'required'"></v-select>
                                    <span class="text-danger">{{errors.first('unidad')}}</span>
                                </template>
                                <template v-if="tipo_vale==2">
                                    <label>Cantidad</label>
                                    <select :disabled="cancelado" class="form-control" v-model="cantidad_bidones">
                                        <option value="1">1 Bidón</option>
                                        <option value="2">2 Bidones</option>
                                        <option value="3">3 Bidones</option>
                                        <option value="4">4 Bidones</option>
                                        <option value="5">5 Bidones</option>
                                        <option value="6">6 Bidones</option>
                                        <option value="7">7 Bidones</option>
                                        <option value="8">8 Bidones</option>
                                        <option value="9">9 Bidones</option>
                                        <option value="10">10 Bidones</option>
                                    </select>
                                </template>
                            </div>

                            <template v-if="tipo_vale==1">
                                <div class="col-md-2 mb-3">
                                    <label>Placas</label>
                                    <input disabled type="text" class="form-control" v-validate="'required'" data-vv-name="Placas" v-model="unidad.placas">
                                    <span class="text-danger">{{errors.first('Placas')}}</span>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label>Modelo</label>
                                    <input disabled type="text" class="form-control" v-validate="'required'" data-vv-name="Modelo" v-model="unidad.modelo">
                                    <span class="text-danger">{{errors.first('Modelo')}}</span>
                                </div>
                            </template>
                        </div>
                        <div class="form-row">
                            <div class="col-md-3 mb-3">
                                <label>Producto</label>
                                <select :disabled="cancelado" class="form-control" v-model="producto">
                                    <option value="1" selected>GASOLINA MAGNA</option>
                                    <option value="2">GASOLINA PREMIUM</option>
                                    <option value="3">DIESEL</option>
                                </select>
                                <span class="text-danger">{{errors.first('Producto')}}</span>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label>Horas motor</label>
                                <input :disabled="cancelado" type="number" class="form-control" step="1" v-validate="'required|decimal:4'" data-vv-name="Horas" v-model="horas">
                                <span v-if="!cancelado" class="text-danger">{{errors.first('Horas')}}</span>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label>Kilometraje</label>
                                <input :disabled="cancelado" type="number" class="form-control" v-validate="'required|decimal:4'" data-vv-name="Kilometraje" v-model="kilometraje">
                                <span v-if="!cancelado" class="text-danger">{{errors.first('Kilometraje')}}</span>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label>Cantidad</label>
                                <input :disabled="cancelado" type="number" min="1" class="form-control" v-validate="'required|decimal:4'" data-vv-name="Cantidad" v-model="cantidad">
                                <span v-if="!cancelado" class="text-danger">{{errors.first('Cantidad')}}</span>
                            </div>
                        </div>
                        <hr>
                        <div class="form-row">
                            <div class="col-md-3 mb-3">
                                <label>Precio</label>
                                <input :disabled="cancelado" type="number" min="1" class="form-control" v-validate="'required|decimal:4'" data-vv-name="Precio" v-model="precio">
                                <span v-if="!cancelado" class="text-danger">{{errors.first('Precio')}}</span>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label>SubTotal</label>
                                <input :disabled="cancelado" type="number" min="1" v-validate="'required'" class="form-control" data-vv-name="SubTotal" v-model="subtotal">
                                <span v-if="!cancelado" class="text-danger">{{errors.first('SubTotal')}}</span>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label>IVA</label>
                                <input :disabled="cancelado" type="number" min="1" v-validate="'required'" class="form-control" data-vv-name="IVA" v-model="iva">
                                <span v-if="!cancelado" class="text-danger">{{errors.first('IVA')}}</span>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label>Total</label>
                                <input :disabled="cancelado" type="number" min="1" class="form-control" v-validate="'required'" data-vv-name="Total" v-model="total">
                                <span v-if="!cancelado" class="text-danger">{{errors.first('Total')}}</span>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="col-md-4 mb-3">
                                <label></label>
                                <input :disabled="cancelado" type="file" accept="image/*" ref="adjunto" @change="cargarImg()" />
                            </div>
                        </div>

                        <div class="form-row">

                            <div class="form-group col-md-2">
                                <label><b>#</b></label>
                            </div>
                            <div class="form-group col-md-6">
                                <label><b>Preview</b></label>
                            </div>
                            <div class="form-group col-md-1">
                                <label><b>.</b></label>
                            </div>
                        </div>
                        <li :key="index" v-for="(vi, index) in img" class="list-group-item">
                            <div class="form-row">

                                <div class="form-group col-md-2">
                                    <label>{{index + 1}}</label>
                                </div>
                                <div class="form-group col-md-6">
                                    <label></label>
                                    <img :src="vi.name" width="200px" height="150px">
                                </div>
                                <div class="form-group col-md-1">
                                    <a @click="deleteu(vi, index)">
                                        <span class="fas fa-trash" arial-hidden="true"></span>
                                    </a>
                                </div>
                            </div>
                        </li>

                    </div>
                    <div class='modal-footer'>
                        <vue-element-loading :active="isGuardar_loading" />
                        <div>
                            <button type='button' class='btn btn-outline-dark' @click='CerrarModal()'><i class='mr-1 fas fa-times'></i>Cerrar</button>
                            <button type='button' v-if='tipoAccion == 1' class='btn btn-secondary' @click='Guardar(1)'><i class='fas fa-save mr-1'></i>Guardar</button>
                            <button type='button' v-if='tipoAccion == 2' class='btn btn-secondary' @click='Guardar(0)'><i class='fas fa-save mr-1'></i>Actualizar</button>
                        </div>
                    </div>
                </div>
                <!-- /.modal-content -->
            </div>
            <!-- /.modal-dialog -->
        </div>
        <!-- Modal -->

    </div>
</main>
</template>

<script>
import Utilerias from '../../Herramientas/utilerias.js';
var config = require('../../Herramientas/config-vuetables-client').call(this);

export default
{
    data()
    {
        return {
            PermisosCRUD:
            {},
            id: 0,
            cantidad_bidones: 1,
            tipo_vale: 0,
            proveedor:
            {},
            isGuardar_loading: false,
            isObtener_loading: false,
            empresa_nombre: "",
            empresa: 0, // Todos
            folio: '',
            fecha: '',
            tipo_deposito: 1,
            proyecto:
            {},
            list_proveedores: [],
            operador: '',
            factura: '',
            placas: '',
            unidad:
            {},
            producto: '',
            kilometraje: '',
            cancelado: false,
            cantidad: '',
            precio: '',
            subtotal: '',
            iva: '',
            total: '',
            ubicacion: 1,
            reporte:
            {
                inicio: "",
                fin: "",
            },
            listaEmpleados: [],
            listaProyectos: [],
            list_unidades: [],
            total_aux: 0,
            horas: 0,
            img: [],

            list_combustible: [],
            columns: [
                'id',
                'vp_nombre',
                'folio',
                'fecha',
                'nombre_corto',
                'operador',
                'factura',
                'unidad',
                "cantidad_bidones",
                'total',
                "condicion",
                "ubicacion",
                "tipo_deposito"
            ],
            options:
            {
                headings:
                {
                    id: 'Acciones',
                    vp_nombre: "Proveedor",
                    nombre_corto: "Proyecto",
                    tipo_deposito: "Tipo"
                },
                perPage: 10,
                perPageValues: [],
                skin: config.skin,
                sortIcon: config.sortIcon,
                filterByColumn: true,
                texts: config.texts,
                listColumns:
                {
                    "condicion": [
                    {
                        id: 1,
                        text: "Activo"
                    },
                    {
                        id: 0,
                        text: "Cancelado"
                    }],
                    "ubicacion": [
                    {
                        id: 1,
                        text: "Tehuacán"
                    },
                    {
                        id: 2,
                        text: "Coatzacoalcos"
                    }, ],
                    "tipo_deposito": [
                    {
                        id: 1,
                        text: "VALE"
                    },
                    {
                        id: 2,
                        text: "TRANSFERENCIA"
                    }, ]
                }
            },

            modal: 0,
            titulo: '',
            tipoAccion: 0,
            ubicacion_formato: 1
        }
    },
    methods:
    {

        /**
         * Cambiar empresa
         */
        MostrarEmpresa(empresa_id)
        {
            this.empresa = empresa_id;
            if (empresa_id == 1) this.empresa_nombre = "CONSERFLOW";
            else if (empresa_id == 2) this.empresa_nombre = "CSCT";
            else this.empresa_nombre = "PENDIENTE";
            // this.empresa_nombre=empresa_id==1?"":"CSCT";
            this.ObtenerCombustible();
        },
        /**
         * Obtiene los registros del combustible
         */
        ObtenerCombustible()
        {
            this.isObtener_loading = true;
            axios.get('vehiculos/combustible/obtener/' + this.empresa).then(res =>
            {
                this.isObtener_loading = false;
                if (res.data.status)
                {
                    this.list_combustible = res.data.combustible;
                }
                else
                    toastr.error(res.data.mensaje);
            });
        },

        ObtenerEmpleados()
        {
            axios.get('generales/empleadoactivos').then(res =>
            {
                if (res.data.status)
                {
                    this.listaEmpleados = res.data.empleados;
                }
                else
                {
                    toastr.error(res.data.mensaje);
                }
            })
        },

        ObtenerProyectos()
        {
            axios.get("generales/proyectos/1").then(res =>
            {
                if (res.data.status)
                {
                    this.listaProyectos = res.data.proyectos;
                }
                else
                {
                    toastr.mensaje(res.data.mensaje);
                }
            })
        },

        ObtenerProveedores()
        {
            axios.get("vehiculos/proveedor/obtener").then(res =>
            {
                if (res.data.status)
                {
                    this.list_proveedores = res.data.proveedores;
                }
                else
                {
                    toastr.error(res.data.mensaje);
                }
            });
        },

        ObtenerUnidades()
        {
            axios.get("vehiculos/combustible/obtenerunidades").then(res =>
            {
                if (res.data.status)
                {
                    this.list_unidades = res.data.unidades;
                }
                else toastr.error(ress.data.mensaje);
            })
        },

        abrirModal(edo, data = [])
        {
            this.ObtenerEmpleados();
            this.ObtenerProyectos();
            this.ObtenerUnidades();
            this.ObtenerProveedores();
            this.modal = 1;
            if (edo == 1)
            {
                this.titulo = 'Guardar';
                this.tipoAccion = 1;
            }
            else if (edo == 2)
            {
                if (data["unidad_id"] == -1) // Bidones
                {
                    this.tipo_vale = 2;
                    this.cantidad_bidones = data["cantidad_bidones"];
                }
                else
                {
                    this.tipo_vale = 1;
                }
                this.proveedor = {
                    id: data["vp_id"],
                    razon_social: data["vp_nombre"]
                };
                this.titulo = 'Actualizar';
                this.tipoAccion = 2;
                this.id = data['id'];
                // this.proveedor = data['proveedor'];
                this.folio = data['folio'];
                this.fecha = data['fecha'];
                this.proyecto = {
                    id: data['proyecto_id'],
                    nombre_corto: data['nombre_corto']
                };
                this.operador = {
                    id: data["operador_id"],
                    nombre: data['operador']
                };
                this.factura = data['factura'];
                this.unidad = {
                    id: data["unidad_id"],
                    placas: data['placas'],
                    unidad: data['unidad'],
                };
                this.producto = data['producto_id'];
                this.horas = data['horas'];
                this.kilometraje = data['kilometraje'];
                this.cantidad = data['cantidad'];
                this.precio = data['precio'];
                this.subtotal = data['subtotal'];
                this.iva = data['iva'];
                this.ubicacion = data['ubicacion'];
                this.total = data['total'];
                this.tipo_deposito = data['tipo_deposito'];
                this.getImages(data['id']);
            }
        },

        getImages(id)
        {
            this.img = [];
            axios.get('vehiculos/combustible/obtenerimg/' + id).then(response =>
            {
                if (response.data.length != 0)
                {
                    this.img.push(
                    {
                        id: response.data.id,
                        name: response.data.img
                    });
                }
            })
        },

        vaciar()
        {
            this.id = 0;
            this.folio = '';
            this.tipo_deposito = 1;
            this.fecha = '';
            this.proyecto = {};
            this.operador = {};
            this.factura = '';
            this.placas = '';
            this.unidad = {};
            this.producto = 1;
            this.kilometraje = 0;
            this.cantidad = 0;
            this.precio = 0;
            this.subtotal = 0;
            this.iva = 0;
            this.total = 0;
            this.img = [];
        },

        CerrarModal()
        {
            this.modal = 0;
            this.vaciar();
            this.proyecto={};
            this.tipo_vale = 0;
        },

        cargarImg()
        {
            if (this.img.length > 0)
            {
                toastr.warning('Solo se puede adjuntar un archivo');
            }
            else
            {
                const selectedImage = this.$refs.adjunto.files[0];
                this.imageToBase64(selectedImage);
            }
        },

        imageToBase64(file)
        {
            var reader = new FileReader()
            reader.readAsDataURL(file)
            reader.onload = () =>
            {
                this.img.push(
                {
                    id: 0,
                    name: reader.result
                });
            }
            reader.onerror = function (error)
            {
                console.log('Error: ', error)
            }
        },

        /**
         * Registrar el vale de combustible
         */
        Guardar(nuevo)
        {
            this.$validator.validate().then(isValid =>
            {
                if (!isValid) return;

                this.isGuardar_loading = true;
                let data = new FormData();
                // Comprobar imagen
                if (this.img.length == 1)
                    data.append("adjunto", this.img[0]["name"]);
                if (this.tipo_vale == 2)
                {
                    this.unidad.id = -1;
                }
                else
                {
                    this.cantidad_bidones = 0;
                }
                if (this.cancelado) data.append("condicion", 0);
                if (!nuevo)
                    data.append("id", this.id);
                data.append("proveedor_id", this.proveedor.id);
                data.append("folio", this.folio);
                data.append("fecha", this.fecha);
                data.append("proyecto_id", this.proyecto.id);
                data.append("cantidad_bidones", this.cantidad_bidones);
                data.append("operador_id", this.operador.id);
                data.append("factura", this.factura);
                data.append("tipo_deposito", this.tipo_deposito);
                data.append("ubicacion", this.ubicacion);
                data.append("unidad_id", this.unidad.id);
                data.append("producto_id", this.producto);
                data.append("kilometraje", this.kilometraje);
                data.append("horas", this.horas);
                data.append("cantidad", this.cantidad);
                data.append("precio", this.precio);
                data.append("subtotal", this.subtotal);
                data.append("iva", this.iva);
                data.append("total", this.total);
                axios.post("vehiculos/combustible/guardar", data).then(res =>
                {
                    this.isGuardar_loading = false;
                    if (res.data.status)
                    {
                        toastr.success('Registrado correctamente');
                        this.ObtenerCombustible();
                        this.CerrarModal();
                    }
                    else
                    {
                        toastr.error(res.data.mensaje);
                    }
                }).catch(e =>
                {
                    console.error(e);
                });
            });
        },

        deleteu(data, index)
        {
            if (data.id == 0)
            {
                this.img.splice(index, 1);
            }
            else if (data.id != 0)
            {
                axios.get('vehiculos/combustible/borrarimg/' + data.id).then(response =>
                {
                    this.getImages(this.id);
                }).catch(e =>
                {
                    console.error(e);
                });
            }
        },

        eliminar(id)
        {
            axios.get('vehiculos/combustible/eliminar/' + id).then(res =>
            {
                if (res.data.status)
                {
                    toastr.success('Eliminado correctamente');
                    this.ObtenerCombustible();
                }
                else
                {
                    toastr.error(res.data.mensaje);
                }
            })
        },

        // Registrar vale como cancelado
        CancelarVale()
        {
            this.proyecto = {
                id: -99,
                nombre_corto: "N/A"
            };
            this.operador = {
                id: -99,
                nombre: "N/A"
            };
            this.tipo_vale = 1;
            this.unidad = {
                id: -99,
                placas: "N/A",
                modelo: "N/A"
            };
            this.horas = 0;
            this.kilometraje = 0;
            this.cantidad = 0;
            this.cantidad_bidones = 0;
            this.precio = 0;
            this.producto = 0;
            this.subtotal = 0;
            this.factura = "CANCELADO";
            this.iva = 0;
            this.total = 0;
        },
        CambiarTipoDeposito()
        {
            this.folio = "TRANSFERENCIA";
        },

        Reporte()
        {
            if (this.reporte.inicio == "") return;
            if (this.reporte.fin == "") return;
            window.open('vehiculos/combustible/reporte/' + this.reporte.inicio + "&" +
                this.reporte.fin + "&" + this.ubicacion_formato, '_blank');
        },
    },
    mounted()
    {
        this.PermisosCRUD = Utilerias.getCRUD(this.$route.path);
        this.MostrarEmpresa(1);
    }
}
</script>
