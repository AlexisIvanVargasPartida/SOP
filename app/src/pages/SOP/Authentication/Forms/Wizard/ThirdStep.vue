<template>
  <ValidationObserver ref="form">
    <form @submit.prevent="validate">
      <div class="md-layout">
        <div class="md-layout-item md-size-100">
          <h5 class="info-text">
            Ingrese su correo y contraseña para crear su cuenta
          </h5>
        </div>
        <div class="md-layout-item md-size-60 md-small-size-100 mx-auto">
          <ValidationProvider
            name="email"
            rules="required|email"
            v-slot="{ passed, failed }"
          >
            <md-field
              class="md-form-group"
              :class="[{ 'md-error': failed }, { 'md-valid': passed }]"
            >
              <md-icon>person</md-icon>
              <label>Correo Electronico</label>
              <md-input
                v-model="email"
                type="text"
                @change="sendData('email', email)"
                @keyup.prevent="
                  email = $helpers.todasMinusculas($event.target.value)
                "
              ></md-input>
            </md-field>
          </ValidationProvider>
        </div>
        <div class="md-layout-item md-size-60 md-small-size-100 mx-auto">
          <ValidationProvider
            name="password"
            rules="required|min:6"
            v-slot="{ passed, failed }"
          >
            <md-field
              class="md-form-group"
              :class="[{ 'md-error': failed }, { 'md-valid': passed }]"
            >
              <md-icon>lock_outline</md-icon>
              <label>Contraseña</label>
              <md-input
                v-model="password"
                type="password"
                @change="sendData('password', password)"
              ></md-input>
            </md-field>
          </ValidationProvider>
        </div>
        <div class="md-layout-item md-size-60 md-small-size-100 mx-auto">
          <ValidationProvider
            name="passwordv"
            rules="required|confirmed:password"
            v-slot="{ passed, failed }"
          >
            <md-field
              class="md-form-group"
              :class="[{ 'md-error': failed }, { 'md-valid': passed }]"
            >
              <md-icon>lock_outline</md-icon>
              <label>Verifique su contraseña</label>
              <md-input v-model="passwordv" type="password"></md-input>
            </md-field>
          </ValidationProvider>
        </div>
      </div>
    </form>
  </ValidationObserver>
</template>
<script>
import { extend } from "vee-validate";
import { required, email, confirmed, min } from "vee-validate/dist/rules";

extend("required", required);
extend("email", email);
extend("confirmed", confirmed);
extend("min", min);

export default {
  data() {
    return {
      email: "",
      password: "",
      passwordv: ""
    };
  },
  methods: {
    sendData(field, val) {
      this.$emit("data", { field: field, value: val });
    },
    validate() {
      return this.$refs.form.validate().then(res => {
        if (!res) {
          return;
        }
        this.$emit("on-validated", res);
        return res;
      });
    }
  }
};
</script>
<style></style>
