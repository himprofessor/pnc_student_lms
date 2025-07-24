<template>
  <div class="login">
    <h2>Login</h2>
    <form @submit.prevent="loginUser">
      <input type="email" v-model="email" placeholder="Email" required />
      <input type="password" v-model="password" placeholder="Password" required />
      <button type="submit">Login</button>
      <p v-if="error" class="error">{{ error }}</p>
    </form>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import axios from 'axios'

const email = ref('')
const password = ref('')
const error = ref(null)

const loginUser = async () => {
  try {
    const response = await axios.post('/api/login', {
      email: email.value,
      password: password.value,
    })
    // Handle success (e.g., save token and redirect)
    console.log('Login successful:', response.data)
  } catch (err) {
    error.value = err.response?.data?.error || 'Login failed'
  }
}
</script>

<style scoped>
.error {
  color: red;
}
</style>