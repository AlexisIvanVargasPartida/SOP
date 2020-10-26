<template>
  <ValidationObserver ref="form">
    <form @submit.prevent="validate">
      <div>
        <h5 class="info-text">
          Información personal
        </h5>
        <div class="md-layout">
          <div class="md-layout-item md-size-70 md-small-size-100 mx-auto">
            <div class="md-layout">
              <div
                class="md-layout-item md-size-100 md-small-size-100 md-medium-size-100"
              >
                <ValidationProvider
                  name="nombre"
                  rules="required|min:4"
                  v-slot="{ passed, failed }"
                >
                  <md-field
                    :class="[{ 'md-error': failed }, { 'md-valid': passed }]"
                  >
                    <label>Nombres</label>
                    <md-input
                      v-model="nombre"
                      type="text"
                      @change="
                        sendData('nombre', nombre);
                        generaCE();
                      "
                      @keyup.prevent="
                        nombre = $helpers.todasMayusculas($event.target.value)
                      "
                    >
                    </md-input>
                  </md-field>
                </ValidationProvider>
              </div>
              <div
                class="md-layout-item md-size-50 md-small-size-100 md-medium-size-100"
              >
                <ValidationProvider
                  name="apellidop"
                  rules="required|min:4"
                  v-slot="{ passed, failed }"
                >
                  <md-field
                    :class="[{ 'md-error': failed }, { 'md-valid': passed }]"
                  >
                    <label>Apellido Paterno</label>
                    <md-input
                      v-model="apellidop"
                      type="text"
                      @change="
                        sendData('apellidop', apellidop);
                        generaCE();
                      "
                      @keyup.prevent="
                        apellidop = $helpers.todasMayusculas(
                          $event.target.value
                        )
                      "
                    >
                    </md-input>
                  </md-field>
                </ValidationProvider>
              </div>
              <div
                class="md-layout-item md-size-50 md-small-size-100 md-medium-size-100"
              >
                <ValidationProvider
                  name="apellidom"
                  rules="required|min:4"
                  v-slot="{ passed, failed }"
                >
                  <md-field
                    :class="[{ 'md-error': failed }, { 'md-valid': passed }]"
                  >
                    <label>Apellido Materno</label>
                    <md-input
                      v-model="apellidom"
                      type="text"
                      @change="
                        sendData('apellidom', apellidom);
                        generaCE();
                      "
                      @keyup.prevent="
                        apellidom = $helpers.todasMayusculas(
                          $event.target.value
                        )
                      "
                    >
                    </md-input>
                  </md-field>
                </ValidationProvider>
              </div>
              <label
                class="md-layout-item md-size-100 md-form-label"
                style="padding-left: 10px;"
              >
                Fecha de Nacimiento
              </label>
              <div
                class="md-layout-item md-size-30 md-small-size-100 md-medium-size-100"
              >
                <ValidationProvider
                  name="day"
                  rules="required|numeric|min_value:1|max_value:31"
                  v-slot="{ passed, failed }"
                >
                  <md-field
                    :class="[{ 'md-error': failed }, { 'md-valid': passed }]"
                  >
                    <label>Día</label>
                    <md-input
                      v-model="day"
                      type="text"
                      @change="
                        sendData('day', day);
                        generaCE();
                      "
                    >
                    </md-input>
                  </md-field>
                </ValidationProvider>
              </div>
              <div
                class="md-layout-item md-size-40 md-small-size-100 md-medium-size-100"
              >
                <ValidationProvider
                  name="month"
                  rules="required|numeric"
                  v-slot="{ passed, failed }"
                >
                  <md-field
                    :class="[{ 'md-error': failed }, { 'md-valid': passed }]"
                  >
                    <label>Mes</label>
                    <md-select
                      v-model="month"
                      name="month"
                      id="month"
                      @input="
                        sendData('month', month);
                        generaCE();
                      "
                    >
                      <md-option value="1">Enero</md-option>
                      <md-option value="2">Febrero</md-option>
                      <md-option value="3">Marzo</md-option>
                      <md-option value="4">Abril</md-option>
                      <md-option value="5">Mayo</md-option>
                      <md-option value="6">Junio</md-option>
                      <md-option value="7">Julio</md-option>
                      <md-option value="8">Agosto</md-option>
                      <md-option value="9">Septimbre</md-option>
                      <md-option value="10">Octubre</md-option>
                      <md-option value="11">Noviembre</md-option>
                      <md-option value="12">Diciembre</md-option>
                    </md-select>
                  </md-field>
                </ValidationProvider>
              </div>
              <div
                class="md-layout-item md-size-30 md-small-size-100 md-medium-size-100"
              >
                <ValidationProvider
                  name="year"
                  rules="required|numeric|min_value:1920|max_value:2002"
                  v-slot="{ passed, failed }"
                >
                  <md-field
                    :class="[{ 'md-error': failed }, { 'md-valid': passed }]"
                  >
                    <label>Año</label>
                    <md-input
                      v-model="year"
                      type="text"
                      @change="
                        sendData('year', year);
                        generaCE();
                      "
                    >
                    </md-input>
                  </md-field>
                </ValidationProvider>
              </div>
              <div
                class="md-layout-item md-size-50 md-small-size-100 md-medium-size-100"
              >
                <ValidationProvider
                  name="nacimiento"
                  rules="required|numeric"
                  v-slot="{ passed, failed }"
                >
                  <md-field
                    :class="[{ 'md-error': failed }, { 'md-valid': passed }]"
                  >
                    <label>Lugar de Nacimiento</label>
                    <md-select
                      v-model="nacimiento"
                      name="nacimiento"
                      id="nacimiento"
                      @input="
                        sendData('nacimiento', nacimiento);
                        generaCE();
                      "
                    >
                      <md-option
                        v-for="ef in entidadeFederativas"
                        :key="ef.id"
                        :value="ef.id"
                        >{{ ef.nombre }}</md-option
                      >
                      <md-option value="99">EXTRANJERO</md-option>
                    </md-select>
                  </md-field>
                </ValidationProvider>
              </div>
              <div
                class="md-layout-item md-size-50 md-small-size-100 md-medium-size-100"
              >
                <ValidationProvider
                  name="sexo"
                  rules="required"
                  v-slot="{ passed, failed }"
                >
                  <md-field
                    :class="[{ 'md-error': failed }, { 'md-valid': passed }]"
                  >
                    <label>Sexo</label>
                    <md-select
                      v-model="sexo"
                      name="sexo"
                      id="sexo"
                      @input="
                        sendData('sexo', sexo);
                        generaCE();
                      "
                    >
                      <md-option value="H">Hombre</md-option>
                      <md-option value="M">Mujer</md-option>
                    </md-select>
                  </md-field>
                </ValidationProvider>
              </div>
              <div
                class="md-layout-item md-size-100 md-small-size-100 md-medium-size-100"
              >
                <ValidationProvider
                  name="ce"
                  rules="required|min:15|max:15|clve"
                  v-slot="{ passed, failed }"
                >
                  <md-field
                    :class="[{ 'md-error': failed }, { 'md-valid': passed }]"
                  >
                    <label>Clave Elector</label>
                    <md-input
                      v-model="ce"
                      type="text"
                      @change="sendData('ce', ce)"
                      disabled
                    >
                    </md-input>
                  </md-field>
                </ValidationProvider>
              </div>
              <div
                class="md-layout-item md-size-30 md-small-size-100 md-medium-size-100"
              >
                <ValidationProvider
                  name="cp"
                  rules="required|numeric"
                  v-slot="{ passed, failed }"
                >
                  <md-field
                    :class="[{ 'md-error': failed }, { 'md-valid': passed }]"
                  >
                    <label>Código Postal</label>
                    <md-input
                      v-model="cp"
                      type="text"
                      @change="
                        sendData('cp', cp);
                        getColonias();
                      "
                    >
                    </md-input>
                  </md-field>
                </ValidationProvider>
              </div>
              <div
                class="md-layout-item md-size-35 md-small-size-100 md-medium-size-100"
              >
                <ValidationProvider
                  name="colonia"
                  rules="required"
                  v-slot="{ passed, failed }"
                >
                  <md-field
                    :class="[{ 'md-error': failed }, { 'md-valid': passed }]"
                  >
                    <label>Colonia</label>
                    <md-select
                      v-model="colonia"
                      name="colonia"
                      id="colonia"
                      @input="
                        sendData('colonia', colonia);
                        getColoniasSecciones();
                      "
                    >
                      <md-option
                        v-for="(col, id) in colonias"
                        :key="id"
                        :value="col.nombre"
                        >{{ col.nombre }}</md-option
                      >
                    </md-select>
                  </md-field>
                </ValidationProvider>
              </div>
              <div
                class="md-layout-item md-size-35 md-small-size-100 md-medium-size-100"
              >
                <ValidationProvider
                  name="seccion"
                  rules="required|numeric"
                  v-slot="{ passed, failed }"
                >
                  <md-field
                    :class="[{ 'md-error': failed }, { 'md-valid': passed }]"
                  >
                    <label>Sección</label>
                    <md-select
                      v-model="seccion"
                      name="seccion"
                      id="seccion"
                      @input="sendData('seccion', seccion)"
                    >
                      <md-option
                        v-for="(sec, id) in secciones"
                        :key="id"
                        :value="sec.id"
                        >Sección {{ sec.seccion }}</md-option
                      >
                    </md-select>
                  </md-field>
                </ValidationProvider>
              </div>
              <div
                class="md-layout-item md-size-100 md-small-size-100 md-medium-size-100"
              >
                <ValidationProvider
                  name="calle"
                  rules="required|min:3"
                  v-slot="{ passed, failed }"
                >
                  <md-field
                    :class="[{ 'md-error': failed }, { 'md-valid': passed }]"
                  >
                    <label>Calle</label>
                    <md-input
                      v-model="calle"
                      type="text"
                      @change="sendData('calle', calle)"
                      @keyup.prevent="
                        calle = $helpers.todasMayusculas($event.target.value)
                      "
                    >
                    </md-input>
                  </md-field>
                </ValidationProvider>
              </div>
              <div
                class="md-layout-item md-size-50 md-small-size-100 md-medium-size-100"
              >
                <ValidationProvider
                  name="numext"
                  rules="required"
                  v-slot="{ passed, failed }"
                >
                  <md-field
                    :class="[{ 'md-error': failed }, { 'md-valid': passed }]"
                  >
                    <label>Número Exterior</label>
                    <md-input
                      v-model="numext"
                      type="text"
                      @change="sendData('numext', numext)"
                      @keyup.prevent="
                        numext = $helpers.todasMayusculas($event.target.value)
                      "
                    >
                    </md-input>
                  </md-field>
                </ValidationProvider>
              </div>
              <div
                class="md-layout-item md-size-50 md-small-size-100 md-medium-size-100"
              >
                <md-field>
                  <label>Número Interior</label>
                  <md-input
                    v-model="numint"
                    type="text"
                    @change="sendData('numint', numint)"
                    @keyup.prevent="
                      numint = $helpers.todasMayusculas($event.target.value)
                    "
                  >
                  </md-input>
                </md-field>
              </div>
              <div
                class="md-layout-item md-size-50 md-small-size-100 md-medium-size-100"
              >
                <ValidationProvider
                  name="telefono"
                  rules="required|numeric|min:10|max:10"
                  v-slot="{ passed, failed }"
                >
                  <md-field
                    :class="[{ 'md-error': failed }, { 'md-valid': passed }]"
                  >
                    <label>Número Teléfono</label>
                    <md-input
                      v-model="telefono"
                      type="text"
                      @change="sendData('telefono', telefono)"
                    >
                    </md-input>
                  </md-field>
                </ValidationProvider>
              </div>
              <div
                class="md-layout-item md-size-50 md-small-size-100 md-medium-size-100"
              >
                <ValidationProvider
                  name="adicional_tel"
                  rules="numeric|min:10|max:10"
                  v-slot="{ passed, failed }"
                >
                  <md-field
                    :class="[{ 'md-error': failed }, { 'md-valid': passed }]"
                  >
                    <label>Otro Número Teléfono</label>
                    <md-input
                      v-model="adicional_tel"
                      type="text"
                      @change="sendData('adicional_tel', adicional_tel)"
                    >
                    </md-input>
                  </md-field>
                </ValidationProvider>
              </div>
              <div
                class="md-layout-item md-size-100 md-small-size-100 md-medium-size-100"
              >
                <md-field>
                  <label>Usuario Facebook</label>
                  <md-input
                    v-model="redsocial"
                    type="text"
                    @change="sendData('redsocial', redsocial)"
                  >
                  </md-input>
                </md-field>
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
import {
  required,
  email,
  min,
  max,
  min_value,
  max_value,
  numeric
} from "vee-validate/dist/rules";

