// Filosofia Conser
const Filosofia = r => require.ensure([], () => r(require('./Filosofia/Filosofia.vue')), 'sgi');
const Contexto = r => require.ensure([], () => r(require('./Contexto.vue')), 'sgi');
const Procedimientos = r => require.ensure([], () => r(require('./Procedimientos/Procedimientos.vue')), 'sgi');
const Seguridad = r => require.ensure([], () => r(require('./Seguridad.vue')), 'sgi');
const Certificaciones = r => require.ensure([], () => r(require('./Certificaciones.vue')), 'sgi');
const Normativas = r => require.ensure([], () => r(require('./Normativas.vue')), 'sgi');
const Politicas = r => require.ensure([], () => r(require('./Politicas/Politicas.vue')), 'sgi');
const ApoyosVisuales = r => require.ensure([], () => r(require('./ApoyosVisuales.vue')), 'sgi');

const routes = [
   { path: "/sgi/filosofia" , component: Filosofia},
   { path: "/sgi/contexto" , component:Contexto},
   { path: "/sgi/procedimientos" , component:Procedimientos },
   { path: "/sgi/ssma" , component:Seguridad },
   { path: "/sgi/certificaciones" , component:Certificaciones },
   { path: "/sgi/normativas" , component:Normativas },
   { path: "/sgi/politicas" , component:Politicas },
   { path: "/sgi/apoyosvisuales" , component:ApoyosVisuales },
  ];

export default routes
