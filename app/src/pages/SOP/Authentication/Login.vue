<template>
  <div class="md-layout text-center">
    <div
      class="md-layout-item md-size-33 md-medium-size-50 md-small-size-70 md-xsmall-size-100"
    >
      <ValidationObserver v-slot="{ handleSubmit }">
        <form @submit.prevent="handleSubmit(login)">
          <login-card header-color="green">
            <h4 slot="title" class="title">
              <strong>SOP</strong> | Sistema de Operatividad Política
            </h4>
            <p slot="description" class="description">Inicio de Sesión</p>

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
                  <md-icon>person</md-icon>
                  <label>Usuario...</label>
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

            <div slot="inputs">
              <ValidationProvider
                name="password"
                rules="required"
                v-slot="{ passed, failed }"
              >
                <md-field
                  class="md-form-group"
                  :class="[{ 'md-error': failed }, { 'md-valid': passed }]"
                >
                  <md-icon>lock_outline</md-icon>
                  <label>Contraseña...</label>
                  <md-input v-model="password" type="password"></md-input>
                </md-field>
              </ValidationProvider>
            </div>

            <div slot="footer" class="md-layout">
              <div
                class="md-layout-item md-size-100 md-small-size-100"
                v-if="btn"
              >
                <md-button class="md-success md-success md-lg" type="submit">
                  Iniciar
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
import { LoginCard } from "@/components";
import { extend } from "vee-validate";
import { required, email } from "vee-validate/dist/rules";

extend("email", email);
extend("required", required);
export default {
  components: {
    LoginCard
  },
  data() {
    return {
      email: null,
      password: null,
      error: null,
      btn: true
    };
  },
  methods: {
    login() {
      this.btn = false;
      this.$store
        .dispatch("retrieveFetchup", {
          email: this.email,
          password: this.password
        })
        .then(response => {
          let varRes = response;
          this.$router.push({ name: "Dashboard" });
        })
        .catch(error => {
          this.btn = true;
          this.error = error.message || error.response.data.error.message;
        });
    }
  }
};
</script>

<style>
</style>
