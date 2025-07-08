const DashBoard = r => require.ensure([],()=> r(require('./Dashboard/Dashborad.vue')), 'calidad')
// Nuevos equipos de calibracion
const EquiposCalib2 = r => require.ensure([],()=> r(require('./Calibracion/EquiposCalibracion2.vue')), 'calidad')

const rutas = [
	{ path: '/calidad/dashboard', component: DashBoard },
	{ path: '/calidad/calib/equipos2', component: EquiposCalib2 },
]

export default rutas
