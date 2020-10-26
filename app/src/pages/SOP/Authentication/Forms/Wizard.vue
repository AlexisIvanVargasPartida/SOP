<template>
  <div class="md-layout">
    <div
      class="md-layout-item md-size-80 md-xsmall-size-80 mx-auto"
      v-if="!dataSend"
    >
      <simple-wizard :asycFinish="asycFinish">
        <template slot="header">
          <h3 class="title" style="color: #999999;">Registro</h3>
          <h5 class="category">
            Registro de Candidato para gestionar.
          </h5>
        </template>

        <wizard-tab :before-change="() => validateStep('step1')">
          <template slot="label">
            Información Personal
          </template>
          <first-step
            ref="step1"
            @on-validated="onStepValidated"
            @data="getData"
          ></first-step>
        </wizard-tab>

        <wizard-tab :before-change="() => validateStep('step2')">
          <template slot="label">
            Selección de Candidatura
          </template>
          <second-step
            ref="step2"
            @on-validated="onStepValidated"
            @data="getData"
          ></second-step>
        </wizard-tab>

        <wizard-tab :before-change="() => validateStep('step3')">
          <template slot="label">
            Cuenta de Accesso
          </template>
          <third-step
            ref="step3"
            @on-validated="wizardComplete"
            @data="getData"
          ></third-step>
        </wizard-tab>
      </simple-wizard>
    </div>
    <div class="md-layout-item md-size-66 md-xsmall-size-80 mx-auto" v-else>
      <md-card>
        <md-card-content style="text-align: center;">
          <md-empty-state
            class="md-primary"
            md-icon="done"
            md-label="Registro Exitoso"
            md-description="Tu cuenta está lista, Inicia Sesión para empezar a gestionar"
          ></md-empty-state>

          <md-button class="md-success md-success md-lg" to="/login">
            Iniciar
          </md-button>
        </md-card-content>
      </md-card>
    </div>
  </div>
</template>
<script>
import FirstStep from "./Wizard/FirstStep.vue";
import SecondStep from "./Wizard/SecondStep.vue";
import ThirdStep from "./Wizard/ThirdStep.vue";
import { APIURL } from "@/api.js";
import axios from "axios";
import { SimpleWizard, WizardTab } from "@/components";

export default {
  data() {
    return {
      wizardModel: {},
      data: {},
      dataSend: false,
      asycFinish: false
    };
  },
  components: {
    FirstStep,
    SecondStep,
    ThirdStep,
    SimpleWizard,
    WizardTab
  },
  methods: {
    validateStep(ref) {
      return this.$refs[ref].validate();
    },
    onStepValidated(validated, model) {
      this.wizardModel = { ...this.wizardModel, ...model };
    },
    getData(data) {
      this.data[data.field] = data.value;
    },
    wizardComplete() {
      this.sendForm();
    },
    sendForm() {
      let cObject = this;
      this.asycFinish = true;
      axios
        .post(APIURL + "registro", {
          partido: this.data.partido,
          ce: this.data.clave,
          values: this.data.values,
          nombre: this.data.nombre,
          email: this.data.email,
          password: this.data.password
        })
        .then(response => {
          cObject.dataSend = true;
        })
        .catch(error => {
          cObject.asycFinish = false;
          cObject.$helpers.catchError(error);
        });
    }
  }
};
</script>
