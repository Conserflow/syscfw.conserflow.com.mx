const Unidades = r => require.ensure([], () => r(require('./Unidades/Unidades.vue')), 'trafico')

const Conductores = r => require.ensure([], () => r(require('./Conductores.vue')), 'trafico')

const MantenimientoAnual = r => require.ensure([], () => r (require('./Servicios/MantenimientoAnual.vue')), 'trafico')
const TipoServicio = r => require.ensure([], () => r(require('./TipoServicio.vue')), 'trafico')

const Combustible = r => require.ensure([], () => r (require('./Reportes/Combustibles.vue')), 'trafico')

const Asignacion = r => require.ensure([], () => r (require('./AsignacionVehiculo.vue')), 'trafico')
const Vehiculos = r => require.ensure([], () => r (require('./Vehiculos.vue')), 'trafico')
const EntregaRecepcion = r => require.ensure([], () => r (require('./EntregaRecepcion.vue')), 'trafico')
const Solicitud = r => require.ensure([], () => r (require('./Solicitud/Solicitud.vue')), 'trafico')
const Mantenimiento = r => require.ensure([], () => r (require('./Mantenimiento/Mantenimiento.vue')), 'trafico')
const ResguardoUnidad = r => require.ensure([], () => r (require('./ValeResguardo.vue')), 'trafico')
const Proveedores = r => require.ensure([], () => r (require('./ProveedoresVehiculos.vue')), 'trafico')

const routes = [
    { path: '/unidades', component: Unidades},
    { path: '/conductores', component: Conductores},
    { path: '/trafico/tiposervicio', component: TipoServicio},
    { path: '/trafico/asignacion/', component: Asignacion },
    { path: '/trafico/vehiculos/', component: Vehiculos },
    { path: '/trafico/vehiculos/entrega/recepcion', component: EntregaRecepcion },
    { path: '/trafico/registro/combustibles', component: Combustible },
    { path: '/trafico/solicitud', component: Solicitud },
    { path: '/trafico/mantenimientoanual', component: MantenimientoAnual },
    { path: '/trafico/mantenimiento', component: Mantenimiento },
    { path: '/trafico/valeresguardo', component: ResguardoUnidad },
    { path: '/trafico/proveedores', component:  Proveedores}
  ]

export default routes
