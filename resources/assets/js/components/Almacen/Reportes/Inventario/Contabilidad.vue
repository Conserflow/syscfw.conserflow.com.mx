<template>
<main class="main">
    <div class="container-fluid">
        <div class="card">
            <div class="card-header">
                <i class="fa fa-align-justify"></i> Reportes - Inventario
            </div>
            <div class="card-body">
                <div class="card">
                    <div class="card-header bg-dark text-white">
                        Salidas
                    </div>
                    <div class="card-body">
                        <div class="form-group row">
                            <label class="col-md-1 form-control-label mt-1">Mes</label>
                            <div class="col-md-2 mb-3">
                                <select class="form-control" v-model="inventario_mes">
                                    <option value="1">Enero</option>
                                    <option value="2">Febrero</option>
                                    <option value="3">Marzo</option>
                                    <option value="4">Abril</option>
                                    <option value="5">Mayo</option>
                                    <option value="6">Junio</option>
                                    <option value="7">Julio</option>
                                    <option value="8">Agosto</option>
                                    <option value="9">Septiembre</option>
                                    <option value="10">Octubre</option>
                                    <option value="11">Noviembre</option>
                                    <option value="12">Diciembre</option>
                                </select>
                            </div>
                            <label class="col-md-1 form-control-label mt-1">Año</label>
                            <div class="col-md-2 mb-3">
                                <select class="form-control" v-model="inventario_anio">
                                    <option value="2022">2022</option>
                                    <option value="2023">2023</option>
                                    <option value="2024">2024</option>
                                </select>
                            </div>
                            <div class="col-md-2 mb-3">
                                <button class="btn btn-success" @click="descargarSalidas"> Descargar
                                    <i class="fas fa-file-excel"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header bg-dark text-white">
                        Existencia por mes
                    </div>
                    <div class="card-body">
                        <div class="form-group row">
                            <label class="col-md-1 form-control-label mt-1">Mes</label>
                            <div class="col-md-2 mb-3">
                                <select class="form-control" v-model="existencia_mes">
                                    <option value="1">Enero</option>
                                    <option value="2">Febrero</option>
                                    <option value="3">Marzo</option>
                                    <option value="4">Abril</option>
                                    <option value="5">Mayo</option>
                                    <option value="6">Junio</option>
                                    <option value="7">Julio</option>
                                    <option value="8">Agosto</option>
                                    <option value="9">Septiembre</option>
                                    <option value="10">Octubre</option>
                                    <option value="11">Noviembre</option>
                                    <option value="12">Diciembre</option>
                                </select>
                            </div>
                            <label class="col-md-1 form-control-label mt-1">Año</label>
                            <div class="col-md-2 mb-3">
                                <select class="form-control" v-model="existencia_anio">
                                    <option value="2022">2022</option>
                                    <option value="2023">2023</option>
                                </select>
                            </div>
                            <div class="col-md-2 mb-3">
                                <button class="btn btn-success" @click="descargarExistencias"> Descargar
                                    <i class="fas fa-file-excel"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
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
            inventario_mes: 1,
            inventario_anio: 2023,
            existencia_mes: 1,
            existencia_anio: 2023,
        }
    },
    methods:
    {
        /**
         * Descargar las salidas de almacén en la fecha seleccionada
         */
        descargarSalidas()
        {
            window.open(`alamcen/reportes/inventariocontabilidad/${this.inventario_anio}/${this.inventario_mes}`);
        },

        /**
         * Descargar las existencia de almacén en la fecha seleccionada
         */
        descargarExistencias()
        {
            window.open(`alamcen/reportes/existenciascontabilidad/${this.existencia_anio}/${this.existencia_mes}`);
        },

    },
    mounted()
    {
    }
}
</script>
