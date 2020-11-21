// =========================================================
// * SOP - v1.2.0
// =========================================================

import Vue from "vue";
import Vuex from "vuex";
import VueRouter from "vue-router";
import DashboardPlugin from "./material-dashboard";
import App from "./App.vue";
import Chartist from "chartist";
import routes from "./routes/routes";
import axios from "axios";
import VueAxios from "vue-axios";
import StoreData from "./store";
import helpers from "./helpers";
import "./registerServiceWorker";

Vue.use(VueRouter);
Vue.use(DashboardPlugin);
Vue.use(Vuex);
Vue.use(VueAxios, axios);
Vue.use(StoreData);

Vue.prototype.$Chartist = Chartist;
Vue.prototype.$helpers = helpers;

const store = new Vuex.Store(StoreData);
const router = new VueRouter({
  mode: "history",
  routes,
  scrollBehavior: to => {
    if (to.hash) {
      return { selector: to.hash };
    } else {
      return { x: 0, y: 0 };
    }
  },
  linkExactActiveClass: "nav-item active"
});

router.beforeEach((to, from, next) => {
  if (to.matched.some(record => record.meta.requiresAuth)) {
    if (!store.getters.loggedIn) {
      next({ path: "/login" });
    } else {
      if (
        to.matched[1].meta.permission == true ||
        helpers.hasPermision(to.matched[1].meta.permission) == true
      ) {
        next();
      } else {
        next(from);
      }
    }
  } else {
    if (store.getters.loggedIn) {
      next({ name: "Dashboard" });
    } else {
      next();
    }
  }
});

/* eslint-disable no-new */
new Vue({
  el: "#app",
  render: h => h(App),
  router,
  store
});
