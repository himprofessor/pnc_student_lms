<template>
  <div>
    <nav class="p-4 bg-gray-800 text-white flex gap-4">
      <router-link to="/">Home</router-link>
      <router-link to="/about">About</router-link>
      <router-link to="/profile-settings">Profile Settings</router-link>
    </nav>

    <router-view />
  </div>
</template>

<script>
export default {
  name: "App",
};
</script>
