<template>
  <div>
    <Navbar v-if="!route.meta.hideNavbar" />
    <router-view />
  </div>
</template>

<script setup>
import { useRoute } from 'vue-router';
import Navbar from './components/layouts/Navbar.vue';

const route = useRoute();
</script>
