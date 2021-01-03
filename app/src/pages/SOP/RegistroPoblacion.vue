<template>
  <div class="md-layout">
    <md-dialog-alert
                  :md-active.sync="alerta"
                  md-title="Alerta"
                  md-content="La Clave de Elector ya se encuentra registrada"
                  md-confirm-text="ACEPTAR"
                  />
    <div
      class="md-layout-item md-size-80 md-xsmall-size-80 mx-auto"
      v-if="!dataSend"
    >
      <simple-wizard :asycFinish="asycFinish" :finishButtonText="'Guardar'">
        <template slot="header">
          <h3 class="title" style="color: #999999;">Registro Población</h3>
          <!--<h5 class="category">
            Registro de Población para gestionar.
          </h5>-->
        </template>

        <wizard-tab :before-change="() => validateStep('step1')">
          <template slot="label">
            Información Personal
          </template>
          <registro-poblacion
            ref="step1"
            @on-validated="onStepValidated"
            @data="getData"
          ></registro-poblacion>
        </wizard-tab>

        <wizard-tab :before-change="() => validateStep('step2')">
          <template slot="label">
            Simpatizante
          </template>
          <simpatizante
            ref="step2"
            @on-validated="wizardComplete"
            @data="getData"
          ></simpatizante>
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
            md-description="Registros de población actualizada!"
          ></md-empty-state>

          <md-button class="md-success md-success md-lg" to="/poblacion">
            Ir a Población
          </md-button>
        </md-card-content>
      </md-card>
    </div>
  </div>
</template>
<script>
import RegistroPoblacion from "./Forms/RegistroPoblacion";
import Simpatizante from "./Forms/Simpatiza";
import { APIURL } from "@/api.js";
import axios from "axios";
import { SimpleWizard, WizardTab } from "@/components";

export default {
  data() {
    return {
      alerta:false,
      wizardModel: {},
      data: {},
      dataSend: false,
      asycFinish: false
    };
  },
  components: {
    RegistroPoblacion,
    Simpatizante,
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
      let dataCandidato = {
        telefonos: [],
        negativa: this.data.negativa || null,
        participacion: this.data.participacion || null,
        likeconocer: this.data.likeconocer || null,
        redsocial: this.data.redsocial || null
      };

      dataCandidato.telefonos.push(this.data.telefono);
      if (this.data.adicional_tel)
        dataCandidato.telefonos.push(this.data.adicional_tel);

      dataCandidato.telefonos;
      axios
        .post(
          APIURL + "registro/poblacion",
          {
            cve_elector: this.data.ce,
            paterno: this.data.apellidop,
            materno: this.data.apellidom,
            nombre: this.data.nombre,
            lugar_nacimiento: this.data.nacimiento,
            sexo: this.data.sexo,
            calle: this.data.calle,
            num_ext: this.data.numext,
            num_int: this.data.numint,
            colonia: this.data.colonia,
            cp: this.data.cp,
            seccion: this.data.seccion,
            simpatiza: this.data.simpatiza,
            year: this.data.year,
            month: this.data.month,
            day: this.data.day,
            data: JSON.stringify(dataCandidato)
          },
          {
            headers: {
              Authorization:
                "Bearer " + this.$store.state.sop.authorization.token
            }
          }
        )
        .then(response => {
          if(response.data==202){
            cObject.alerta=true;
            return;
          }
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
