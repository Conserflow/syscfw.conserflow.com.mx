// Salidas no conformes
const NoConformes = r => require.ensure([],() => r(require('./NoConformes.vue')),'sal')

const routes = [

    {path: '/salidas/noconformes/',component: NoConformes},
]

export default routes
