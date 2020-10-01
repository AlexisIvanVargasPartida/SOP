<template>
  <div class="md-layout">
    <div
      class="md-layout-item md-size-33 md-medium-size-100 md-small-size-100"
      v-if="entidades.length > 1"
    >
      <md-card>
        <md-card-header class="md-card-header-icon md-card-header-green">
          <h4 class="title">Entidad Federativa</h4>
        </md-card-header>

        <md-card-content>
          <md-field>
            <label for="entidad">Entidad Federativa</label>
            <md-select
              v-model="entidad"
              name="entidad"
              id="entidad"
              @input="getMunicipios"
            >
              <md-option
                v-for="(ef, id) in entidades"
                :key="id"
                :value="ef.id"
                >{{ ef.nombre }}</md-option
              >
            </md-select>
          </md-field>
        </md-card-content>
      </md-card>
    </div>
    <div
      class="md-layout-item md-size-33 md-medium-size-100 md-small-size-100 mx-auto"
    >
      <md-card>
        <md-card-header class="md-card-header-icon md-card-header-green">
          <h4 class="title">Municipio</h4>
        </md-card-header>

        <md-card-content>
          <md-field>
            <label for="municipios">Municipio</label>
            <md-select
              v-model="municipio"
              name="municipio"
              id="municipio"
              @input="getSecciones"
              ><md-option
                v-for="(mun, id) in municipios"
                :key="id"
                :value="mun.id"
                >{{ mun.nombre }}</md-option
              >
            </md-select>
          </md-field>
        </md-card-content>
      </md-card>
    </div>
    <div
      class="md-layout-item md-size-33 md-medium-size-100 md-small-size-100 mx-auto"
    >
      <md-card>
        <md-card-header class="md-card-header-icon md-card-header-green">
          <h4 class="title">Sección</h4>
        </md-card-header>

        <md-card-content>
          <md-field>
            <label for="secciones">Sección</label>
            <md-select
              v-model="seccion"
              name="seccion"
              id="seccion"
              @input="getPoblacion(1)"
              ><md-option v-for="(se, id) in secciones" :key="id" :value="se.id"
                >Sección {{ se.seccion }}</md-option
              >
            </md-select>
          </md-field>
        </md-card-content>
      </md-card>
    </div>
    <div class="md-layout-item md-size-100">
      <md-card>
        <md-card-content>
          <md-table
            :value="queriedData"
            :md-sort.sync="currentSort"
            :md-sort-order.sync="currentSortOrder"
            :md-sort-fn="customSort"
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

              <md-field>
                <md-input
                  type="search"
                  class="mb-3"
                  clearable
                  style="width: 200px"
                  placeholder="Search records"
                  v-model="searchQuery"
                >
                </md-input>
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
              <md-table-cell md-label="Calle" md-sort-by="email">{{
                item.calle
              }}</md-table-cell>
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
              <md-table-cell md-label="Acciónes">
                <md-button
                  class="md-just-icon md-info md-simple"
                  @click.native="handleLike(item)"
                >
                  <md-icon>favorite</md-icon>
                </md-button>
              </md-table-cell>
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
import Fuse from "fuse.js";
import Swal from "sweetalert2";

export default {
  components: {
    Pagination
  },
  computed: {
    /***
     * Returns a page from the searched data or the whole data. Search is performed in the watch section below
     */
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
      fuseSearch: null,
      entidad: null,
      municipio: null,
      seccion: null,
      entidades: [],
      municipios: [],
      secciones: [],
      loader: false
    };
  },
  methods: {
    customSort(value) {
      return value.sort((a, b) => {
        const sortBy = this.currentSort;
        if (this.currentSortOrder === "desc") {
          return a[sortBy].localeCompare(b[sortBy]);
        }
        return b[sortBy].localeCompare(a[sortBy]);
      });
    },
    handleLike(item) {
      Swal.fire({
        title: `Simpatiza ${item.nombre}`,
        buttonsStyling: false,
        type: "success",
        confirmButtonClass: "md-button md-success"
      });
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
            "/municipios",
          {
            headers: {
              Authorization:
                "Bearer " + this.$store.state.sop.authorization.token
            }
          }
        )
        .then(response => {
          cObject.municipios = response.data.data;
          cObject.municipios.length == 1
            ? (cObject.municipio = cObject.municipios[0].id)
            : null;
          cObject.getSecciones();
        })
        .catch(error => {
          console.log(error.message);
        });
    },
    getSecciones() {
      let cObject = this;
      if (this.municipio == null) return;
      axios
        .get(
          APIURL +
            "candidato/" +
            this.$store.state.sop.user.idcandidato +
            "/" +
            this.entidad +
            "/" +
            this.municipio +
            "/secciones",
          {
            headers: {
              Authorization:
                "Bearer " + this.$store.state.sop.authorization.token
            }
          }
        )
        .then(response => {
          cObject.secciones = response.data.data;
        })
        .catch(error => {
          console.log(error.message);
        });
    },
    getPoblacion(page = 1) {
      let cObject = this;
      this.loader = true;
      if (this.seccion == null) return;
      axios
        .get(
          APIURL +
            "candidato/" +
            this.$store.state.sop.user.idcandidato +
            "/" +
            this.entidad +
            "/" +
            this.municipio +
            "/" +
            this.seccion +
            "/poblacion?page=" +
            page,
          {
            headers: {
              Authorization:
                "Bearer " + this.$store.state.sop.authorization.token
            }
          }
        )
        .then(response => {
          cObject.tableData = response.data.data.data;
          cObject.pagination.total = response.data.data.total;
          cObject.loader = false;
        })
        .catch(error => {
          console.log(error.message);
        });
    }
  },
  created() {
    this.getEntidades();
  },
  mounted() {
    // Fuse search initialization.
    this.fuseSearch = new Fuse(this.tableData, {
      keys: ["nombre", "paterno"],
      threshold: 0.3
    });
  },
  watch: {
    /**
     * Searches through the table data by a given query.
     * NOTE: If you have a lot of data, it's recommended to do the search on the Server Side and only display the results here.
     * @param value of the query
     */
    searchQuery(value) {
      let result = this.tableData;
      if (value !== "") {
        result = this.fuseSearch.search(this.searchQuery);
      }
      this.searchedData = result;
    },
    currentPage() {
      this.tableData = [];
      this.searchedData = [];
      console.log("---", this.pagination.currentPage);
      this.getPoblacion(this.pagination.currentPage);
    }
  }
};
</script>

<style lang="css" scoped>
.md-card .md-card-actions {
  border: 0;
  margin-left: 20px;
  margin-right: 20px;
}
</style>
