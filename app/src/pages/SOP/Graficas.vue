<template>
  <div class="md-layout">
    <div
      class="md-layout-item md-medium-size-100 md-xsmall-size-100 md-size-25"
    >
      <stats-card header-color="green">
        <template slot="header">
          <div class="card-icon">
            <i class="fa fa-users"></i>
          </div>
          <p class="category">Simpatizantes</p>
          <h3 class="title">
            {{ simpatizan }}
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
    <div
      class="md-layout-item md-medium-size-100 md-xsmall-size-100 md-size-25"
    >
      <stats-card header-color="rose">
        <template slot="header">
          <div class="card-icon">
            <i class="fa fa-users"></i>
          </div>
          <p class="category">No Simpatizan</p>
          <h3 class="title">
            {{ nosimpatizan }}
          </h3>
        </template>
        <template slot="footer">
          <div class="stats">
            <md-icon class="text-default">filter_alt</md-icon>
            <router-link to="/graficas/nosimpatizantes">Ver Detalle</router-link>
          </div>
        </template>
      </stats-card>
    </div>
    <div
      class="md-layout-item md-medium-size-100 md-xsmall-size-100 md-size-25"
    >
      <stats-card header-color="blue">
        <template slot="header">
          <div class="card-icon">
            <i class="fa fa-users"></i>
          </div>
          <p class="category">No nos conocen</p>
          <h3 class="title">
            {{ nonosconoce }}
          </h3>
        </template>
        <template slot="footer">
          <div class="stats">
            <md-icon class="text-default">filter_alt</md-icon>
            <router-link to="/graficas/noconocen">Ver Detalle</router-link>
          </div>
        </template>
      </stats-card>
    </div>
    <div
      class="md-layout-item md-medium-size-100 md-xsmall-size-100 md-size-25"
    >
      <stats-card header-color="default">
        <template slot="header">
          <div class="card-icon">
            <i class="fa fa-users"></i>
          </div>
          <p class="category">No deciden</p>
          <h3 class="title">
            {{ nodeciden }}
          </h3>
        </template>
        <template slot="footer">
          <div class="stats">
            <md-icon class="text-default">filter_alt</md-icon>
            <router-link to="/graficas/nodeciden">Ver Detalle</router-link>
          </div>
        </template>
      </stats-card>
    </div>

    <div class="md-layout-item md-size-100">
      <md-card>
        <md-card-content
          ><highcharts :options="chartOptions"></highcharts> </md-card-content
      ></md-card>
    </div>
  </div>
</template>

<script>
import { APIURL } from "@/api.js";
import axios from "axios";
import { StatsCard } from "@/components";
import { Chart } from "highcharts-vue";

export default {
  components: {
    StatsCard,
    highcharts: Chart
  },
  computed: {
    simpatizan() {
      let data = this.chartOptions.series[3].data;
      return data ? data.reduce((a, b) => a + b, 0) : 0;
    },
    nosimpatizan() {
      let data = this.chartOptions.series[2].data;
      return data ? data.reduce((a, b) => a + b, 0) : 0;
    },
    nonosconoce() {
      let data = this.chartOptions.series[1].data;
      return data ? data.reduce((a, b) => a + b, 0) : 0;
    },
    nodeciden() {
      let data = this.chartOptions.series[0].data;
      return data ? data.reduce((a, b) => a + b, 0) : 0;
    }
  },
  data() {
    return {
      chartOptions: {
        chart: {
          renderTo: "container",
          type: "area",
          panning: true
        },
        title: {
          text:
            (this.$store.state.sop.user.candidato || "Simaptizantes") +
            " | Población Simpatizantes"
        },
        xAxis: {
          categories: ["SOP"],
          max: 6,
          labels: {
            useHTML: true,
            formatter: function() {
              return (
                '<button class="md-button md-success md-simple md-sm" onclick="getDataMunicipio(\'' +
                this.value +
                "')\">" +
                getName(this.value) +
                "</button>"
              );
            }
          }
        },
        yAxis: {
          min: 0,
          allowDecimals: false,
          title: {
            text: "Total Registros Población"
          },
          events: {
            click: function() {
              console.log(this);
            }
          }
        },
        plotOptions: {
          series: {
            stacking: "normal",
            cursor: "pointer",
            dataLabels: {
              enabled: false
            },
            point: {
              events: {
                click: function() {
                  console.log(this.series.userOptions.name, "--");
                }
              }
            }
          }
        },
        series: [
          {
            name: "Simpatizan",
            data: [0]
          },
          {
            name: "No Simpatizan",
            data: [0]
          },
          {
            name: "No nos conocen",
            data: [0]
          },
          {
            name: "No deciden",
            data: [0]
          }
        ]
      },
      entidades: [],
      entidad: null,
      categorias: []
    };
  },
  methods: {
    getDataMunicipio: function(data) {
      console.log(data);
    },
    getName: function(item) {
      return this.categorias[item];
    },
    getEntidades() {
      let cObject = this;
      axios
        .get(
          APIURL +
            "candidato/" +
            this.$store.state.sop.user.idcandidato +
            "/entidades",
          {
            headers: {
              Authorization:
                "Bearer " + this.$store.state.sop.authorization.token
            }
          }
        )
        .then(response => {
          cObject.entidades = response.data.data;
          if (cObject.entidades.length > 0)
            cObject.entidad = cObject.entidades[0].id;
          cObject.getMunicipios();
        })
        .catch(error => {
          console.log(error.message);
        });
    },
    getMunicipios() {
      let cObject = this;
      if (this.entidad == null) return;
      axios
        .get(
          APIURL +
            "candidato/" +
            this.$store.state.sop.user.idcandidato +
            "/" +
            18 +
            "/grafica/municipios/all",
          {
            headers: {
              Authorization:
                "Bearer " + this.$store.state.sop.authorization.token
            }
          }
        )
        .then(response => {
          cObject.categorias = response.data.data.idMunicipios;
          cObject.chartOptions.xAxis.categories = response.data.data.Municipios;

          cObject.chartOptions.series = [
            {
              name: "No deciden",
              data: response.data.data.NoDecide
            },
            {
              name: "No nos conocen",
              data: response.data.data.NoNosConoce
            },
            {
              name: "No Simpatizan",
              data: response.data.data.NoSimpatiza
            },
            {
              name: "Simpatizan",
              data: response.data.data.Simpatizantes
            }
          ];
        })
        .catch(error => {
          console.log(error.message);
        });
    }
  },
  created() {
    window.getDataMunicipio = this.getDataMunicipio;
    window.getName = this.getName;
    this.getEntidades();
  }
};
</script>
<style>
.highcharts-credits {
  display: none;
}
</style>
