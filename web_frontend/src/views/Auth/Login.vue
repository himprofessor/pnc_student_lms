<template>
  <div class="min-h-screen flex items-center justify-center bg-gray-100">
    <div class="w-full max-w-md bg-white rounded p-8 shadow">
      <h1 class="text-2xl font-bold text-center mb-4">Login</h1>
      <form @submit.prevent="login" class="space-y-4">
        <div>
          <input
            v-model="email"
            type="email"
            placeholder="Email"
            class="w-full p-2 border rounded"
            required
          />
          <p v-if="fieldErrors.email" class="text-red-600 text-sm mt-1">{{ fieldErrors.email }}</p>
        </div>
        <div>
          <input
            v-model="password"
            type="password"
            placeholder="Password"
            class="w-full p-2 border rounded"
            required
          />
          <p v-if="fieldErrors.password" class="text-red-600 text-sm mt-1">{{ fieldErrors.password }}</p>
        </div>
        <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded hover:bg-blue-700">Login</button>
      </form>
      <p v-if="errorMessage" class="text-red-600 mt-4 text-center">{{ errorMessage }}</p>
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
const fieldErrors = ref({}) // Object to hold field-specific errors

const login = async () => {
  errorMessage.value = ''
  fieldErrors.value = {} // Reset field errors on each attempt

  try {
    const response = await axios.post('http://localhost:8000/api/login', {
      email: email.value,
      password: password.value,
    })

    // Store the token and redirect to the dashboard
    localStorage.setItem('token', response.data.token)
    router.push('/dashboard') // Redirect to the personalized dashboard
  } catch (error) {
    // Check if there are validation errors from the server
    if (error.response?.data?.error) {
      if (error.response.data.error.email) {
        fieldErrors.value.email = error.response.data.error.email[0]; // First error message
      }
      if (error.response.data.error.password) {
        fieldErrors.value.password = error.response.data.error.password[0]; // First error message
      }
    } else {
      errorMessage.value = 'Login failed. Please check your credentials.'
    }
  }
}
</script>

<style scoped>
/* Add any specific styles here if needed */
</style>