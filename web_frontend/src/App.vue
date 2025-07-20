<template>
  <div>
    <nav>
      <RouterLink to="/login">Login</RouterLink> |
      <RouterLink to="/register">Register</RouterLink>
    </nav>
    <RouterView />
  </div>
</template>

<script setup>
import { RouterLink, RouterView } from 'vue-router'
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