extend("required", required);
extend("email", email);
extend("min", min);
extend("max", max);
extend("min_value", min_value);
extend("max_value", max_value);
extend("numeric", numeric);

export default {
  data() {
    return {
      nombre: "",
      apellidop: "",
      apellidom: "",
      year: "",
      day: "",
      month: "",
      nacimiento: "",
      sexo: "",
      ce: "",
      cp: "",
      colonia: "",
      seccion: "",
      calle: "",
      numext: "",
      numint: "",
      telefono: "",
      adicional_tel: "",
      redsocial: "",
      entidadeFederativas: [],
      colonias: [],
      secciones: []
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
    },
    getEntidades() {
      let cObject = this;
      axios
        .get(APIURL + "entidades", {
          headers: {
            Authorization: "Bearer " + this.$store.state.sop.authorization.token
          }
        })
        .then(response => {
          cObject.entidadeFederativas = response.data.data;
        })
        .catch(error => {
          cObject.$helpers.catchError(error);
        });
    },
    getColonias() {
      let cObject = this;
      if (this.cp.length < 4) return;
      this.colonia = "";
      this.seccion = "";
      this.secciones = [];
      axios
        .get(APIURL + "colonias/" + this.cp, {
          headers: {
            Authorization: "Bearer " + this.$store.state.sop.authorization.token
          }
        })
        .then(response => {
          cObject.colonias = response.data.data;
        })
        .catch(error => {
          cObject.$helpers.catchError(error);
        });
    },
    getColoniasSecciones() {
      let cObject = this;
      this.seccion = "";
      this.secciones = [];
      axios
        .get(APIURL + "secciones/" + this.cp + "/" + this.colonia, {
          headers: {
            Authorization: "Bearer " + this.$store.state.sop.authorization.token
          }
        })
        .then(response => {
          cObject.secciones = response.data.data;
        })
        .catch(error => {
          cObject.$helpers.catchError(error);
        });
    },
    generaCE() {
      var CE = [];

      CE[0] = this.apellidop.charAt(0).toUpperCase();
      CE[1] = this.apellidop
        .slice(1)
        .replace(/[aeiouAEIOU]/gi, "")
        .charAt(0)
        .toUpperCase();
      CE[2] = this.apellidom.charAt(0).toUpperCase();
      CE[3] = this.apellidom
        .slice(1)
        .replace(/[aeiouAEIOU]/gi, "")
        .charAt(0)
        .toUpperCase();
      CE[4] = this.nombre.charAt(0).toUpperCase();
      CE[5] = this.nombre
        .slice(1)
        .replace(/[aeiouAEIOU]/gi, "")
        .charAt(0)
        .toUpperCase();
      CE[6] = this.year.slice(2, 4);
      CE[7] = this.$helpers.concatenerParametro(this.month);
      CE[8] = this.$helpers.concatenerParametro(this.day);
      CE[9] = this.$helpers.concatenerParametro(this.nacimiento);
      CE[10] = this.sexo.toUpperCase();

      this.ce = CE.join("");
      this.sendData("ce", this.ce);
    }
  },
  created() {
    extend("clve", {
      validate: value =>
        new Promise(resolve => {
          let regex = new RegExp(
            "[A-Z]{6}[0-9]{2}(0[1-9]|1[0-2])(0[1-9]|1[0-9]|2[0-9]|3[0-1])[0-3]|[9]{1}[0-9]{1}[HM]{1}$"
            //"[A-Z]{6}[0-9]{2}(0[1-9]|1[0-2])(0[1-9]|1[0-9]|2[0-9]|3[0-1])[0-3]{1}[0-9]{1}[HM]{1}[0-9]{1}[0-9]{2}$"
          );
          resolve({
            valid: value && regex.test(value)
          });
        })
    });
    this.getEntidades();
  }
};
</script>
<style></style>
