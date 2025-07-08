const SatCatFormapago = r => require.ensure([], () => r(require('../Contabilidad/Catalogo/SatCatFormapago.vue')), 'conta')
const SatCatProdser = r => require.ensure([], () => r(require('../Contabilidad/Catalogo/SatCatProdser.vue')), 'conta')
const Unidades = r => require.ensure([], () => r(require('./Catalogo/SatCatUnidades')), 'conta')
const Factura = r=> require.ensure([], () => r(require('./Factura/Factura.vue')), 'conta')
const FacturaV4 = r=> require.ensure([], () => r(require('./Factura/Facturav4.vue')), 'conta')

const routes = [
  { path: '/conta/satcatformapago', component: SatCatFormapago},
  { path: '/conta/satcatprodser', component: SatCatProdser},
  { path: '/conta/factura/', component: Factura},
  { path: '/conta/facturav4/', component: FacturaV4},
  { path: '/conta/catalogo/unidades', component:Unidades},
  
  ]

export default routes
