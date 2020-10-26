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
          <p class="category">{{ filtroName }}</p>
          <h3 class="title">
            {{ totalSimpatizantes }}
          </h3>
        </template>
        <template slot="footer">
          <div class="stats">
            <md-icon class="text-default">filter_alt</md-icon>
            <router-link to="/graficas">Volver a datos generales</router-link>
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

    <div class="md-layout-item md-size-100">
      <md-card>
        <md-card-content>
          <md-table
            :value="queriedData"
            class="paginated-table table-striped table-hover"
          >
            <md-table-toolbar>
              <md-field>
                <label for="pages">Per page</label>
                <md-select v-model="pagination.perPage" name="pages">
                  <md-option
                    v-for="item in pagination.perPageOptions"
                    :key="item"
                    :label="item"
                    :value="item"
                  >
                    {{ item }}
                  </md-option>
                </md-select>
              </md-field>
            </md-table-toolbar>

            <md-progress-bar
              md-mode="indeterminate"
              v-if="loader"
              style="margin-top:15px"
            ></md-progress-bar>
            <md-table-row slot="md-table-row" slot-scope="{ item }">
              <md-table-cell md-label="Nombre" md-sort-by="name"
                >{{ item.nombre }} {{ item.paterno }}
                {{ item.materno }}</md-table-cell
              >
              <md-table-cell md-label="Calle">{{ item.calle }}</md-table-cell>
              <md-table-cell md-label="Número">{{
                item.num_ext
              }}</md-table-cell>
              <md-table-cell md-label="Colonia">{{
                item.colonia
              }}</md-table-cell>
              <md-table-cell md-label="CP">{{ item.cp }}</md-table-cell>
              <md-table-cell md-label="Sección">{{
                item.seccion
              }}</md-table-cell>
              <md-table-cell md-label="Facebook">{{
                JSON.parse(item.data).redsocial
              }}</md-table-cell>
              <md-table-cell md-label="Telefono">{{
                JSON.parse(item.data).telefonos.length > 0
                  ? JSON.parse(item.data).telefonos[0]
                  : ""
              }}</md-table-cell>
              <md-table-cell md-label="Fecha">{{
                item.fechacaptura
              }}</md-table-cell>
            </md-table-row>
          </md-table>
          <div class="footer-table md-table" v-if="queriedData.length > 0">
            <table>
              <tfoot>
                <tr>
                  <th
                    v-for="item in footerTable"
                    :key="item.name"
                    class="md-table-head"
                  >
                    <div class="md-table-head-container md-ripple md-disabled">
                      <div class="md-table-head-label">
                        {{ item }}
                      </div>
                    </div>
                  </th>
                </tr>
              </tfoot>
            </table>
          </div>
        </md-card-content>
        <md-card-actions md-alignment="space-between">
          <div class="">
            <p class="card-category">
              Showing {{ from + 1 }} to {{ to }} of {{ total }} entries
            </p>
          </div>
          <pagination
            class="pagination-no-border pagination-success"
            v-model="pagination.currentPage"
            :per-page="pagination.perPage"
            :total="total"
          >
          </pagination>
        </md-card-actions>
      </md-card>
    </div>
  </div>
</template>

<script>
import { APIURL } from "@/api.js";
import axios from "axios";
import { Pagination } from "@/components";
import { StatsCard } from "@/components";
import { Chart } from "highcharts-vue";

export default {
  components: {
    StatsCard,
    Pagination,
    highcharts: Chart
  },
  computed: {
    totalSimpatizantes() {
      let data = this.chartOptions.series[0].data;
      return data ? data.reduce((a, b) => a + b, 0) : 0;
    },
    queriedData() {
      let result = this.tableData;
      if (this.searchedData.length > 0) {
        result = this.searchedData;
        return result.slice(this.from, this.to);
      }
      return result;
    },
    to() {
      let highBound = this.from + this.pagination.perPage;
      if (this.total < highBound) {
        highBound = this.total;
      }
      return highBound;
    },
    from() {
      return this.pagination.perPage * (this.pagination.currentPage - 1);
    },
    total() {
      return this.searchedData.length > 0
        ? this.searchedData.length
        : this.pagination.total;
    },
    currentPage() {
      return this.pagination.currentPage;
    }
  },
  data() {
    return {
      filtroName: "SOP",
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
            name: "SOP",
            data: [0]
          }
        ]
      },
      entidades: [],
      entidad: null,
      categorias: [],

      loader: false,
      currentSort: "nombre",
      currentSortOrder: "asc",
      pagination: {
        perPage: 10,
        currentPage: 1,
        perPageOptions: [5, 10, 25, 50],
        total: 0
      },
      footerTable: [
        "Nombre",
        "Calle",
        "Número",
        "Colonia",
        "CP",
        "Sección",
        "Acciónes"
      ],
      searchQuery: "",
      propsToSearch: ["nombre", "paterno"],
      tableData: [],
      searchedData: [],
      fuseSearch: null
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
          cObject.getMunicipios(cObject.entidad);
        })
        .catch(error => {
          cObject.$helpers.catchError(error);
        });
    },
    getMunicipios(idEntidad, page = 1) {
      let cObject = this;
      if (idEntidad == null) return;
      this.loader = true;
      axios
        .get(
          APIURL +
            "candidato/" +
            this.$store.state.sop.user.idcandidato +
            "/" +
            idEntidad +
            "/grafica/municipios/" +
            this.$route.params.filtro +
            "?page=" +
            page,
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
          cObject.tableData = response.data.data.poblacion[0].data;
          cObject.pagination.total = response.data.data.poblacion[0].total;

          cObject.chartOptions.series = [
            {
              name: cObject.filtroName,
              data: response.data.data.filter
            }
          ];
          cObject.loader = false;
        })
        .catch(error => {
          cObject.$helpers.catchError(error);
        });
    }
  },
  created() {
    switch (this.$route.params.filtro) {
      case "simpatizantes":
        this.filtroName = "Simpatizantes";
        break;
      case "nosimpatizantes":
        this.filtroName = "No Simpatizan";
        break;
      case "noconocen":
        this.filtroName = "No nos conocen";
        break;
      case "nodeciden":
        this.filtroName = "No deciden";
        break;
      default:
        this.$router.go(-1);
        break;
    }
    window.getDataMunicipio = this.getDataMunicipio;
    window.getName = this.getName;
    this.getEntidades();
  },
  watch: {
    currentPage() {
      this.tableData = [];
      this.searchedData = [];
      this.getMunicipios(this.entidad, this.pagination.currentPage);
    }
  }
};
</script>
<style>
.highcharts-credits {
  display: none;
}
</style>
