<template>
  <div>
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
          :multiple="true"
          :options="options"
          @input="sendData('values', values)"
        >
        </treeselect>
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
    