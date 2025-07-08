const DatosEmpleados = r => require.ensure([], () => r(require('./DatosEmpleados.vue')), 'enf');

// Catalagos
const MotivosAtencion = r => require.ensure([], () => r(require('./Catalogos/MotivosAtencion.vue')), 'enf');
// Atención medica
const AtencionMedica = r => require.ensure([], () => r(require('./AtencionMedica/AtencionMedica.vue')), 'enf');
const AtencionMedicaReportes = r => require.ensure([], () => r(require('./AtencionMedica/Reportes.vue')), 'enf');

// Covid
const RegistroCovid = r => require.ensure([], () => r(require('./Covid/Registro.vue')), 'enf');

// Incapacidad
const Incapacidad = r => require.ensure([], () => r(require('./Incapacidad.vue')), 'enf');

const routes = [
   { path: '/enfemeria/datosempleados' , component: DatosEmpleados},
   { path: '/enfemeria/catalogos/motivoatencion' , component: MotivosAtencion},
   { path: '/enfemeria/atencionmedica' , component: AtencionMedica},
   { path: '/enfemeria/atencionmedica/reportes' , component: AtencionMedicaReportes},
   { path: '/enfemeria/registrocovid' , component: RegistroCovid},
   { path: '/enfemeria/incapacidad' , component: Incapacidad},
  ]

export default routes
