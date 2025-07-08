const Clientes = r=> require.ensure([], () => r(require('./Clientes/Clientes.vue')), 'venta')

const routes = [
    { path: '/venta/clientes/', component: Clientes},
  ]

export default routes
