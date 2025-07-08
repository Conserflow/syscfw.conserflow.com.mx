import { TIPOS } from "../../utils/requisiciones";

const Compras = r => require.ensure([],() => r(require('./Compra/ProyectosCompras.vue')),'compras')
const RegistroProveedores = r => require.ensure([],() => r(require('./Proveedores/Proveedor.vue')),'compras')
const Evaluacion = r => require.ensure([],() => r(require('./Proveedores/Evaluacion.vue')),'compras')
const EstadoCompra = r => require.ensure([],() => r(require('./Catalogos/EstadoCompras.vue')),'compras')
const Servicios = r => require.ensure([],() => r(require('./Catalogos/Servicios.vue')),'compras')
const Dashboard = r => require.ensure([],() => r(require('./Dashboard/Dashboard.vue')),'compras')
const MttoVehicular = r => require.ensure([],() => r(require('./Catalogos/MttoVehicular.vue')),'compras')
const GeneralCompras = r => require.ensure([],() => r(require('./Reportes/GeneralCompras.vue')),'compras')

const routes = [

  {path: '/comp/proveedor/proveedores/',component: RegistroProveedores},
  {path: '/comp/compra/compras/',component: Compras},
  {path: '/comp/catalogo/estadocompras/',component: EstadoCompra},
  {path: '/comp/catalogo/catservicios/',component: Servicios},
  {path: '/comp/dashboard/',component: Dashboard},
  {path: '/compras/evaluacion/proveedores/',component: Evaluacion},
  {path: '/comp/reportes/general/compras/',component: GeneralCompras},
  {path: '/comp/catalogos/mttovehicular',component: MttoVehicular},
  {
    path: "/comp/requisiciones",
    name: "compras-requisiciones",
    component: () => import(/* webpackChunkName: "Requisiciones-Index" */"./Requisiciones/Index.vue"),
  },
  {
    path: "/comp/requisiciones/:id/material",
    name: TIPOS.MATERIAL_PRINCIPAL.clave,
    props: true,
    component: () => import(/* webpackChunkName: "Requisiciones-Index" */"./Requisiciones/Material.vue"),
  },
]

export default routes
