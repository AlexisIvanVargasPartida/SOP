<template>
  <div class="md-layout">
    <div
      class="md-layout-item md-medium-size-100 md-xsmall-size-100 md-size-33"
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
      </stats-card>
    </div>
    <div
      class="md-layout-item md-medium-size-100 md-xsmall-size-100 md-size-33"
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
      </stats-card>
    </div>
    <div
      class="md-layout-item md-medium-size-100 md-xsmall-size-100 md-size-33"
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
      </stats-card>
    </div>

    <div class="md-layout-item md-size-100">
      <md-card>
        <md-card-content>
          <apexchart
            type="bar"
            height="550"
            :options="chartOptions"
            :series="series"
          ></apexchart> </md-card-content
      ></md-card>
    </div>
  </div>
</template>

<script>
import { APIURL } from "@/api.js";
import axios from "axios";
import { StatsCard } from "@/components";

export default {
  components: {
    StatsCard
  },
  computed: {
    simpatizan() {
      return this.series[0].data.reduce((a, b) => a + b, 0);
    },
    nosimpatizan() {
      return this.series[1].data.reduce((a, b) => a + b, 0);
    },
    nonosconoce() {
      return this.series[2].data.reduce((a, b) => a + b, 0);
    }
  },
  data() {
    return {
      entidades: [],
      entidad: null,
      series: [
        {
          name: "Simpatizan",
          data: []
        },
        {
          name: "No Simpatizan",
          data: []
        },
        {
          name: "No nos conocen",
          data: []
        }
      ],
      chartOptions: {
        chart: {
          type: "bar",
          height: 350,
          stacked: true,
          zoom: {
            enabled: true
          },
          events: {
            dataPointSelection: function(event, chartContext, opts) {
              //console.log(opts.w.config.xaxis.categories[opts.dataPointIndex]);
              //generar ruta para grafica
              console.log(opts.w.config.xaxis.claves[opts.dataPointIndex]);
            }
          }
        },
        plotOptions: {
          bar: {
            horizontal: true
          }
        },
        colors: ["#60b664", "#e83874", "#14bace"],
        stroke: {
          width: 1,
          colors: ["#fff"]
        },
        title: {
          text: "Población - Simpatizantes"
        },
        xaxis: {
          categories: [],
          tickPlacement: "on",
          labels: {
            formatter: function(val) {
              return val.toFixed(1);
            }
          }
        },
        yaxis: {
          title: {
            text: undefined
          }
        },
        tooltip: {
          y: {
            formatter: function(val) {
              return val + "";
            }
          }
        },
        fill: {
          opacity: 1
        },
        legend: {
          position: "top",
          horizontalAlign: "left",
          offsetX: 40
        },
        noData: {
          text: "Loading..."
        }
      }
    };
  },
  methods: {
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
          cObject.entidades.length > 0
            ? (cObject.entidad = cObject.entidades[0].id)
            : null;
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
            this.entidad +
            "/grafica/municipios",
          {
            headers: {
              Authorization:
                "Bearer " + this.$store.state.sop.authorization.token
            }
          }
        )
        .then(response => {
          cObject.chartOptions = {
            xaxis: {
              categories: response.data.data.Municipios,
              claves: response.data.data.Claves
            }
          };

          cObject.series = [
            {
              name: "Simpatizan",
              data: response.data.data.Simpatizantes
            },
            {
              name: "No Simpatizan",
              data: response.data.data.NoSimpatiza
            },
            {
              name: "No nos conocen",
              data: response.data.data.NoNosConoce
            }
          ];
        })
        .catch(error => {
          console.log(error.message);
        });
    }
  },
  created() {
    this.getEntidades();
  }
};
</script>
