<template>
  <div v-if="route.meta.hideLayout">
    <router-view />
  </div>
  <div v-else class="container">
    <Sidebar />
    <div class="shell">
      <Navbar />
      <div class="content">
        <router-view />
      </div>
    </div>
  </div>
</template>

<script setup>
import { useRoute } from "vue-router";
import Navbar from "./components/Navbar.vue";
import Sidebar from "./components/Sidebar.vue";

const route = useRoute();
</script>

<style>
.shell{
  flex:1;
  min-width:0;
  display:flex;
  flex-direction:column;
}
</style>
