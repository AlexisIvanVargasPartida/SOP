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
            <router-link to="/graficas/nosimpatizantes"
              >Ver Detalle</router-link
            >
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
          type: "column",
          panning: true
        },
        title: {
          text:
            (this.$store.state.sop.user.candidato || "Simaptizantes") +
            " | Población Simpatizantes"
        },
        xAxis: {
          categories: ["SOP"],
          max: 0,
          scrollbar: {
            enabled: true
          },
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
        tooltip: {
        headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
        pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
            '<td style="padding:0"><b>{point.y} personas</b></td></tr>',
        footerFormat: '</table>',
        shared: true,
        useHTML: true
        },
        plotOptions: {
        column: {
            pointPadding: 0.2,
            borderWidth: 0
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
            name: "No  conocen",
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
      this.$router.push({
        name: "Graficas Municipio",
        params: { municipio: data, entidad: this.entidad }
      });
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
          cObject.$helpers.catchError(error);
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
            this.entidad +
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
          cObject.$helpers.catchError(error);
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
