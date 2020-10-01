<template>
  <div class="md-layout">
    <div class="md-layout-item md-size-100">
      <wizard></wizard>
    </div>
  </div>
</template>
<script>
import { SignupCard } from "@/components";
import Wizard from "./Forms/Wizard";
export default {
  components: {
    Wizard
  },
  data() {
    return {
      boolean: false
    };
  }
};
</script>
<style></style>
