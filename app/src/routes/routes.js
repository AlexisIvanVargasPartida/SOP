import DashboardLayout from "@/pages/SOP/Layout/DashboardLayout.vue";
import AuthLayout from "@/pages/SOP/Layout/AuthLayout.vue";

// Dashboard pages
import Dashboard from "@/pages/SOP/Dashboard.vue";
import RegistroPoblacion from "@/pages/SOP/RegistroPoblacion.vue";
import Poblacion from "@/pages/SOP/Poblacion.vue";
import Graficas from "@/pages/SOP/Graficas.vue";
import GraficasMunicipio from "@/pages/SOP/GraficasMunicipio.vue";
import GraficasFiltro from "@/pages/SOP/GraficasFiltro.vue";

import Login from "@/pages/SOP/Authentication/Login.vue";
import Register from "@/pages/SOP/Authentication/Register.vue";
import RecuperarContrasena from "@/pages/SOP/Authentication/RecuperarContrasena.vue";
import CambiarContrasena from "@/pages/SOP/Authentication/CambiarContrasena.vue";

let authPages = {
    path: "/",
    component: AuthLayout,
    name: "Authentication",
    children: [{
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
        },
        {
            path: "/recuperar-contrasena",
            name: "Recuperar Contrasena",
            component: RecuperarContrasena,
            meta: {
                requiresAuth: false,
                title: "SOP | Recuperar Contraseña"
            }
        },
        {
            path: "/cambiar-contrasena/:token",
            name: "Cambio Contrasena",
            component: CambiarContrasena,
            meta: {
                requiresAuth: false,
                title: "SOP | Cambiar Contraseña"
            }
        }
    ]
};

const routes = [{
        path: "/",
        redirect: "/dashboard",
        name: "Home"
    },
    authPages,
    {
        path: "/",
        component: DashboardLayout,
        children: [{
                path: "/dashboard",
                name: "Dashboard",
                component: Dashboard,
                meta: {
                    requiresAuth: true,
                    title: "SOP | Tablero",
                    permission: true
                }
            },
            {
                path: "/poblacion/registro",
                name: "Registro Poblacion",
                component: RegistroPoblacion,
                meta: {
                    requiresAuth: true,
                    title: "SOP | Registro Poblacion",
                    permission: true
                }
            },
            {
                path: "/poblacion",
                name: "Poblacion",
                component: Poblacion,
                meta: {
                    requiresAuth: true,
                    title: "SOP | Población",
                    permission: true
                }
            },
            {
                path: "/graficas",
                name: "Graficas",
                component: Graficas,
                meta: {
                    requiresAuth: true,
                    title: "SOP | Graficas",
                    permission: true
                }
            },
            {
                path: "/graficas/:filtro",
                name: "Graficas Filtro",
                component: GraficasFiltro,
                meta: {
                    requiresAuth: true,
                    title: "SOP | Graficas Filtro",
                    permission: true
                }
            },
            {
                path: "/graficas/municipio/:municipio",
                name: "Graficas Municipio",
                component: GraficasMunicipio,
                meta: {
                    requiresAuth: true,
                    title: "SOP | Graficas Municipio",
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