<template>
  <div>
    <Navbar />
    <router-view />
  </div>
</template>

<script setup>
import Navbar from './components/layouts/Navbar.vue';
</script>

<style>
nav {
  padding: 20px;
  text-align: center;
}
nav a {
  margin: 0 10px;
  text-decoration: none;
  color: blue;
}
</style>
