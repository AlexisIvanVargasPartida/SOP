<template>
  <ValidationObserver ref="form">
    <form @submit.prevent="validate">
      <div>
        <h5 class="info-text">
          Conocimiento para el candidato
        </h5>
        <div class="md-layout">
          <div
            class="md-layout-item md-small-size-100 mx-auto"
            :class="pageSize"
          >
            <div class="md-layout">
              <label
                class="md-layout-item md-size-35 md-small-size-100 md-medium-size-100 md-form-label"
              >
                ¿Simpatiza con {{ $store.state.sop.user.candidato }} ...?
              </label>
              <div
                class="md-layout-item md-size-65 md-small-size-100 md-medium-size-100"
              >
                <ValidationProvider
                  name="simpatiza"
                  rules="required"
                  v-slot="{ passed, failed }"
                >
                  <md-field
                    :class="[{ 'md-error': failed }, { 'md-valid': passed }]"
                  >
                    <md-select
                      v-model="simpatiza"
                      name="simpatiza"
                      id="simpatiza"
                      @input="
                        sendData('simpatiza', simpatiza);
                        participacion = '';
                        sendData('participacion', '');
                        negativa = '';
                        sendData('negativa', '');
                        likeconocer = '';
                        sendData('likeconocer', '');
                      "
                    >
                      <md-option value="">-- Respuesta --</md-option>
                      <md-option value="SI">SI</md-option>
                      <md-option value="NO">NO</md-option>
                      <md-option value="NO LO CONOZCO">NO LO CONOZCO</md-option>
                      <md-option value="NO DECIDE">NO DECIDE</md-option>
                    </md-select>
                  </md-field>
                </ValidationProvider>
              </div>
              <label
                v-if="simpatiza == 'SI'"
                class="md-layout-item md-size-35 md-small-size-100 md-medium-size-100 md-form-label"
              >
                ¿Participaria ...?
              </label>

              <div
                v-if="simpatiza == 'SI'"
                class="md-layout-item md-size-65 md-small-size-100 md-medium-size-100"
              >
                <md-radio
                  v-model="participacion"
                  value="Solo Simpatizo"
                  @change="sendData('participacion', participacion)"
                  >Sólo Simpatizo</md-radio
                >
                <md-radio
                  v-model="participacion"
                  @change="sendData('participacion', participacion)"
                  value="Promotor"
                  class="md-primary"
                  >Promotor</md-radio
                >
              </div>
              <label
                v-if="simpatiza == 'NO'"
                class="md-layout-item md-size-35 md-small-size-100 md-medium-size-100 md-form-label"
              >
                ¿Por qué no...?
              </label>

              <div
                v-if="simpatiza == 'NO'"
                class="md-layout-item md-size-65 md-small-size-100 md-medium-size-100"
              >
                <md-radio
                  v-model="negativa"
                  value="Otro Partido"
                  @change="sendData('negativa', negativa)"
                  >Otro Partido</md-radio
                >
                <md-radio
                  v-model="negativa"
                  @change="sendData('negativa', negativa)"
                  value="Abstinencia"
                  class="md-primary"
                  >Abstinencia</md-radio
                >
                <md-radio
                  v-model="negativa"
                  @change="sendData('negativa', negativa)"
                  value="Deseo Esperar"
                  class="md-primary"
                  >Deseo Esperar</md-radio
                >
              </div>

              <label
                v-if="simpatiza == 'NO LO CONOZCO'"
                class="md-layout-item md-size-35 md-small-size-100 md-medium-size-100 md-form-label"
              >
                ¿Le gustaría conocerlo?
              </label>

              <div
                v-if="simpatiza == 'NO LO CONOZCO'"
                class="md-layout-item md-size-65 md-small-size-100 md-medium-size-100"
              >
                <md-radio
                  v-model="likeconocer"
                  value="SI"
                  @change="sendData('likeconocer', likeconocer)"
                  >Si</md-radio
                >
                <md-radio
                  v-model="likeconocer"
                  @change="sendData('likeconocer', likeconocer)"
                  value="NO"
                  class="md-primary"
                  >No</md-radio
                >
              </div>
            </div>
          </div>
        </div>
      </div>
    </form>
  </ValidationObserver>
</template>
<script>
import { APIURL } from "@/api.js";
import axios from "axios";
import { extend } from "vee-validate";
import { required } from "vee-validate/dist/rules";

extend("required", required);

export default {
  props: {
    pageSize: {
      type: String,
      default: "md-size-70"
    }
  },
  data() {
    return {
      simpatiza: "",
      participacion: "",
      negativa: "",
      likeconocer: ""
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
        if (this.simpatiza == "SI" && this.participacion == "") {
          return Promise.resolve(false);
        }
        if (this.simpatiza == "NO" && this.negativa == "") {
          return Promise.resolve(false);
        }
        if (this.simpatiza == "NO LO CONOZCO" && this.likeconocer == "") {
          return Promise.resolve(false);
        }
        this.$emit("on-validated", res);
        return res;
      });
    }
  },
  created() {}
};
</script>
<style lang="scss">
</style>
