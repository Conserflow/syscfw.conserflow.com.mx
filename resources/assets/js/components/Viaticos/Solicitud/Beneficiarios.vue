<template>
  <div>
    <template v-if="tipo_viatico != 0">
    <div class="form-row">
      <div class="form-group col-md-6">
        <label>&nbsp;Beneficiario</label>
        <v-select :options="optionsvs_benficiario_uno" v-model="solicitud.beneficiariosuno" label="name" name="proyecto" data-vv-name="proyecto" @input="buscar_uno" ></v-select>
      </div>
      <div class="form-group col-md-6">
        <label>&nbsp;Banco beneficiario</label>
        <select class="form-control" name="datos bancarios" v-model="solicitud.datos_bancos_beneficiariosuno"  @change="enviar_uno($event)" data-vv-name="Proyecto" >
          <option v-for="item in listaDatosBancariosuno" :value="item.id" :key="item.id">{{ item.bnombre }}</option>
          <option value="0">OTRO</option>
        </select>
        <span class="text-danger">{{ errors.first('datos bancarios') }}</span>
      </div>
    </div>
    </template>
    <template v-if="tipo_viatico == 0">
      <div class="form-row">
        <div class="form-group col-md-6">
          <label>&nbsp;Beneficiario</label>
          <input type="text" class="form-control" v-model="solicitud.beneficiariosuno">
        </div>
        <div class="form-group col-md-6">
          <label>&nbsp;Banco</label>
          <input type="text" class="form-control" v-model="solicitud.datos_bancos_beneficiariosuno">
        </div>
      </div>
    </template>
    <!-- {{solicitud}} -->
    <div class="form-row">
      <div class="form-group col-md-6 ">
        <label>CUENTA</label>
        <input type="text" v-model="solicitud.cuentauno" class="form-control" >
      </div>
      <div class="form-group col-md-6 ">
        <label>CLABE</label>
        <input type="text" v-model="solicitud.claveuno" class="form-control" >
      </div>
    </div>
    <div class="form-row">
      <div class="form-group col-md-6 ">
        <label>TARJETA</label>
        <input type="text" v-model="solicitud.clavecuentatarjetauno" class="form-control" >
      </div>
      <template v-if="solicitud.datos_bancos_beneficiariosuno === '0' && tipo_viatico > 0">
        <div class="form-group col-md-6 ">
          <label>BANCO</label>
          <input type="text" v-model="solicitud.banco" class="form-control" >
        </div>
      </template>
      <div class="form-group col-md-6">
        <button type="button" class="btn btn-secondary" @click="quitar_uno()"><i class="fas fa-trash"></i>&nbsp;Limpiar</button>
      </div>
    </div>
    <hr>


  </div>
</template>

