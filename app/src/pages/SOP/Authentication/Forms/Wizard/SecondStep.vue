<template>
          
  <div>
    <br>
     <div class="md-layout-item md-size-50 md-small-size-100 mx-auto" v-if="coordinador">
               <md-switch v-model="co_de" 
                @change="sendData('co_de', co_de+'')">¿Es Coordinador de Demarcación? {{(co_de) ? "Si":"No"}}</md-switch>
          </div>
          <div class="md-layout-item md-size-50 md-small-size-100 mx-auto" v-if="co_de">
               <md-field>
            <label for="municipio">Municipios</label>
            <md-select
              v-model="municipio"
              name="municipio"
              id="municipio"
              @input="getDemarcaciones()"
              ><md-option
                v-for="(mun, id) in municipios"
                :key="id"
                :value="mun.clave_municipio"
                >{{ mun.nombre }}</md-option
              >
              
            </md-select>
          </md-field>
            <md-field>
            <label for="demarcaciones">Demarcacion</label>
            <md-select
              v-model="demarcacion"
              name="demarcacion"
              id="demarcacion"
              @input="sendData('demarcacion', demarcacion)"
              ><md-option
                v-for="(dem, id) in demarcaciones"
                :key="id"
                :value="dem.id"
                >demarcacion {{ dem.demarcacion }} secciones {{dem.secciones}}</md-option
              >
              
            </md-select>
          </md-field>
          </div>
    <div v-if="!co_de">
      <h5 class="info-text">
      Selecciona la Entidad Federativa, Municipios, y Distritos requeridos para
      tu Candidatura
    </h5>
    <div class="md-layout">
      <div class="md-layout-item md-size-100">
        <md-button
          class="md-primary md-sm"
          @click="reloadOptions"
          v-if="options == null || options.length < 1"
          ><md-icon>update</md-icon></md-button
        >
        <treeselect
          ref="tree"
          placeholder="Select your categorie(s)..."
          v-model="values"
          :value-consists-of="'LEAF_PRIORITY'"
          :load-options="loadOptions"
          :default-expand-level="0"
          :show-count="true"
          :always-open="true"
          :multiple="false"
          :options="options"
          @input="sendData('values', values)"
        >
        </treeselect>
      </div>
    </div>
    </div>
  </div>
</template>
<script>
import Treeselect from "@riophae/vue-treeselect";
import "@riophae/vue-treeselect/dist/vue-treeselect.css";
import { LOAD_ROOT_OPTIONS } from "@riophae/vue-treeselect";
const sleep = d => new Promise(r => setTimeout(r, d));
let called = false;
import { APIURL } from "@/api.js";
import axios from "axios";
import EventBus from "./bus";

export default {
  components: {
    Treeselect
  },
  data() {
    return {
      municipios:[],
      demarcaciones:[],
      demarcacion:null,
      municipio:null,
      co_de:false,
      values: null,
      options: null,
      id:null,
      coordinador:null,
    };
  },
  methods: {
    reloadOptions() {
      called = false;
      this.$refs.tree.loadRootOptions();
    },
    async loadOptions({ action /*, callback*/ }) {
      let cObject = this;
      if (action === LOAD_ROOT_OPTIONS) {
        if (!called) {
          //await sleep(2000);
          await this.getCategories()
            .then(response => {
              cObject.options = response.data.data;
              called = true;
            })
            .catch(error => {
              throw new Error("Failed to load categories.");
            });
        }
      }
    },
    getCategories() {
      let obj = this;
      EventBus.$on("coordinador",function(item){
        if(item == "false"){
          obj.coordinador=false;
          obj.reloadOptions();
        }
     });
     EventBus.$on("candidato",function(item){
       obj.coordinador=true;
       obj.id=item;
       obj.reloadOptions();
       obj.getMunicipios(item);
     });
      return new Promise((resolve, reject) => {
       
        axios
          .get(APIURL + "registro/entidades", {
            params:{
              coordinador:obj.coordinador, 
              id:obj.id
            }}) 
          .then(response => {
            resolve(response);
          })
          .catch(error => {
            reject(error);
          });
      });
    },
    getMunicipios(id){
      let obj=this;
      axios
          .get(APIURL + "get/municipio/candidato/"+id, {
            params:{
             
            }}) 
          .then(response => {
            this.municipios=response.data;
          })
          .catch(error => {
            reject(error);
          });

    },
    getDemarcaciones(){
      let obj=this;
      obj.demarcacion=null;
      axios
          .get(APIURL + "get/demarcaciones/"+obj.municipio, {
            params:{
             
            }}) 
          .then(response => {
            this.demarcaciones=response.data;
          })
          .catch(error => {
            reject(error);
          });

    },
 getData(data) {
      this.data[data.field] = data.value;
    },
    beforeChange(){
      alert("algo");
    },
    sendData(field, val) {
      this.$emit("data", { field: field, value: val });
    },
    validate() {
      this.$emit("on-validated", true, this.values);
      if(this.co_de){
      return Promise.resolve(true);
      }
      if (this.values == null || this.values.length < 1)
        return Promise.resolve(false);
      return Promise.resolve(true);
    }
  },
  created() {
    setTimeout(() => {
      this.$refs.tree.$el.childNodes[2].childNodes[0].className =
        "vue-treeselect__menu md-scrollbar";
    }, 100);
  }
};
</script>



<style lang="scss">
.vue-treeselect__menu {
  max-height: 200px !important;
  border: 0px !important;
}
.vue-treeselect__control {
  display: none;
}
</style>
    