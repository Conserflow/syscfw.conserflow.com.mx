const Solicitud = r => require.ensure([],() => r(require("./Solicitud/Solicitud.vue")),"via")

const routes = [
  {path: "/via/solicitud/",component: Solicitud},
]

export default routes
