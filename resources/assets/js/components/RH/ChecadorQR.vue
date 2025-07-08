<template>
<main class="main">
    <!-- checador  -->
    <div class="container-fluid">
        <div class="card">
            <div class="card-body">
                <span class="text-danger">{{error}}</span>
                <div class="form-row">
                    <label class="form-control-label mt-1 mr-2" for="Ubicación">Ubicación</label>
                    <div class="col-md-3 mb-3">
                        <template v-if="tipo_ubicacion>0">
                            <select class="form-control" v-model="tipo_ubicacion">
                                <option value="1">TEHUACAN</option>
                                <option value="2">COATZACOALCOS</option>
                                <option value="0">OTRO</option>
                            </select>
                        </template>
                        <template v-else>
                            <input type="text" class="form-control" v-model="ubicacion" maxlength="25">
                        </template>
                    </div>
                    <div class="col-md-7 mb-3">
                        <h3 style="text-align: center;">Checador QR </h3>
                    </div>
                </div>
                <div class="form-row">
                    <div class="col-md-6 mb-3" style=" transform: scaleX(-1);">
                        <qrcode-stream @decode="onDecode" @init="onInit" />
                    </div>
                    <div class="col-md-6 mb-3">
                        <br>
                        <h1 style="font-family: 'Share Tech Mono', monospace;text-align: center;">
                            <digital-clock :blink="true" :displaySeconds="true" />
                        </h1>
                        <br>
                        <h2 style="font-family: 'Share Tech Mono', monospace;text-align: center;">{{empleado}}</h2>
                        <br>
                        <h2 style="font-family: 'Share Tech Mono', monospace;text-align: center;">{{hora_reg}}</h2>
                    </div>
                </div>
                <div v-show="PermisosCrud.Create">

                    <div class="form-row">

                        <div class="form-group col-md-8" style="text-align: center;">
                            <label><b>REGISTROS SIN SINCRONIZACIÓN</b></label>
                        </div>
                        <div class="form-group col-md-2">
                            <button class="btn btn-outline-success" @click="sincronizar()"><i class="fas fa-redo-alt"></i>Sincronizar</button>
                        </div>

                    </div>
                    <li :key="index" v-for="(vi, index) in registros_fail" class="list-group-item">
                        <div class="form-row">
                            <div class="form-group col-md-2">
                                <label>{{index + 1}}</label>
                            </div>
                            <div class="form-group col-md-8">
                                <label>{{vi}}</label>
                            </div>
                        </div>
                    </li>
                </div>
            </div>
        </div>
    </div>
</main>
</template>

<script>
import Utilerias from '../Herramientas/utilerias.js';
var config = require('../Herramientas/config-vuetables-client').call(this);
import DigitalClock from "vue-digital-clock";

