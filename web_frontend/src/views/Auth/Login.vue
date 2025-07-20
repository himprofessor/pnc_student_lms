<template>
  <div class="min-h-screen flex items-center justify-center bg-gray-100">
    <div class="w-full max-w-md bg-white rounded p-8 shadow">
      <h1 class="text-2xl font-bold text-center mb-4">Login</h1>
      <form @submit.prevent="login" class="space-y-4">
        <input v-model="email" type="email" placeholder="Email" class="w-full p-2 border rounded" required />
        <input v-model="password" type="password" placeholder="Password" class="w-full p-2 border rounded" required />
        <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded hover:bg-blue-700">Login</button>
      </form>
      <p v-if="errorMessage" class="text-red-600 mt-4 text-center">{{ errorMessage }}</p>
      <p class="mt-4 text-center">
        Don't have an account? 
        <router-link to="/register" class="text-blue-600">Register</router-link>
      </p>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import axios from 'axios'
import { useRouter } from 'vue-router'

const router = useRouter()

const email = ref('')
const password = ref('')
const errorMessage = ref('')

const login = async () => {
  errorMessage.value = ''

  try {
    const response = await axios.post('http://localhost:8000/api/login', {
      email: email.value,
      password: password.value,
    })

    // Store the token and redirect to the dashboard
    localStorage.setItem('token', response.data.token)
    alert('Login successful!')
    router.push('/dashboard') // Redirect to the personalized dashboard
  } catch (error) {
    errorMessage.value = error.response?.data?.error || 'Login failed. Please check your credentials.'
  }
}
</script>