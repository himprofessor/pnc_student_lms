<template>
  <div>
    <Navbar v-if="!isAuthPage" />
    <router-view />
  </div>
</template>

<script setup>
import { useRoute } from 'vue-router';
import { computed } from 'vue';
import Navbar from './components/layouts/Navbar.vue';

const route = useRoute();

const isAuthPage = computed(() => {
  return route.path === '/login' || route.path === '/register';
});
</script>
