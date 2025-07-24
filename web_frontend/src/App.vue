<template>
  <div>
    <StudentNavbar v-if="!route.meta.hideStudentNavbar" />
    <!-- <EducatorNavbar/> -->
    <router-view />
  </div>
</template>

<script setup>
import { useRoute } from 'vue-router';
import StudentNavbar from './components/layouts/StudentNavbar.vue';  
// import EducatorNavbar from './components/layouts/EducatorNavbar.vue';

const route = useRoute();
</script>
