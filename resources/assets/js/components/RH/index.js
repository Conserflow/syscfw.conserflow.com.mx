// Empleados
const Empleados = r => require.ensure([], () => r(require('./Empleados/Empleados.vue')), 'rh')
const Cumples = r => require.ensure([], () => r(require('./Empleados/Cumpleanios.vue')), 'rh')
const Vacaciones = r => require.ensure([], () => r(require('./Empleados/Vacaciones.vue')), 'rh')
const DatosBancarios = r => require.ensure([], () => r(require('./Empleados/DatosBancarios.vue')), 'rh')

// Asistencia
const Registro = r => require.ensure([], () => r(require('./Asistencias/Asistencia.vue')), 'rh')

// Catalogos
const Puesto = r => require.ensure([], () => r(require('./Catalogos/Puesto.vue')), 'rh')
const Bancos = r => require.ensure([], () => r(require('./Catalogos/Bancos.vue')), 'rh')
const Departamentos = r => require.ensure([], () => r(require('./Catalogos/Departamentos.vue')), 'rh')

const Dashboard = r => require.ensure([], () => r(require('./Dashboard/Dashboard.vue')), 'rh')

// Checador
const ChecadorQR = r => require.ensure([], () => r (require('./ChecadorQR.vue')), 'rh')

// Infraestructura
const CuestionarioInfraestructura = r => require.ensure([], () => r (require('./CuestionarioInfraestructura.vue')), 'rh')

// Factores de riesgo
const FactoresRiesgo = r => require.ensure([], () => r (require('./FactoresRiesgo/FactoresRiesgo.vue')), 'rh')

// Días festivos
const DiasFestivos = r => require.ensure([], () => r (require('./DiasFestivos.vue')), 'rh')

const routes = [
    // Empleados
    { path: '/rh/empleados/alta', component: Empleados },
    { path: '/rh/empleados/cumpleaños/', component: Cumples },
    { path: '/rh/empleados/vacaciones', component: Vacaciones },
    { path: '/rh/empleados/datosbancarios', component: DatosBancarios },
    
    // Asistencia
    { path: '/rh/asistencias/asistencia', component: Registro },

    // Catalogos
    { path: '/rh/catalogos/bancos', component: Bancos },
    { path: '/rh/catalogos/puestos', component: Puesto },
    { path: '/rh/catalogos/departamentos', component: Departamentos },

    // Notificaciones
    { path: '/rh/dashboard', component: Dashboard },

    // Checador
    { path: '/rh/checador', component: ChecadorQR},
    // Infraestructura    
    { path: '/rh/cuestionario/infraestructura', component: CuestionarioInfraestructura},

    // Factor riesgo
    { path: '/rh/capacitacion/factoresriesgo', component: FactoresRiesgo},

    // Días festivos
    { path: '/rh/diasfestivos', component: DiasFestivos},

]


export default routes
