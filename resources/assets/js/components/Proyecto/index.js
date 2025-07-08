// Proyectos
const Proyecto = r => require.ensure([], () => r(require('../Proyecto/Proyecto.vue')), 'proyecto')

// Requis
const PrincipalRequisiciones = r => require.ensure([], () => r(require('./Requisiciones/PrincipalRequisiciones.vue')), 'proyecto')

// Notificaciones
const Dashboard = r => require.ensure([], () => r(require('./Dashboard/Dashboard.vue')), 'proyecto')

const AprobarRequisiciones = r => require.ensure([], () => r(require('./Requisiciones/AprobarRequisiciones.vue')), 'proyecto')

const routes = [
  // Proyectos
    { path: '/proyecto', component: Proyecto },
    // Requisiciones
    { path: '/proyecto/requisicion', component: PrincipalRequisiciones },
    // Notificaciones
    { path: '/proyecto/dashboard/', component: Dashboard },
    // Requisiciones
    { path: '/proyectos/requisiciones', component: AprobarRequisiciones },
    {
      path: "/proyectos/requisiciones/:id/detalles",
      name: "proyectos-requisiciones-detalles",
      props: true,
      component: () => import(/* webpackChunkName: "Requisiciones-Detalles" */"./Requisiciones/Detalles.vue"),
    },
  ]

export default routes
