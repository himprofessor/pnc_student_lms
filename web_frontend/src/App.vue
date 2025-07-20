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

const authPages = ['/login', '/register'];

const isAuthPage = computed(() => authPages.includes(route.path));
</script>
