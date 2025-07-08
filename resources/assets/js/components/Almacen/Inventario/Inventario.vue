<template>
<div class="main">
    <!-- Card Inicio-->
    <div class="card">
        <!-- Inicio card-->
        <div class="card-header">
            <i class="fa fa-align-justify"></i> Inventario de Almacén
        </div>
        <div class="card-body">
            <nav>
                <div class="nav nav-tabs" id="nav-tab" role="tablist">
                    <a class="nav-link active" id="nav-home-tab" data-toggle="tab" href="#nav-home" role="tab" aria-controls="nav-home" aria-selected="true">
                        Registrar <i class="fas fa-plus"></i>
                    </a>
                    <a class="nav-link" id="nav-profile-tab" data-toggle="tab" href="#nav-profile" role="tab" aria-controls="nav-profile" aria-selected="false">
                        Agrupar <i class="fas fa-hands ml-1"></i>
                    </a>
                </div>
            </nav>
            <div class="tab-content" id="nav-tabContent">
                <div class="tab-pane fade show active" id="nav-home" role="tabpanel" aria-labelledby="nav-home-tab">
                    <registar name="registrar"></registar>
                </div>
                <div class="tab-pane fade" id="nav-profile" role="tabpanel" aria-labelledby="nav-profile-tab">
                    <agrupar name="agrupar"></agrupar>
                </div>
            </div>
        </div>
    </div>

</div>

<!-- Main -->
</template>

<script>
const Registrar = r => require.ensure([], () => r(require('./RegistrarInventario.vue')),"alm");
const Agrupar = r => require.ensure([], () => r(require('./Agruparnventario.vue')),"alm");
export default
{
    components:
    {
        "registar": Registrar,
        "agrupar": Agrupar
    }
}
</script>
