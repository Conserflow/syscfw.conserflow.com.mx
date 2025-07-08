import Vue from "vue";
import Router from "vue-router";

import Almacen from "../components/Almacen";
import Aplicacion from "../components/Aplicacion";
import Calidad from "../components/Calidad";
import Compras from "../components/Compras";
import Contabilidad from "../components/Contabilidad";
import Costos from "../components/Costos";
import Enfermeria from "../components/Enfermeria";
import Proyecto from "../components/Proyecto";
import RH from "../components/RH";
import Salidas from "../components/Salidas";
import Seguridad from "../components/Seguridad";
import SGI from "../components/SGI";
import Sistema from "../components/Sistema";
import TI from "../components/TI";
import Trafico from "../components/Trafico";
import Ventas from "../components/Ventas";
import Viaticos from "../components/Viaticos";

Vue.use(Router);

var allRoutes = [];

allRoutes = allRoutes.concat(
  Almacen,
  RH,
  Proyecto,
  Sistema,
  Aplicacion,
  Compras,
  Trafico,
  TI,
  Contabilidad,
  Ventas,
  Costos,
  Viaticos,
  Calidad,
  Seguridad,
  SGI,
  Enfermeria,
  Salidas,
);

const routes = allRoutes;
export default new Router({
  routes,
  linkExactActiveClass: "active",
});
