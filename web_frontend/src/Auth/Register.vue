<template>
  <div class="register">
    <h2>Register</h2>
    <form @submit.prevent="registerUser">
      <input type="text" v-model="name" placeholder="Name" required />
      <input type="email" v-model="email" placeholder="Email" required />
      <input type="password" v-model="password" placeholder="Password" required />
      <button type="submit">Register</button>
      <p v-if="error" class="error">{{ error }}</p>
    </form>
  </div>
</template>

<script setup>
import { ref } from 'vue'
// import axios from '../axios'

const name = ref('')
const email = ref('')
const password = ref('')
const error = ref(null)

const registerUser = async () => {
  try {
    const response = await axios.post('/api/register', {
      name: name.value,
      email: email.value,
      password: password.value,
    })
    // Handle success (e.g., redirect or show a message)
    console.log('Registration successful:', response.data)
  } catch (err) {
    error.value = err.response?.data?.error || 'Registration failed'
  }
}
</script>

<style scoped>
.error {
  color: red;
}
</style>