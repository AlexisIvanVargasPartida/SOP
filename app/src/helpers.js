import StoreData from "./store";
import Vue from "vue";
import Vuex from "vuex";
Vue.use(Vuex);
Vue.use(StoreData);

const store = new Vuex.Store(StoreData);

export default {
  hasPermision(permission) {
    let response =
      store.state.sop.authorization.permissions.indexOf(permission) >= 0
        ? true
        : false;
    return response;
  },
  concatenerParametro(data, longitud = 2, concatena = "0") {
    let dataReturn = data.toString();
    if (dataReturn.length < longitud) {
      dataReturn = concatena + dataReturn;
    }
    return dataReturn;
  },
  formatoMoneda(value, params = "$1,") {
    let newValue = parseFloat(value.toString().replace(/[^\d\.]/g, ""));
    if (isNaN(newValue)) {
      newValue = 0;
    }
    return newValue.toFixed(2).replace(/(\d)(?=(\d{3})+(?:\.\d+)?$)/g, params);
  },
  primeraMayuscula(string) {
    var value = string.split(" ");
    var retorna = "";
    for (let index = 0; index < value.length; index++) {
      if (index < value.length - 1) {
        retorna +=
          value[index].charAt(0).toUpperCase() +
          value[index].slice(1).toLowerCase() +
          " ";
      } else {
        retorna +=
          value[index].charAt(0).toUpperCase() +
          value[index].slice(1).toLowerCase();
      }
    }
    return retorna;
  },
  todasMinusculas(string) {
    var retorna = string.toLowerCase();
    retorna = retorna.replace(/ /g, "");
    return retorna;
  },
  todasMayusculas(string) {
    var retorna = string.toUpperCase();
    return retorna;
  },
  getLocalStorageSize() {
    var total = 0;
    for (var x in localStorage) {
      // Value is multiplied by 2 due to data being stored in `utf-16` format, which requires twice the space.
      var amount = (localStorage[x].length * 1) / 1024 / 1024;
      if (!isNaN(amount) && localStorage.hasOwnProperty(x)) {
        //console.log(x, localStorage.getItem(x), amount);
        total += amount;
      }
    }
    return total.toFixed(2);
  },
  setBytes(n) {
    return new Array(n * 1024 + 1).join(".");
  },
  getLocalStorageSizeLimit() {
    var i = 0;
    var total = 0;
    console.log("Size localStorage Before: " + this.getLocalStorageSize());
    try {
      // Test up to 10 MB
      for (i = 0; i <= 10000; i += 250) {
        localStorage.setItem("localLimit", this.setBytes(i));
      }
    } catch (e) {
      let getLocal = localStorage.getItem("localLimit");
      //console.log(getLocal);
      console.log("Size localStorage After: " + this.getLocalStorageSize());
      localStorage.removeItem("localLimit");
      //total = i ? i - 500 : 0;
    }
  }
};