<script>
import Utilerias from '../../Herramientas/utilerias.js';
var config = require('../../Herramientas/config-vuetables-client').call(this);
export default {
  data(){
    return {
      empresa_beneficiarios : '',
      tipo_viatico : '',
      nuevo : null,
      solicitud : {
        beneficiariosuno : '',
        datos_bancos_beneficiariosuno : '',
        clavecuentatarjetauno : '',
        claveuno : '',
        cuentauno : '',
        banco : '',
      },
      optionsvs_benficiario_uno : [],
      listaDatosBancariosuno : [],

    }
  },
  watch: {
  'solicitud' : {
      handler : function (after,before){
      // console.log(after,'asdfgh');
      var a = [];
      a.push({
       id : after.beneficiariosuno,
       dbemp_id : after.datos_bancos_beneficiariosuno,
       tarjeta : after.clavecuentatarjetauno,
       clave : after.claveuno,
       cuenta : after.cuentauno,
       banco : after.banco,
      });
      this.$emit('enviarUno', a);
      },
      deep :  true
  }
},

  methods : {

    datos(){
      // this.optionsvs_benficiario_uno = [];
      // this.optionsvs_benficiario_dos = [];
      // axios.get('/vertodosempleados').then(response =>{
      //   for (var i = 0; i < response.data.length; i++) {
      //     this.optionsvs_benficiario_uno.push({
      //       id : response.data[i].id,
      //       name : response.data[i].nombre + ' ' + response.data[i].ap_paterno + ' ' + response.data[i].ap_materno,
      //     });
      //   }
      // }).catch(error => {
      //   console.error(error);
      // })
    },

    getDatos(dato,dato_dos,vs_options){
      this.quitar_uno();
      this.solicitud.beneficiariosuno = '';
      this.optionsvs_benficiario_uno = [];

      this.empresa_beneficiarios = dato;
      this.tipo_viatico = dato_dos;
      this.optionsvs_benficiario_uno = vs_options;
      // console.log(vs_options,'datocomponente');
      // this.datos();
      Utilerias.resetObject(this.solicitud);
    },

    buscar_uno(){
      // this.solicitud.clavecuentatarjetauno = '';
      this.listaDatosBancariosuno = [];
      let me = this;
      axios.get('/datosbanemp/' + me.solicitud.beneficiariosuno.id + '&' + me.empresa_beneficiarios + '/datosbanemp').then(response => {
        me.listaDatosBancariosuno = response.data;
      }).catch(error => {
        // console.error(error);
      });
    },


    enviar_uno(e = null){
      this.limpiaInputs();
      var target = e == null ? 0 : e.target.selectedIndex;
      if (e.target.value != 0) {
          // this.beneficiariosuno : '',
          this.solicitud.datos_bancos_beneficiariosuno = this.listaDatosBancariosuno[e.target.selectedIndex]['id'],
          this.solicitud.clavecuentatarjetauno = this.listaDatosBancariosuno[e.target.selectedIndex]['numero_tarjeta'],
          this.solicitud.claveuno = this.listaDatosBancariosuno[e.target.selectedIndex]['clabe'],
          this.solicitud.cuentauno = this.listaDatosBancariosuno[e.target.selectedIndex]['numero_cuenta'],
          this.solicitud.banco = this.listaDatosBancariosuno[e.target.selectedIndex]['bnombre'];

      }
      // axios.get('/datosbanemp/' + this.solicitud.beneficiariosuno.id + '/datosbanemp').then(response => {
      //   this.solicitud.clavecuentatarjetauno = response.data[target].numero_tarjeta;
      // }).catch(error => {
      //   console.log(error);
      // });
      var a = [];
      a.push({
        id : this.solicitud.beneficiariosuno,
        dbemp_id : this.solicitud.datos_bancos_beneficiariosuno,
        tarjeta : this.solicitud.clavecuentatarjetauno,
        clave : this.solicitud.claveuno,
        cuenta : this.solicitud.cuentauno,
        banco : this.solicitud.banco,
      });
      this.$emit('enviarUno', a);

    },

    // enviar_dos(e = null){
    //   var target = e == null ? 0 : e.target.selectedIndex;
    //   axios.get('/datosbanemp/' + this.solicitud.beneficiariosdos.id + '/datosbanemp').then(response => {
    //     this.solicitud.clavecuentatarjetados = response.data[target].numero_tarjeta;
    //   }).catch(error => {
    //     // console.log(error);
    //   });
    //   var a = [];
    //   a.push({
    //     id : this.solicitud.beneficiariosdos.id,
    //     dbemp_id : this.solicitud.datos_bancos_beneficiariosdos,
    //   });
    //   this.$emit('enviarDos', a);
    // },

    setDatos(data,tipo,dato){
      let me = this;
      this.nuevo = data;
      if (data.length != 0) {
      this.tipo_viatico = tipo;
      this.empresa_beneficiarios = dato;
      if (data[0]['empleado_beneficiario_id'] == 0) {
        this.solicitud.beneficiariosuno  = data[0]['beneficiario_externo'];
        this.solicitud.datos_bancos_beneficiariosuno = data[0]['banco_nombre'];
      }else {
        this.solicitud.beneficiariosuno  = {id: data[0]['empleado_beneficiario_id'], name: data[0]['nombre_beneficiario']};
        this.solicitud.datos_bancos_beneficiariosuno = data[0]['datos_bancarios_empleado_id'];
      }
      this.solicitud.clavecuentatarjetauno = data[0]['tarjeta'];
      this.solicitud.claveuno = data[0]['clabe'];
      this.solicitud.cuentauno = data[0]['cuenta'];
      this.solicitud.banco = data[0]['banco_nombre'];
      // me.enviar_uno();
      // me.buscar_uno();
    }
    },

    quitar_uno(){
      // this.optionsvs_benficiario_uno = [];
      this.solicitud.beneficiariosuno = '';
      this.solicitud.datos_bancos_beneficiariosuno = '';
      this.solicitud.clavecuentatarjetauno = '';
      this.solicitud.claveuno = '';
      this.solicitud.cuentauno = '';
      this.solicitud.banco = '';
      this.listaDatosBancariosuno = [];
      this.$emit('limpiarUno');
    },

    limpiaInputs(){
      this.solicitud.clavecuentatarjetauno = '';
      this.solicitud.claveuno = '';
      this.solicitud.cuentauno = '';
      this.solicitud.banco = '';
    },

  },
  mounted (){
  }
}
</script>