export default
{
    data()
    {
        return {
            message: '',
            counter: 0,
            interval: null,
            tipo_ubicacion: 2,
            ubicacion: "CIUDAD",
            PermisosCrud:
            {},
            fecha_reg: '',
            hora_reg: '',
            date: '',
            date_two: '',
            registros_fail: [],

            fecha_i: '',
            fecha_f: '',
            result: '',
            error: '',
            empleado: '',
            id_empleado: 0,
            id: 0,
            cantidad: 1,
            tipo: 'Caretas',
            options:
            {
                headings:
                {},
                perPage: 10,
                perPageValues: [],
                skin: config.skin,
                sortIcon: config.sortIcon,
                filterByColumn: true,
                texts: config.texts
            },
        }
    },
    components:
    {
        DigitalClock,
    },

    methods:
    {

        zeroPadding(num, digit)
        {
            var zero = '';
            for (var i = 0; i < digit; i++)
            {
                zero += '0';
            }
            return (zero + num).slice(-digit);
        },

        InitCrud()
        {
            this.PermisosCrud = Utilerias.getCRUD(this.$route.path);
        },

        async onInit(promise)
        {
            try
            {

                await promise
            }
            catch (error)
            {
                if (error.name === 'NotAllowedError')
                {
                    this.error = "ERROR: Permita el acceso a la camara"
                }
                else if (error.name === 'NotFoundError')
                {
                    this.error = "ERROR: No existe camara en este dispositivo"
                }
                else if (error.name === 'NotSupportedError')
                {
                    this.error = "ERROR: No es suguro la activacion de la camara (HTTPS, localhost)"
                }
                else if (error.name === 'NotReadableError')
                {
                    this.error = "ERROR: la camra ya esta en uso?"
                }
                else if (error.name === 'OverconstrainedError')
                {
                    this.error = "ERROR: installed cameras are not suitable"
                }
                else if (error.name === 'StreamApiNotSupportedError')
                {
                    this.error = "ERROR: Stream API no soportado para este navegador"
                }
            }
        },

        onDecode(result)
        {
            console.error(result);
            this.result = result
            var porciones = this.result.split('|');

            if (porciones.length == 2)
            {
                this.empleado = porciones[1];
                this.id_empleado = porciones[0];
                var cd = new Date();
                this.fecha_reg = this.zeroPadding(cd.getFullYear(), 4) +
                    '-' + this.zeroPadding(cd.getMonth() + 1, 2) +
                    '-' + this.zeroPadding(cd.getDate(), 2);
                this.hora_reg = this.zeroPadding(cd.getHours(), 2) +
                    ':' + this.zeroPadding(cd.getMinutes(), 2) +
                    ':' + this.zeroPadding(cd.getSeconds(), 2);
                this.SwalMensaje();

                // obtener ubicacion
                let aux_ubicacion = "";
                if (this.tipo_ubicacion == 0) aux_ubicacion = this.ubicacion
                else
                {
                    aux_ubicacion = this.tipo_ubicacion == 1 ? "TEHUACAN" : "COATZACOALCOS";
                }
                if (navigator.onLine)
                {
                    // el navegador está conectado a la red
                    axios.post("rh/checador/guardar",
                    {
                        empleado: this.empleado,
                        empleado_id: this.id_empleado,
                        fecha: this.fecha_reg,
                        hora: this.hora_reg,
                        ubicacion: aux_ubicacion
                    }).then(res =>
                    {
                        if (res.data.status)
                        {
                            console.log("ok");
                        }
                        else
                        {
                            this.addC();
                        }
                    }).catch(e =>
                    {
                        this.addC();
                    });
                }
                else
                {
                    // el navegador no está conectado a la red
                    this.addC();
                }
            }

        },

        SwalMensaje()
        {
            swal(
            {
                type: 'success',
                title: 'Acceso Correcto',
                position: 'center',
                showConfirmButton: false,
                timer: 1500
            });
        },

        localSt()
        {
            if (localStorage.getItem('regs'))
            {
                try
                {
                    this.registros_fail = JSON.parse(localStorage.getItem('regs'));
                }
                catch (e)
                {
                    localStorage.removeItem('regs');
                }
            }
        },

        addC()
        {
            // obtener ubicacion
            let aux_ubicacion = "";
            if (this.tipo_ubicacion == 0) aux_ubicacion = this.ubicacion
            else
            {
                aux_ubicacion = this.tipo_ubicacion == 1 ? "TEHUACAN" : "COATZACOALCOS";
            }
            let emp = this.empleado + '|' + this.id_empleado + '|' + this.fecha_reg + '|' + this.hora_reg + "|" + aux_ubicacion;
            this.registros_fail.push(emp);
            this.saveC();
        },

        removeC(x)
        {
            this.registros_fail.splice(x, 1);
            this.saveC();
        },

        saveC()
        {
            let parsed = JSON.stringify(this.registros_fail);
            localStorage.setItem('regs', parsed);
        },

        sincronizar()
        {
            if (navigator.onLine)
            {
                this.registros_fail.forEach((item, i) =>
                {
                    var porciones = item.split('|');
                    axios.post("rh/checador/guardar",
                    {
                        empleado: porciones[0],
                        empleado_id: porciones[1],
                        fecha: porciones[2],
                        hora: porciones[3],
                        ubicacion: porciones[4],
                    }).then(res =>
                    {
                        if (res.data.status)
                        {
                            console.log("ok");
                            this.removeC(i);
                        }
                        else
                        {
                            // Nada
                        }
                    }).catch(e =>
                    {
                        console.error(e);
                    });
                });
            }
            else
            {
                toastr.warning('NO SE PUEDE SINCRONIZAR REVISE SU CONEXION A INTERNET!!!');
            }
        }

    },
    mounted()
    {
        this.localSt();
        this.InitCrud();
        this.tipo_ubicacion = 1;
    }
}
</script>

<style>
#clock {
    font-family: 'Share Tech Mono', monospace;
    color: #000000;
    text-align: center;
    transform: translate(-50%, -50%);
    color: #000000;
}

.time {
    letter-spacing: 0.05em;
    font-size: 150px;
    padding: 5px 0;
}

.date {
    letter-spacing: 0.1em;
    font-size: 150px;
}
</style>
