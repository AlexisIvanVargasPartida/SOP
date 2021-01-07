<template>

  <div class="md-layout text-center">
       <md-dialog-alert
                  :md-active.sync="alerta"
                  md-title="Alerta"
                  md-content="El usuario con el correo que ingresó, no se encuentra registrado"
                  md-confirm-text="ACEPTAR"
                  />
        <md-dialog-alert
                 :md-active.sync="alerta2"
                  md-title="OK"
                  md-content="Revise su correo y siga las instrucciones para restaurar su contraseña"
                  md-confirm-text="ACEPTAR"
                  />
    <div
      class="md-layout-item md-size-33 md-medium-size-50 md-small-size-70 md-xsmall-size-100"
    >
      <ValidationObserver v-slot="{ handleSubmit }">
        <form @submit.prevent="handleSubmit(login)">
          <login-card header-color="green">
            <h4 slot="title" class="title">
              <strong>SOP</strong> | Sistema de Operatividad Política
            </h4>
            <p slot="description" class="description"><b>Recuperar tu cuenta</b></p>

            <div
              class="alert alert-danger"
              slot="description"
              style="padding:0px; line-height: 25px;"
              v-if="error"
            >
              <span style=" max-width:100%">{{ error }}</span>
            </div>
             <p slot="description" class="description">	
            Ingresa tu correo electrónico para buscar tu cuenta.</p>
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
                  <md-icon>email</md-icon>
                  <label>Correo</label>
                  <md-input
                    v-model="email"
                    type="text"
                    @keyup.prevent="
                      email = $helpers.todasMinusculas($event.target.value)
                    "
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
      email: null,
      error: null,
      btn: true
    };
  },
  methods: {
       restauraContrasena(){
       let cObject=this;
      axios
        .post(APIURL + "solicita/cambio/pass", {
      
          email:cObject.email,
          headers: {
            Authorization: "Bearer " + this.$store.state.sop.authorization.token
          }
        })
        .then(response => {
          if(response.data.code==202){
            cObject.alerta=true;
          }
           if(response.data.code==200){
            cObject.alerta2=true;
          }
        })
        .catch(error => {
          cObject.$helpers.catchError(error);
        });
      
    },
   
  }
};
</script>

<style></style>