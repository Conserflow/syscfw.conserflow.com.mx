import Swal from "sweetalert2";

/**
 * Mostrar mensaje de error
 * @param string  mensaje Mensaje a mostrar 
 */
export const error = (mensaje) =>
{
    toastr.error(mensaje);
}

/**
 * Mostrar mensaje de error
 * @param string  mensaje Mensaje a mostrar 
 */
export const success = (mensaje) =>
{
    toastr.success(mensaje);
}

/**
 * Mostrar mensaje de pregunta
 * @param {string} title Title
 * @param {String} text Text
 * @param {String} icon Icon
 */
export const question = (title,text,icon = "question") =>
{
    return Swal.fire(
        {
            title,
            text,
            icon,
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Aceptar",
            cancelButtonText: "Cancelar"
        });
}

/**
 * Solicitar un texto
  * @param {string} title Title
 */
export const getText = (title) =>
{
    return Swal.fire({
        title,
        input: "text",
        icon: "info",
        inputAttributes: {
            autocapitalize: "off"
        },
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        showCancelButton: true,
        confirmButtonText: "Aceptar",
        cancelButtonText: "Cancelar",
    });
}

/**
 * Realiza una peticion GET a la URL ingresada
 * @param {String} url URL a consultar
 * @param {String} successMessage Mensaje de correcto
 * @param {Object} loading Objeto de loading
 * @param {Function} callback Acciones en caso de correcto
 */
export const get = async (url,successMessage,loading,callback) =>
{
    try
    {
        if ((typeof loading === "object") && loading != null)
            loading.enable = true;
        let res = await axios.get(url);
        if ((typeof loading === "object") && loading != null)
            loading.enable = false;
        if (res.data.status || res.data.success)
        {
            if (typeof callback == "function")
                callback(res);
            if (successMessage != null && successMessage != "")
                success(successMessage);
        }
        else
        {
            error(res.data.message);
        }
    } catch (ex)
    {
        console.error(ex);
        error(`URL: ${ url }\n` + ex);
    }
}

/**
 * Realiza una peticion POST o PUT a la URL ingresada
 * @param {String} url URL a consultar
 * @param {Object} data Objecto con data a guardar
 * @param {Number} id Id del objeto (En caso de actualización)
 * @param {String} successMessage Mensaje de correcto
 * @param {Object} loading Objeto de loading
 * @param {Function} callback Acciones en caso de correcto
 */
export const postPut = async (url,data,id,successMessage,loading,callback) =>
{
    if (loading && typeof loading === "object")
        loading.enable = true;
    let method = (id == null || id == 0) ? "POST" : "PUT";
    url += method == "POST" ? "" : `/${ id }`;
    let res = await axios({
        method,
        url,
        data
    });
    if (callback == null) return res;
    if (loading && typeof loading === "object")
        loading.enable = false;
    if (res.data.status || res.data.success)
    {
        if (typeof callback == "function")
        {
            callback(res);
        }
        if (successMessage != null && successMessage != "")
            success(successMessage);
    }
    else
    {
        error(res.data.message);
    }
}

/**
 * Realiza una peticion DEL a la URL ingresada
 * @param {String} url URL a consultar
 * @param {Number} id Id del objeto
 * @param {String} successMessage Mensaje de correcto
 * @param {Object} loading Objeto de loading
 * @param {Function} callback Acciones en caso de correcto
 */
export const del = async (url,id,successMessage,loading,callback) =>
{
    if (typeof loading === "object")
        loading.enable = true;
    url += `/${ id }`
    let res = await axios.delete(url);
    if (typeof loading === "object")
        loading.enable = false;
    if (res.data.status || res.data.success)
    {
        if (typeof callback === "function")
        {
            callback(res);
        }
        if (successMessage != null && successMessage != "")
            success(successMessage);
    }
    else
    {
        error(res.data.message);
    }
}

/**
 * Comprueba que los models de los select ingresados sean válidos
 * @param selects Modelos a validar 
 */
export const validateSelect = (selects) =>
{
    const aux_validation = (obj) =>
    {
        if (obj == null || obj.id == null)
        {
            Swal.fire(
                {
                    title: "Error",
                    text: "Ingrese todos los campos",
                    icon: "error",
                    confirmButtonColor: "#3085d6",
                    confirmButtonText: "Aceptar",
                });
            return false;
        }
        return true;
    }
    if (selects.length)
    {
        let isValid = true;
        for (const s of selects)
        {
            const res = aux_validation(s);
            if (!res) isValid = false;
        }
        return isValid;
    }
    else
    {
        return aux_validation(selects);
    }
}

/**
 * Mostrar ventana de informacion
 * @param {string} title Titulo
 * @param {string} text Mensaje
 */
export const info = (title,text) =>
{
    Swal.fire(
        {
            title,
            text,
            icon: "info",
            confirmButtonColor: "#3085d6",
            confirmButtonText: "Aceptar",
        });
}