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

    <div class="md-layout-item md-size-100">
      <md-card>
        <md-card-content>
          <md-field style="width: 18%">
            <label for="secciones">Secciónes</label>
            <md-select
              v-model="seccion"
              name="secciones"
              @input="getPoblacion()"
            >
              <md-option
                v-for="item in secciones"
                :key="item.id"
                :value="item.id"
              >
                Sección {{ item.seccion }}
              </md-option>
            </md-select>
          </md-field>
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
              style="margin-top: 15px"
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
    highcharts: Chart,
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
    },
  },
  data() {
    return {
      chartOptions: {
        chart: {
          renderTo: "container",
          type: "column",
        },
        title: {
          text:
            (this.$store.state.sop.user.candidato || "Simaptizantes") +
            " | Población Simpatizantes",
        },
        xAxis: {
          categories: ["SOP"],
          scrollbar: {
            enabled: true,
          },
          labels: {
            useHTML: true,
            formatter: function () {
              return getName(this.value);
            },
          },
        },
        yAxis: {
          allowDecimals: false,
          title: {
            text: "Total Registros Población",
          },
          events: {
            click: function () {
              console.log(this);
            },
          },
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
            data: [0],
          },
          {
            name: "No Simpatizan",
            data: [0],
          },
          {
            name: "No nos conocen",
            data: [0],
          },
          {
            name: "No deciden",
            data: [0],
          },
        ],
      },
      entidades: [],
      secciones: [],
      seccion: null,
      entidad: null,
      categorias: [],

      loader: false,
      currentSort: "nombre",
      currentSortOrder: "asc",
      pagination: {
        perPage: 15,
        currentPage: 1,
        perPageOptions: [5, 10, 15, 25, 50],
        total: 0,
      },
      footerTable: [
        "Nombre",
        "Calle",
        "Número",
        "Colonia",
        "CP",
        "Sección",
        "Acciónes",
      ],
      searchQuery: "",
      propsToSearch: ["nombre", "paterno"],
      tableData: [],
      searchedData: [],
      fuseSearch: null,
    };
  },
  methods: {
    getName: function (item) {
      return this.categorias[item];
    },
    getMunicipios(idEntidad, idMunicipio, page = 1) {
      let cObject = this;
      axios
        .get(
          APIURL +
            "candidato/" +
            this.$store.state.sop.user.idcandidato +
            "/" +
            idEntidad +
            "/grafica/municipios/" +
            idMunicipio +
            "/all" +
            "?page=" +
            page,
          {
            headers: {
              Authorization:
                "Bearer " + this.$store.state.sop.authorization.token,
            },
          }
        )
        .then((response) => {
          cObject.categorias = response.data.data.idMunicipios;
          cObject.chartOptions.xAxis.categories = response.data.data.Municipios;
          cObject.tableData = response.data.data.poblacion[0].data;
          cObject.pagination.total = response.data.data.poblacion[0].total;

          cObject.chartOptions.series = [
            {
              name: "No deciden",
              data: response.data.data.NoDecide,
            },
            {
              name: "No nos conocen",
              data: response.data.data.NoNosConoce,
            },
            {
              name: "No Simpatizan",
              data: response.data.data.NoSimpatiza,
            },
            {
              name: "Simpatizan",
              data: response.data.data.Simpatizantes,
            },
          ];
        })
        .catch((error) => {
          cObject.$helpers.catchError(error);
        });
    },
    getSecciones(idEntidad, idMunicipio) {
      let cObject = this;
      if (idMunicipio == null || idEntidad == null) return;
      axios
        .get(
          APIURL +
            "candidato/" +
            this.$store.state.sop.user.idcandidato +
            "/" +
            idEntidad +
            "/" +
            idMunicipio +
            "/secciones",
          {
            headers: {
              Authorization:
                "Bearer " + this.$store.state.sop.authorization.token,
            },
          }
        )
        .then((response) => {
          cObject.secciones = response.data.data;
        })
        .catch((error) => {
          cObject.$helpers.catchError(error);
        });
    },
    getPoblacion(page = 1) {
      let cObject = this;
      if (this.seccion == null) return;
      this.tableData = [];
      this.searchedData = [];
      this.loader = true;
      axios
        .get(
          APIURL +
            "candidato/" +
            this.$store.state.sop.user.idcandidato +
            "/" +
            this.$route.params.entidad +
            "/" +
            this.$route.params.municipio +
            "/" +
            this.seccion +
            "/graficas/all?page=" +
            page,
          {
            headers: {
              Authorization:
                "Bearer " + this.$store.state.sop.authorization.token,
            },
          }
        )
        .then((response) => {
          cObject.tableData = response.data.data;
          cObject.pagination.total = response.data.total;
          cObject.loader = false;
        })
        .catch((error) => {
          cObject.$helpers.catchError(error);
        });
    },
  },
  created() {
    window.getName = this.getName;
    if (this.$route.params.entidad && this.$route.params.municipio) {
      this.entidad = this.$route.params.entidad;
      this.getMunicipios(
        this.$route.params.entidad,
        this.$route.params.municipio
      );
      this.getSecciones(
        this.$route.params.entidad,
        this.$route.params.municipio
      );
    } else {
      this.$router.go(-1);
    }
  },
  watch: {
    currentPage() {
      this.tableData = [];
      this.searchedData = [];
      this.getMunicipios(
        this.entidad,
        this.$route.params.municipio,
        this.pagination.currentPage
      );
      this.getSecciones(this.entidad, this.$route.params.municipio);
    },
  },
};
</script>
<style>
.highcharts-credits {
  display: none;
}
</style>
