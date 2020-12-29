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
            {{ simpatizantesglobales }}
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
 <div
      class="md-layout-item md-medium-size-100 md-small-size-100 mx-auto"
      :class="entidades.length > 1 ? 'md-size-33' : 'md-size-100'"
    >
      <md-card>
        <md-card-header class="md-card-header-icon md-card-header-green">
          <h4 class="title">Municipio</h4>
        </md-card-header>

        <md-card-content>
          <md-field>
            <label for="municipios">Municipio</label>
            <md-select
              v-model="selmunicipio"
              name="municipio"
              id="municipio"
              @input="selected()"
              disabled
              ><md-option
                v-for="mun in selMunicipios"
                :key="mun.id"
                :value="mun.clave_municipio"
                >{{ mun.nombre }}</md-option
              >
            </md-select>
          </md-field>
        </md-card-content>
      </md-card>
    </div>
    
    <div
      class="md-layout-item md-medium-size-100 md-small-size-100 mx-auto"
      :class="entidades.length > 1 ? 'md-size-33' : 'md-size-100'"
      >
      <md-button class="md-icon-button" @click="previous" >
        <md-icon>keyboard_arrow_left</md-icon>
      </md-button>
       <md-button class="md-icon-button" @click="next">
        <md-icon>keyboard_arrow_right</md-icon>
      </md-button>
      <md-progress-bar md-mode="determinate" :md-value="amount" class="md-success"></md-progress-bar>
      
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
      pageactual:1,
      pages:1,
      selMunicipios:[],
      selmunicipio:0,
      amount:1,
      secciones:[],
      simpatizantesglobales:0,

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
          categories: ["SECCIONES"],
          scrollbar: {
            enabled: true
          },/* 
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
          } */
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
    next:function(){
    let cObject=this;
    if(cObject.pageactual == null){
    cObject.pageactual =1;
    }
    cObject.pageactual=Number(cObject.pageactual)+1;
    cObject.amount=(cObject.pageactual)*(100/cObject.pages);

    if(cObject.pageactual<=cObject.pages){
      location.replace("/graficas?page="+cObject.pageactual+"&municipio="+cObject.selmunicipio+"&id="+this.$store.state.sop.user.idusr)
    }else{
      cObject.pageactual=cObject.pages;
    }
    },
    previous:function(){let cObject=this;
    cObject.pageactual=cObject.pageactual-1;
    cObject.amount=(cObject.pageactual)*(100/cObject.pages);
    if(cObject.pageactual>=1){
      location.replace("/graficas?page="+cObject.pageactual+"&municipio="+cObject.selmunicipio+"&id="+this.$store.state.sop.user.idusr)
    }else{
      cObject.pageactual=1;
    }
    },
    selected:function(){
    let cObject=this;
    cObject.getSelSeccion(cObject.selmunicipio);
    },
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
          cObject.getSelMunicipios();
          cObject.executeSearch();
          cObject.amount=(cObject.pageactual)*(cObject.pages/10);
        })
        .catch(error => {
          cObject.$helpers.catchError(error);
        });
    },
    getMunicipios() {/*
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
          //cObject.chartOptions.xAxis.categories = response.data.data.Municipios;

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
        });*/
    },
    getSelMunicipios(){
      let cObject = this;
      axios
        .get(
          APIURL +
            "get/municipios/" +
            cObject.entidad+"/"+this.$store.state.sop.user.idcandidato ,
          {
            headers: {
              Authorization:
                "Bearer " + this.$store.state.sop.authorization.token
            }
          }
        )
        .then(response => {
          cObject.selMunicipios=response.data.municipios;
          const queryString=window.location.search;
          const urlParams= new URLSearchParams(queryString);
          //const municipio=urlParams.get('municipio');
          let datos=response.data.municipio[0];
          cObject.selmunicipio=datos.clave_municipio;
        })
        .catch(error => {
          cObject.$helpers.catchError(error);
        });

    },
    getSelSeccion(municipio){
      let cObject = this;
      const queryString=window.location.search;
      const urlParams= new URLSearchParams(queryString);
      const page=urlParams.get('page');
      const mun=urlParams.get('municipio');
      if(mun==municipio){
        return;

      }
      axios
        .get(
          APIURL +
            "get/secciones/" + this.entidad+"/"+
             municipio+"/"+ this.$store.state.sop.user.idcandidato,
          {
            params:{
             id:this.$store.state.sop.user.idusr
            },
            headers: {
              Authorization:
                "Bearer " + this.$store.state.sop.authorization.token
            }
          }
        )
        .then(response => {
          cObject.secciones=response.data;
          cObject.chartOptions.xAxis.categories = response.data.secciones;
          cObject.pages=response.data.pages;
          cObject.amount=(page)*(100/cObject.pages);
          cObject.chartOptions.series = [
            {
              name: "No deciden",
              data: response.data.nd
            },
            {
              name: "No nos conocen",
              data: response.data.nnc
            },
            {
              name: "No Simpatizan",
              data: response.data.ns
            },
            {
              name: "Simpatizan",
              data: response.data.s
            }
          ];
          if(response.data.coordinador){
            cObject.simpatizantesglobales=response.data.s[0];
          }else{
            cObject.getSimpatizantes();
          }
        })
        .catch(error => {
          cObject.$helpers.catchError(error);
        });


    },
    executeSearch:function(){
    let cObject = this;
    const queryString=window.location.search;
    const urlParams= new URLSearchParams(queryString);
    const page=urlParams.get('page');
    const municipio=urlParams.get('municipio');
    cObject.pageactual=page;
  
    if(page!=null&&municipio!=null){
      axios
        .get(
          APIURL +
            "get/secciones/" + this.entidad+"/"+
             municipio+"/"+ this.$store.state.sop.user.idcandidato,
          {
            params:{
            id:this.$store.state.sop.user.idusr,page:page
            },
            headers: {
              Authorization:
                "Bearer " + this.$store.state.sop.authorization.token
            }
          }
        )
        .then(response => {
          cObject.secciones=response.data;
          cObject.chartOptions.xAxis.categories = response.data.secciones;
          console.log(response.data);
          cObject.pages=response.data.pages;
          cObject.amount=(page)*(100/cObject.pages);
          cObject.chartOptions.series = [
            {
              name: "No deciden",
              data: response.data.nd
            },
            {
              name: "No nos conocen",
              data: response.data.nnc
            },
            {
              name: "No Simpatizan",
              data: response.data.ns
            },
            {
              name: "Simpatizan",
              data: response.data.s
            }
          ];
           if(response.data.coordinador){
            cObject.simpatizantesglobales=this.data.s;
          }else{
            cObject.getSimpatizantes();
          }
        })
        .catch(error => {
          cObject.$helpers.catchError(error);
        });
    }
    },
     getSimpatizantes: function(){
      let cObject = this;
      axios.get(APIURL+"get/simpatizantes/"+this.$store.state.sop.user.idcandidato).then(response => {
        cObject.simpatizantesglobales = response.data;
      });
    }
  },
  created() {
    window.getDataMunicipio = this.getDataMunicipio;
    window.getName = this.getName;
    this.getEntidades();
  },
  mounted:function(){
  }
};



</script>
<style >
.highcharts-credits {
  display: none;
}
</style>



 