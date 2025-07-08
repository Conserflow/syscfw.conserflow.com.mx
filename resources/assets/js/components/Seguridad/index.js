
const Lista = r => require.ensure([], () => r(require('./ListaAsistencia.vue')), 'seg')
const Analisis = r => require.ensure([], () => r(require('./AnalisisSeguridad.vue')), 'seg')
const Permiso = r => require.ensure([], () => r(require('./PermisoSeguridad.vue')), 'seg')
const Catalogo = r => require.ensure([], () => r(require('./CatalogoAnalisis.vue')), 'seg')
const Bitacora = r => require.ensure([], () => r(require('./BitacoraResiduos.vue')), 'seg')
const Residuos = r => require.ensure([], () => r(require('./Residuos.vue')), 'seg')
const Vale = r => require.ensure([], () => r(require('./Vale.vue')), 'seg')
const CatalogosSecuencia = r => require.ensure([], () => r(require('./Secuencias.vue')), 'seg')
const CatalogosAnalisis = r => require.ensure([], () => r(require('./CatalogoAnalisis2.vue')), 'seg')
const NuevoAnalisisSeguridad = r => require.ensure([], () => r(require('./NuevoAnalisisSeguridad.vue')), 'seg')

// Catalogos
const FoliosProyectos = r => require.ensure([], () => r(require('./Catalogos/Folios.vue')), 'seg')

const Platicas = r => require.ensure([], () => r(require('./Platicas')), 'seg')
const Alcoholimetria = r => require.ensure([], () => r(require('./Alcoholimetria')), 'seg')
const InspeccionEpp = r => require.ensure([], () => r(require('./InspeccionEpp.vue')), 'seg')
const InspeccionBotiquines = r => require.ensure([], () => r(require('./Botiquines.vue')), 'seg')

const routes = [

    { path: '/seguridad/lista/asistencia/', component: Lista },
    { path: '/seguridad/analisis/seguridad/', component: Analisis },
    { path: '/seguridad/permiso/seguridad/', component: Permiso },
    { path: '/seguridad/catalogo/analisis/', component: Catalogo },
    { path: '/seguridad/bitacora/residuos/', component: Bitacora },
    { path: '/seguridad/residuos/', component: Residuos },
    { path: '/seguridad/vale/epp', component: Vale },
    { path: '/seguridad/catalogo/analisis2/', component: CatalogosAnalisis },
    { path: '/catalogos/secuencias', component: CatalogosSecuencia },
    { path: '/seguridad/nuevoanalisis', component: NuevoAnalisisSeguridad },
    { path: '/seguridad/platicas', component: Platicas },
    { path: '/seguridad/alcoholimetria', component: Alcoholimetria },
    { path: '/seguridad/inspeccionepp', component: InspeccionEpp },
    { path: '/seguridad/botiquin', component: InspeccionBotiquines },
  
    // Catalogos
    { path: '/seguridad/catalogos/folios', component: FoliosProyectos },


  ]

export default routes
