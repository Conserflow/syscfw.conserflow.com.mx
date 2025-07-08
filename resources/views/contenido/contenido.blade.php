@extends('principal')
@section('contenido')
    <router-view></router-view>

    <!--Inicio del modal modulos-->
    <div class="modal fade" tabindex="-1" :class="{ 'mostrar': showModal }" role="dialog" aria-labelledby="myModalLabel"
        style="display: none;" aria-hidden="true">
        <div class="modal-dialog  modal-lg" role="document">
            <div class="modal-modulos modal-content rounded">
                <div class="modal-header">
                    <p class="modulos-title modal-title col-10 text-white font-weight-bold text-center">
                        Módulos Del Sistema
                    </p>
                    <button type="button" class="close" @click="cerrarModalModulos()" aria-label="Close">
                        <span class="text-white" aria-hidden="true">x</span>
                    </button>
                </div>
                <div class="modal-body">
                    <modulos @click="cargarMenuModulo"></modulos>
                </div>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>
    <!--Fin del modal-->
@endsection
