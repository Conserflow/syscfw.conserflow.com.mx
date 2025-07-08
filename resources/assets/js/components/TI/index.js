// Inventario
const Computo = r => require.ensure([], () => r(require('./Inventario/Equipos.vue')), 'ti')
const Impresion = r => require.ensure([], () => r(require('./Inventario/Impresion.vue')), 'ti')
const Accesorios = r => require.ensure([], () => r(require('./Inventario/Accesorios.vue')), 'ti')
const Red = r => require.ensure([], () => r(require('./Inventario/Red.vue')), 'ti')
const Video = r => require.ensure([], () => r(require('./Inventario/Video.vue')), 'ti')

// Vales
const Vales = r => require.ensure([], () => r(require('./Vales/Vale.vue')), 'ti')
const Bitacora  = r => require.ensure([], () => r(require('./Bitacora/Bitacora.vue')), 'ti')

// Mtto
const Programa  = r => require.ensure([], () => r(require('./Mantenimientos/Programa.vue')), 'ti')
const ReportePreventivo  = r => require.ensure([], () => r(require('./Mantenimientos/ReportePreventivo.vue')), 'ti')
const Historico  = r => require.ensure([], () => r(require('./Servicios/Historico.vue')), 'ti')
const Impresoras  = r => require.ensure([], () => r(require('./Mantenimientos/Impresoras.vue')), 'ti')

// Matriz por puesto
const MatrizPuesto  = r => require.ensure([], () => r(require('./MatrizPorPuesto.vue')), 'ti')

const PropuestaEquipo= r => require.ensure([], () => r(require('./PropuestaEquipo.vue')), 'ti')
const routes = [
    { path: '/ti/computo', component: Computo },
    { path: '/ti/impresion', component: Impresion },
    { path: '/ti/accesorios', component: Accesorios },
    { path: '/ti/red', component: Red },
    { path: '/ti/video', component: Video },
    { path: '/ti/vales', component: Vales },
    { path: '/ti/bitacora/resguardo/info', component: Bitacora },
    { path: '/ti/mantenimiento/programaanual', component: Programa },
    { path: '/ti/mantenimiento/impresoras', component: Impresoras },
    { path: '/ti/mantenimiento/reportepreventivo', component: ReportePreventivo },
    { path: '/ti/historico/servicios', component: Historico },
    { path: '/ti/equipopuesto/', component: MatrizPuesto },
    { path: '/ti/propuestaequipo/', component: PropuestaEquipo },
  ]

export default routes
