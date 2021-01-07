
<template>

  <div class="md-layout text-center">
       <md-dialog-alert
                  :md-active.sync="alerta"
                  md-title="Alerta"
                  md-content="Las contraseñas no coinciden"
                  md-confirm-text="ACEPTAR"
                  />
        
        <md-dialog
                 :md-active.sync="alerta2"
                  >
                  <md-dialog-title>OK</md-dialog-title>
                  <center>
                  <p >Contraseña cambiada </p>
                  <p>correctamente</p>
                  </center>
                  <md-dialog-actions>
                      <md-button class="md-primary" @click="redireccionar()">
                          ACEPTAR
                      </md-button>
                  </md-dialog-actions>
                  </md-dialog>
        <md-dialog
                 :md-active.sync="alerta3"
                  >
                  <md-dialog-title>Alerta</md-dialog-title>
                   <center>
                  <p>El Token ha expirado</p>
                  </center>
                  <md-dialog-actions>
                      <md-button class="md-primary" @click="redireccionar2()">
                          ACEPTAR
                      </md-button>
                  </md-dialog-actions>
                  </md-dialog>
    <div
      class="md-layout-item md-size-33 md-medium-size-50 md-small-size-70 md-xsmall-size-100"
    >
      <ValidationObserver v-slot="{ handleSubmit }">
        <form @submit.prevent="handleSubmit(login)">
          <login-card header-color="green">
            <h4 slot="title" class="title">
              <strong>SOP</strong> | Sistema de Operatividad Política
            </h4>
            <p slot="description" class="description"><b>Cambia tu contraseña</b></p>

            <div
              class="alert alert-danger"
              slot="description"
              style="padding:0px; line-height: 25px;"
              v-if="error"
            >
              <span style=" max-width:100%">{{ error }}</span>
            </div>
            <div slot="inputs">
              <ValidationProvider
                name="email"
                rules="required"
                v-slot="{ passed, failed }"
              >
                <md-field
                  class="md-form-group"
                  :class="[{ 'md-error': failed }, { 'md-valid': passed }]"
                >
                  <md-icon>password</md-icon>
                  <label>Contraseña</label>
                  <md-input
                    v-model="password"
                    type="password"
                    
                  ></md-input>
                </md-field>
              </ValidationProvider>
            </div>
            <div slot="inputs">
              <ValidationProvider
                name="email"
                rules="required"
                v-slot="{ passed, failed }"
              >
                <md-field
                  class="md-form-group"
                  :class="[{ 'md-error': failed }, { 'md-valid': passed }]"
                >
                  <md-icon>password</md-icon>
                  <label>Confirmar</label>
                  <md-input
                    v-model="confirm"
                    type="password"
                  ></md-input>
                </md-field>
              </ValidationProvider>
            </div>

           

            <div slot="footer" class="md-layout">
              <div
                class="md-layout-item md-size-100 md-small-size-100"
                v-if="btn"
              >
                <md-button class="md-success md-success md-lg" v-on:click="restauraContrasena()">
                  Enviar
                </md-button>
              </div>
              <div
                class="md-layout-item md-size-50 md-small-size-100 mx-auto"
                v-if="!btn"
              >
                <md-progress-bar
                  class="md-success"
                  md-mode="buffer"
                  :md-value="0"
                  :md-buffer="0"
                ></md-progress-bar>
              </div>
            </div>
          </login-card>
        </form>
      </ValidationObserver>
    </div>
  </div>
</template>
<script>
import { APIURL } from "@/api.js";
import { LoginCard } from "@/components";
import { extend } from "vee-validate";
import axios from 'axios';
import { email } from 'vee-validate/dist/rules';


export default {
  components: {
    LoginCard
  },
  data() {
    return {
      alerta:false,
      alerta2:false,
      alerta3:false,
      password:"",
      confirm:"",
      token:"",
      redirect:false,
      r:"",
      error: null,
      btn: true
    };
  },
  methods: {
       restauraContrasena(){
       let cObject=this;
       if(cObject.password!=cObject.confirm){
           cObject.alerta=true;
           return;

       }
      axios
        .post(APIURL + "cambiar/password", {
      
          password:cObject.password,
          token:cObject.token,
          headers: {
            Authorization: "Bearer " + this.$store.state.sop.authorization.token
          }
        })
        .then(response => {
          if(response.data.code==202){
            cObject.alerta3=true;
          }
           if(response.data.code==200){
            cObject.alerta2=true;
          }
        })
        .catch(error => {
          cObject.$helpers.catchError(error);
        });
      
    },
    redireccionar(){
        window.location.href ="http://localhost:8080/login";
    },
    redireccionar2(){
        window.location.href ="http://localhost:8080/recuperar-contrasena";
    }
   
  },
  watch:{
    redirect:function(a,b){
        console.log(a,b);
    }
  },
   created() {
   let url=window.location.href;
   let array=url.split("/");
   this.token=array[array.length-1];
  }
};
</script>

<style></style>