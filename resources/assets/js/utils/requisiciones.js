export const TIPOS = {
    MATERIAL_PRINCIPAL: {id: 1,clave: "requisiciones-materiales"},
    MATERIAL_MENOR: {id: 2,clave: "requisiciones-materiales"},
    SERVICIOS: {id: 3,clave: "requisiciones-servicios"},
    REEMBOLSOS: {id: 4,clave: "requisiciones-reembolso"},
    IMPORTACIONES: {id: 5,clave: "requisiciones-importaciones"},
    FLETES: {id: 6,clave: "requisiciones-fletes"},
    COMPRA_SITIO: {id: 7,clave: "requisiciones-sitio"},
    COMBUSTIBLES: {id: 8,clave: "requisiciones-combustible"},
    HOSPEDAJE: {id: 9,clave: "requisiciones-hospedaje"}
};

const RUTAS = {
    1: "requisiciones-materiales",
    2: "requisiciones-materiales",
    3: "requisiciones-servicios",
    4: "requisiciones-reembolso",
    5: "requisiciones-importaciones",
    6: "requisiciones-fletes",
    7: "requisiciones-sitio",
    8: "requisiciones-combustible",
    9: "requisiciones-hospedaje"
};

export const obtenerRuta = (tipo) =>
{
    return RUTAS[tipo] || "/dashboard";
};
