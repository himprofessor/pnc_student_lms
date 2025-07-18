<template>
  <div class="min-h-screen bg-gray-100">
    <Navbar />
    <ProfileSettings />
  </div>
</template>

<script setup>
import ProfileSettings from "./views/ProfileSettings.vue";
import Navbar from "./components/layouts/Navbar.vue";
</script>
