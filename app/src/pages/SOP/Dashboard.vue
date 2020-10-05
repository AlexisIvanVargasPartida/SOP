<template>
  <div class="md-layout">
    <div class="md-layout-item md-medium-size-50 md-xsmall-size-100 md-size-25">
      <stats-card header-color="green">
        <template slot="header">
          <div class="card-icon">
            <i class="fa fa-user"></i>
          </div>
          <p class="category">Total Simpatizantes</p>
          <h3 class="title">
            <animated-number :value="245"></animated-number>
          </h3>
        </template>
      </stats-card>
    </div>
    <div class="md-layout-item md-medium-size-50 md-xsmall-size-100 md-size-25">
      <stats-card header-color="rose">
        <template slot="header">
          <div class="card-icon">
            <i class="fa fa-users"></i>
          </div>
          <p class="category">Población</p>
          <h3 class="title">
            <animated-number :value="75"></animated-number>
          </h3>
        </template>
      </stats-card>
    </div>
    <div class="md-layout-item md-medium-size-50 md-xsmall-size-100 md-size-25">
      <stats-card header-color="blue">
        <template slot="header">
          <div class="card-icon">
            <i class="fa fa-cogs"></i>
          </div>
          <p class="category">Total Gestiones</p>
          <h3 class="title">
            <animated-number :value="0"></animated-number>
          </h3>
        </template>
      </stats-card>
    </div>
    <div class="md-layout-item md-medium-size-50 md-xsmall-size-100 md-size-25">
      <stats-card header-color="warning">
        <template slot="header">
          <div class="card-icon">
            <i class="fa fa-dollar-sign"></i>
          </div>
          <p class="category">Total Gestionado</p>
          <h3 class="title">
            <animated-number :value="0"></animated-number>
          </h3>
        </template>
      </stats-card>
    </div>
  </div>
</template>

<script>
import {
  StatsCard,
  ChartCard,
  AnimatedNumber
} from "@/components";

export default {
  components: {
    StatsCard,
    AnimatedNumber
  },
  data() {
    return {};
  }
};
</script>
