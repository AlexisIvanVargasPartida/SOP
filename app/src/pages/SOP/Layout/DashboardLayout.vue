<template>
  <div class="wrapper" :class="[{ 'nav-open': $sidebar.showSidebar }]">
    <notifications></notifications>
    <side-bar
      :active-color="sidebarBackground"
      :background-image="sidebarBackgroundImage"
      :data-background-color="sidebarBackgroundColor"
    >
      <user-menu></user-menu>
      <!--<mobile-menu></mobile-menu>-->
      <template slot="links">
        <sidebar-item
          :link="{
            name: 'Tablero',
            icon: 'dashboard',
            path: '/dashboard',
            view: true,
          }"
        >
        </sidebar-item>
        <sidebar-item
          :link="{ name: 'Simpatizantes', icon: 'person_add', view: true }"
        >
          <sidebar-item
            :link="{
              name: 'Registro Población',
              path: '/poblacion/registro',
              view: true,
            }"
          ></sidebar-item>
          <sidebar-item
            :link="{ name: 'Población', path: '/poblacion', view: true }"
          ></sidebar-item>
          <sidebar-item
            :link="{ name: 'Graficas', path: '/graficas', view: true }"
          ></sidebar-item>
        </sidebar-item>
        <sidebar-item
          :link="{
            name: 'Organigrama',
            icon: 'filter_list',
            path: '/organigrama',
          }"
        ></sidebar-item>
        <sidebar-item
          :link="{
            name: 'Gestioria',
            icon: 'attach_money',
            path: '/gestioria',
          }"
        ></sidebar-item>
        <sidebar-item
          :link="{
            name: 'Encuesta',
            icon: 'content_paste',
            path: '/encuestas',
          }"
        ></sidebar-item>
        <sidebar-item
          :link="{ name: 'Usuarios', icon: 'group', path: '/usuarios' }"
        ></sidebar-item>
      </template>
    </side-bar>
    <div class="main-panel">
      <top-navbar></top-navbar>

      <fixed-plugin
        v-if="1 == 2"
        :color.sync="sidebarBackground"
        :colorBg.sync="sidebarBackgroundColor"
        :sidebarMini.sync="sidebarMini"
        :sidebarImg.sync="sidebarImg"
        :image.sync="sidebarBackgroundImage"
      >
      </fixed-plugin>
      <div v-if="!$store.state.status" class="offline">
        <a><md-icon style="margin-top: 13px">wifi_off</md-icon></a>
      </div>
      <div
        :class="{ content: !$route.meta.hideContent }"
        @click="toggleSidebar"
        style="padding-top: 70px"
      >
        <zoom-center-transition :duration="200" mode="out-in">
          <!-- your content here -->
          <router-view></router-view>
        </zoom-center-transition>
      </div>
      <content-footer v-if="$route.meta.footer"></content-footer>
    </div>
  </div>
</template>
<script>
/* eslint-disable no-new */
import PerfectScrollbar from "perfect-scrollbar";
import "perfect-scrollbar/css/perfect-scrollbar.css";

function hasElement(className) {
  return document.getElementsByClassName(className).length > 0;
}

function initScrollbar(className) {
  if (hasElement(className)) {
    new PerfectScrollbar(`.${className}`);
    document.getElementsByClassName(className)[0].scrollTop = 0;
  } else {
    // try to init it later in case this component is loaded async
    setTimeout(() => {
      initScrollbar(className);
    }, 100);
  }
}

function reinitScrollbar() {
  let docClasses = document.body.classList;
  let isWindows = navigator.platform.startsWith("Win");
  if (isWindows) {
    // if we are on windows OS we activate the perfectScrollbar function
    initScrollbar("sidebar");
    initScrollbar("sidebar-wrapper");
    initScrollbar("main-panel");

    docClasses.add("perfect-scrollbar-on");
  } else {
    docClasses.add("perfect-scrollbar-off");
  }
}

import TopNavbar from "./TopNavbar.vue";
import ContentFooter from "./ContentFooter.vue";
import MobileMenu from "./Extra/MobileMenu.vue";
import FixedPlugin from "../../FixedPlugin.vue";
import UserMenu from "./Extra/UserMenu.vue";
import { ZoomCenterTransition } from "vue2-transitions";

export default {
  components: {
    TopNavbar,
    ContentFooter,
    FixedPlugin,
    //MobileMenu,
    UserMenu,
    ZoomCenterTransition,
  },
  data() {
    return {
      sidebarBackgroundColor: "white",
      sidebarBackground: "green",
      sidebarBackgroundImage: "../../img/sidebar-2.jpg",
      sidebarMini: true,
      sidebarImg: false,
    };
  },
  methods: {
    toggleSidebar() {
      if (this.$sidebar.showSidebar) {
        this.$sidebar.displaySidebar(false);
      }
    },
    minimizeSidebar() {
      if (this.$sidebar) {
        this.$sidebar.toggleMinimize();
      }
    },
  },
  created() {
    window.addEventListener(
      "offline",
      () => (this.$store.state.status = false)
    );
    window.addEventListener("online", () => (this.$store.state.status = true));
  },
  updated() {
    reinitScrollbar();
  },
  mounted() {
    reinitScrollbar();
  },
  watch: {
    sidebarMini() {
      this.minimizeSidebar();
    },
  },
};
</script>
<style lang="scss">
$scaleSize: 0.95;
@keyframes zoomIn95 {
  from {
    opacity: 0;
    transform: scale3d($scaleSize, $scaleSize, $scaleSize);
  }
  to {
    opacity: 1;
  }
}
.main-panel .zoomIn {
  animation-name: zoomIn95;
}
@keyframes zoomOut95 {
  from {
    opacity: 1;
  }
  to {
    opacity: 0;
    transform: scale3d($scaleSize, $scaleSize, $scaleSize);
  }
}
.main-panel .zoomOut {
  animation-name: zoomOut95;
}
.offline {
  position: fixed;
  right: 0;
  width: 64px;
  height: 45px;
  background: rgb(197 9 9 / 59%);
  z-index: 1031;
  border-radius: 8px 0 0 0px;
  text-align: center;
  top: calc(100vh - 45px);
}
</style>
