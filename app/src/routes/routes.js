import DashboardLayout from "@/pages/SOP/Layout/DashboardLayout.vue";
import AuthLayout from "@/pages/SOP/Layout/AuthLayout.vue";

// Dashboard pages
import Dashboard from "@/pages/SOP/Dashboard.vue";
import RegistroPoblacion from "@/pages/SOP/RegistroPoblacion.vue";
import Poblacion from "@/pages/SOP/Poblacion.vue";

import Login from "@/pages/SOP/Authentication/Login.vue";
import Register from "@/pages/SOP/Authentication/Register.vue";

let authPages = {
  path: "/",
  component: AuthLayout,
  name: "Authentication",
  children: [
    {
      path: "/login",
      name: "Login",
      component: Login,
      meta: {
        requiresAuth: false,
        title: "SOP | Inicio Sesión"
      }
    },
    {
      path: "/registro",
      name: "Registro",
      component: Register,
      meta: {
        requiresAuth: false,
        title: "SOP | Registro"
      }
    }
  ]
};

const routes = [
  {
    path: "/",
    redirect: "/dashboard",
    name: "Home"
  },
  authPages,
  {
    path: "/",
    component: DashboardLayout,
    children: [
      {
        path: "dashboard",
        name: "Dashboard",
        component: Dashboard,
        meta: {
          requiresAuth: true,
          title: "SOP | Tablero",
          permission: true
        }
      },
      {
        path: "poblacion/registro",
        name: "Registro Poblacion",
        component: RegistroPoblacion,
        meta: {
          requiresAuth: true,
          title: "SOP | Registro Poblacion",
          permission: true
        }
      },
      {
        path: "poblacion",
        name: "Poblacion",
        component: Poblacion,
        meta: {
          requiresAuth: true,
          title: "SOP | Población",
          permission: true
        }
      }
    ]
  },
  {
    path: "*",
    redirect: "/dashboard"
  }
];

export default routes;
