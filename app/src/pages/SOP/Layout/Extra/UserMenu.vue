<template>
  <div class="user">
    <div class="photo">
      <img :src="avatar" alt="avatar" />
    </div>
    <div class="user-info">
      <a
        data-toggle="collapse"
        :aria-expanded="!isClosed"
        @click.stop="toggleMenu"
        @click.capture="clicked"
      >
        <span>
          {{ $store.state.sop.user.name || "" }}
          <b class="caret"></b>
        </span>
      </a>

      <collapse-transition>
        <div v-show="!isClosed">
          <ul class="nav">
            <slot>
              <li>
                <a to="/perfil">
                  <span class="sidebar-mini">MP</span>
                  <span class="sidebar-normal">Mi Perfil</span>
                </a>
              </li>
              <li>
                <a to="/configuraciones">
                  <span class="sidebar-mini">C</span>
                  <span class="sidebar-normal">Configuraciones</span>
                </a>
              </li>
            </slot>
          </ul>
        </div>
      </collapse-transition>
    </div>
  </div>
</template>
<script>
import { CollapseTransition } from "vue2-transitions";

export default {
  components: {
    CollapseTransition
  },
  props: {
    title: {
      type: String,
      default: "Tania Andrew"
    },
    rtlTitle: {
      type: String,
      default: "تانيا أندرو"
    },
    avatar: {
      type: String,
      default: "../../img/faces/default-avatar.png"
    }
  },
  data() {
    return {
      isClosed: true
    };
  },
  methods: {
    clicked: function(e) {
      e.preventDefault();
    },
    toggleMenu: function() {
      this.isClosed = !this.isClosed;
    }
  }
};
</script>
<style>
.collapsed {
  transition: opacity 1s;
}
</style>
