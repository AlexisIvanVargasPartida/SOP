<template>
  <div class="md-layout">
    <div class="md-layout-item md-medium-size-50 md-xsmall-size-100 md-size-25">
      <stats-card header-color="green">
        <template slot="header">
          <div class="card-icon">
            <i class="fa fa-user"></i>
          </div>
          <p class="category">Total Simpatizantes</p>
          <h3 class="title">
            {{ simpatizantes }}
          </h3>
        </template>
         <template slot="footer">
          <div class="stats">
            <md-icon class="text-default">filter_alt</md-icon>
            <router-link to="/graficas/simpatizantes">Ver Detalle</router-link>
          </div>
        </template>
      </stats-card>
    </div>
    <div class="md-layout-item md-medium-size-50 md-xsmall-size-100 md-size-25">
      <stats-card header-color="rose">
        <template slot="header">
          <div class="card-icon">
            <i class="fa fa-users"></i>
          </div>
          <p class="category">Población</p>
          <h3 class="title">
           {{ poblacion }}
          </h3>
        </template>
        <template slot="footer">
          <div class="stats">
            <md-icon class="text-default">filter_alt</md-icon>
            <router-link to="/poblacion"
              >Ver Detalle</router-link
            >
          </div>
        </template>
      </stats-card>
    </div>
    <div class="md-layout-item md-medium-size-50 md-xsmall-size-100 md-size-25">
      <stats-card header-color="blue">
        <template slot="header">
          <div class="card-icon">
            <i class="fa fa-cogs"></i>
          </div>
          <p class="category">Total Gestiones</p>
          <h3 class="title">
            <animated-number :value="0"></animated-number>
          </h3>
        </template>
        <template slot="footer">
          <div class="stats">
            <md-icon class="text-default">filter_alt</md-icon>
            <router-link to="/.../totalgestiones"
              >Ver Detalle</router-link
            >
          </div>
        </template>
      </stats-card>
    </div>
    <div class="md-layout-item md-medium-size-50 md-xsmall-size-100 md-size-25">
      <stats-card header-color="warning">
        <template slot="header">
          <div class="card-icon">
            <i class="fa fa-dollar-sign"></i>
          </div>
          <p class="category">Total Gestionado</p>
          <h3 class="title">
            <animated-number :value="0"></animated-number>
          </h3>
        </template>
        <template slot="footer">
          <div class="stats">
            <md-icon class="text-default">filter_alt</md-icon>
            <router-link to="/.../gestionado"
              >Ver Detalle</router-link
            >
          </div>
        </template>
      </stats-card>
    </div>
  </div>
</template>

<script>
import { StatsCard, ChartCard, AnimatedNumber } from "@/components";
import { APIURL } from "@/api.js";
import axios from "axios";

export default {
  components: {
    StatsCard,
    AnimatedNumber
  },
  data() {
    return {poblacion: 0,simpatizantes:0};
  
  },
  created: function() {
    this.getSimpatizantes();
    this.getPoblacion();
  },
  methods: {
    getPoblacion: function(){
      let cObject = this;
      axios.get(APIURL+"get/poblacion/"+this.$store.state.sop.user.idcandidato).then(response => {
        cObject.poblacion = response.data;
      });
    },
    getSimpatizantes: function(){
      let cObject = this;
      axios.get(APIURL+"get/simpatizantes/"+this.$store.state.sop.user.idcandidato).then(response => {
        cObject.simpatizantes = response.data;
      });
    }
  },
  
};
</script>
