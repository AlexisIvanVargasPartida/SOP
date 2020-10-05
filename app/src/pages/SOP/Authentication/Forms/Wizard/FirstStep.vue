<template>
  <ValidationObserver ref="form">
    <form @submit.prevent="validate">
      <div>
        <h5 class="info-text">
          Información personal sobre el candidato
        </h5>
        <div class="md-layout">
          <div class="md-layout-item md-size-60 md-small-size-100 mx-auto">
            <ValidationProvider
              name="nombre"
              rules="required|min:4"
              v-slot="{ passed, failed }"
            >
              <md-field
                :class="[{ 'md-error': failed }, { 'md-valid': passed }]"
              >
                <label>Nombre</label>
                <md-input
                  v-model="nombre"
                  type="text"
                  @change="sendData('nombre', nombre)"
                  @keyup.prevent="
                    nombre = $helpers.primeraMayuscula($event.target.value)
                  "
                >
                </md-input>

                <slide-y-down-transition>
                  <md-icon class="error" v-show="failed">close</md-icon>
                </slide-y-down-transition>
                <slide-y-down-transition>
                  <md-icon class="success" v-show="passed">done</md-icon>
                </slide-y-down-transition>
              </md-field>
            </ValidationProvider>
          </div>
          <div class="md-layout-item md-size-60 md-small-size-100 mx-auto">
            <ValidationProvider
              name="clave"
              rules="required"
              v-slot="{ passed, failed }"
            >
              <md-field
                :class="[{ 'md-error': failed }, { 'md-valid': passed }]"
              >
                <label>Partido Politíco</label>
                <md-input
                  v-model="partido"
                  type="text"
                  @change="sendData('partido', partido)"
                  @keyup.prevent="
                    partido = $helpers.todasMayusculas($event.target.value)
                  "
                >
                </md-input>
                <slide-y-down-transition>
                  <md-icon class="error" v-show="failed">close</md-icon>
                </slide-y-down-transition>
                <slide-y-down-transition>
                  <md-icon class="success" v-show="passed">done</md-icon>
                </slide-y-down-transition>
              </md-field>
            </ValidationProvider>
          </div>
          <div
            class="md-layout-item md-size-60 md-small-size-100 mx-auto"
            v-if="1 == 2"
          >
            <ValidationProvider
              name="clave"
              rules="required|min:18|max:18|clve"
              v-slot="{ passed, failed }"
            >
              <md-field
                :class="[{ 'md-error': failed }, { 'md-valid': passed }]"
              >
                <label>Clave Elector</label>
                <md-input
                  v-model="clave"
                  type="text"
                  @change="sendData('clave', clave)"
                  @keyup.prevent="
                    clave = $helpers.todasMayusculas($event.target.value)
                  "
                >
                </md-input>

                <slide-y-down-transition>
                  <md-icon class="error" v-show="failed">close</md-icon>
                </slide-y-down-transition>
                <slide-y-down-transition>
                  <md-icon class="success" v-show="passed">done</md-icon>
                </slide-y-down-transition>
              </md-field>
            </ValidationProvider>
          </div>
        </div>
      </div>
    </form>
  </ValidationObserver>
</template>
<script>
import { SlideYDownTransition } from "vue2-transitions";
import { extend } from "vee-validate";
import { required, email, min, max } from "vee-validate/dist/rules";

extend("required", required);
extend("email", email);
extend("min", min);
extend("max", max);

export default {
  components: {
    SlideYDownTransition
  },
  data() {
    return {
      nombre: "",
      clave: "",
      partido: ""
    };
  },
  methods: {
    sendData(field, val) {
      this.$emit("data", { field: field, value: val });
    },
    validate() {
      return this.$refs.form.validate().then(res => {
        this.$emit("on-validated", res);
        return res;
      });
    }
  },
  created() {
    extend("clve", {
      validate: value =>
        new Promise(resolve => {
          let regex = new RegExp(
            "[A-Z]{6}[0-9]{2}(0[1-9]|1[0-2])(0[1-9]|1[0-9]|2[0-9]|3[0-1])[0-3]{1}[0-9]{1}[HM]{1}[0-9]{1}[0-9]{2}$"
          );
          resolve({
            valid: value && regex.test(value)
          });
        })
    });
  }
};
</script>
<style></style>
