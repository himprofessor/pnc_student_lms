<template>
  <div>
    <!-- Show Student Navbar if the route role is 'student' -->
    <StudentNavbar v-if="route.meta.role === 'student'" />

    <!-- Show Educator Navbar if the route role is 'teacher' -->
    <EducatorNavbar v-if="route.meta.role === 'teacher'" />

    <!-- Show Admin Navbar if the route role is 'admin' -->
    <AdminNavbar v-if="route.meta.role === 'admin'" />

    <!-- Page Content -->
    <router-view />
  </div>
</template>

<script setup>
import { useRoute } from 'vue-router';
import StudentNavbar from './components/layouts/StudentNavbar.vue';
import EducatorNavbar from './components/layouts/EducatorNavbar.vue';
import AdminNavbar from './components/layouts/AdminNavbar.vue';

const route = useRoute();
</script>

<style scoped>
/* Add any styles here */
</style>