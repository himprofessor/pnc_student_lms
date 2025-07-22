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
        <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded hover:bg-blue-700" :disabled="isLoading">
          <span v-if="isLoading">Logging in...</span>
          <span v-else>Login</span>
        </button>
      </form>
      <p v-if="errorMessage" class="text-red-600 mt-4 text-center">{{ errorMessage }}</p>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import axios from 'axios' // Use global axios with main.js interceptor

const router = useRouter()

const email = ref('')
const password = ref('')
const errorMessage = ref('')
const fieldErrors = ref({})
const isLoading = ref(false)

const login = async () => {
  errorMessage.value = ''
  fieldErrors.value = {}
  isLoading.value = true

  try {
    // Clear existing auth info
    localStorage.removeItem('authToken')
    localStorage.removeItem('token')
    localStorage.removeItem('user_data')

    console.log('=== LOGIN DEBUG ===')
    console.log('Attempting login with:', { email: email.value })

    const response = await axios.post('/login', {
      email: email.value,
      password: password.value,
    })

    console.log('Login response:', response.data)

    // Save token with the key that main.js interceptor expects
    localStorage.setItem('authToken', response.data.token)
    console.log('Token saved as authToken:', response.data.token)

    // Save user data if available
    if (response.data.user) {
      localStorage.setItem('user_data', JSON.stringify(response.data.user))
      console.log('User data saved:', response.data.user)
    }

    // Redirect to dashboard
    console.log('Redirecting to dashboard...')
    router.push('/dashboard')
  } catch (error) {
    console.error('=== LOGIN ERROR ===')
    console.error('Full error:', error)
    console.error('Response status:', error.response?.status)
    console.error('Response data:', error.response?.data)
    
    if (error.response?.status === 422 && error.response?.data?.errors) {
      // Handle validation errors
      const errors = error.response.data.errors
      if (errors.email) {
        fieldErrors.value.email = errors.email[0]
      }
      if (errors.password) {
        fieldErrors.value.password = errors.password[0]
      }
      errorMessage.value = error.response.data.message || 'Please correct the errors in the form.'
    } else if (error.response?.status === 401) {
      errorMessage.value = error.response.data.message || 'Invalid email or password. Please try again.'
    } else if (error.response?.data?.message) {
      errorMessage.value = error.response.data.message
    } else if (error.request) {
      errorMessage.value = 'No response from server. Please check your network connection.'
    } else {
      errorMessage.value = 'Login failed. Please check your credentials and try again.'
    }
  } finally {
    isLoading.value = false
  }
}
</script>
