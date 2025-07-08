const Usuario = r => require.ensure([], () => r(require('../Sistema/Usuario.vue')), 'sistema')
const Errors = r => require.ensure([], () => r(require('../Sistema/Errors.vue')), 'sistema')
const PermisosUsuarios = r => require.ensure([],()=> r(require('../Sistema/PermisosUsuarios.vue')),'sistema')
const ElementosMenu = r => require.ensure([], () => r(require('../Sistema/ElementosMenu.vue')), 'sistema')
const PermisosCrud = r => require.ensure([], () => r(require('../Sistema/PermisosCrud.vue')), 'sistema')

const routes = [
    { path: '/usuario', component: Usuario},
    { path: '/errors', component: Errors},
    { path: '/permisousuario', component: PermisosUsuarios},
    { path: '/elementosmenu', component: ElementosMenu},
    { path: '/permisocrud/', component: PermisosCrud},
  ]

export default routes
