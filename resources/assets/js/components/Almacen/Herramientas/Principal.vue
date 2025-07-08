<template>
<div class="main">
    <div class="card" style="min-height:80vh">
        <div class="card-header">
            <i class="fa fa-align-justify"></i> Herramientas de Almacén
        </div>
        <div class="card-body">
            <nav>
                <div class="nav nav-tabs" id="nav-tab" role="tablist">
                    <a class="nav-link active" id="nav-home-tab" data-toggle="tab" href="#nav-home" role="tab" aria-controls="nav-home" aria-selected="true" @click="tipoAccion=1">
                        Asociar <i class="fas fa-link"></i>
                    </a>
                    <a class="nav-link" id="nav-profile-tab" data-toggle="tab" href="#nav-profile" role="tab" aria-controls="nav-profile" aria-selected="false" @click="tipoAccion=2">
                        Catálogo <i class="fas fa-list ml-1"></i>
                    </a>
                </div>
            </nav>
            <div class="tab-content" id="nav-tabContent">
                <div v-show="tipoAccion==1" class="tab-pane fade show active" id="nav-home" role="tabpanel" aria-labelledby="nav-home-tab">
                    <asociar />
                </div>
                <div v-show="tipoAccion==2" class="tab-pane fade" id="nav-profile" role="tabpanel" aria-labelledby="nav-profile-tab">
                    <catalogo />
                </div>
            </div>
        </div>
    </div>
</div>
</template>

<script>
import Catalogo from "./Catalogo.vue";
import Asociar from "./Asociar.vue";
export default
{
    components:
    {
        "catalogo": Catalogo,
        "asociar": Asociar,
    },
    data()
    {
        return {
            tipoAccion: 1,
        }
    }
}
</script>
