// initial state
import { APIURL } from "@/api.js";
import axios from "axios";

let configStorage = {
  authorization: {
    token_type: null,
    token: null,
    permissions: ""
  },
  user: {
    name: null,
    rol: null,
    idcandidato: null,
    candidato: null
  }
};

export default {
  state: {
    sop:
      localStorage.DataSOP == null
        ? configStorage
        : JSON.parse(localStorage.DataSOP)
  },
  getters: {
    loggedIn(state) {
      return state.sop.authorization.token !== null;
    }
  },
  mutations: {
    retrieveFetchup(state, sop) {
      state.sop = sop;
    }
  },
  actions: {
    retrieveFetchup(context, credentials) {
      localStorage.setItem("DataSOP", JSON.stringify(configStorage));
      let data = JSON.parse(localStorage.DataSOP);

      return new Promise((resolve, reject) => {
        axios
          .post(APIURL + "login", {
            email: credentials.email,
            password: credentials.password
          })
          .then(response => {
            data.authorization.token_type = response.data.token_type;
            data.authorization.token = response.data.access_token;
            data.authorization.permissions = response.data.permissions;
            let dataUser = {
              name: response.data.user.nombre,
              rol: response.data.user.rol,
              idcandidato: response.data.user.idcandidato,
              candidato: response.data.user.candidato
            };
            data.user = dataUser;
            localStorage.setItem("DataSOP", JSON.stringify(data));
            context.commit("retrieveFetchup", data);
            resolve(response);
          })
          .catch(error => {
            reject(error);
          });
      });
    },
    destroyFetchup(context) {
      localStorage.clear();
      if (context.getters.loggedIn) {
        return new Promise((resolve, reject) => {
          axios
            .get(APIURL + "logout", {
              headers: {
                Authorization: "Bearer " + context.state.sop.authorization.token
              }
            })
            .then(response => {
              resolve(response);
              context.commit("retrieveFetchup", configStorage);
            })
            .catch(error => {
              reject(error);
              context.commit("retrieveFetchup", configStorage);
            });
        });
      }
    }
  }
};
