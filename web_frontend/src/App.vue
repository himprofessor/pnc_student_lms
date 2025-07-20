<template>
  <Navbar />
  <router-view /> <!-- ✅ This shows ProfileSettings.vue when route changes -->
</template>

<script setup>
import Navbar from './components/layouts/Navbar.vue'
</script>
