export default {
    resetObject(obj) {
        for(let key in obj) {
            if (typeof obj[key] === 'string')
                obj[key] = '';
            else if (typeof obj[key] === 'number')
                obj[key] = 0;
            else if(typeof obj[key] === undefined)
                obj[key] = null;
            else if(typeof obj[key] === 'object')
                obj[key] = {};
        }
    },
      
    getCRUD(ruta){
        let formData = new FormData();
        formData.append('ruta',ruta);
        formData.append('identificador',1);
        var permisos =
        {
            Create:false,
            Read:false,
            Update:false,
            Delete:false,
            Download:false,
            Upload:false,
        };
        axios.post('/CRUD',formData).then(response => {
            for (var i = 0; i < response.data.length; i++) {
                if (response.data[i].control_id == 1) {
                    permisos.Create =true
                }
                if (response.data[i].control_id == 2) {
                    permisos.Read =true
                }
                if (response.data[i].control_id == 3) {
                    permisos.Update =true
                }
                if (response.data[i].control_id == 4) {
                    permisos.Delete =true
                }
                if (response.data[i].control_id == 5) {
                    permisos.Download =true
                }
                if (response.data[i].control_id == 6) {
                    permisos.Upload =true
                }

            }
        })
        .catch(function (error) {
            console.log(error);
        });
        return permisos;
    },
}
