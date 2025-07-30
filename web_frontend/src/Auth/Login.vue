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

    <!-- Success Alert -->
    <div v-if="successMessage" class="fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50 flex items-center">
      <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
      </svg>
      {{ successMessage }}
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import axios from 'axios'

const router = useRouter()

const email = ref('')
const password = ref('')
const errorMessage = ref('')
const successMessage = ref('')
const fieldErrors = ref({})
const isLoading = ref(false)

const showSuccess = (message) => {
  successMessage.value = message
  setTimeout(() => {
    successMessage.value = ''
  }, 3000)
}

const login = async () => {
  errorMessage.value = ''
  fieldErrors.value = {}
  successMessage.value = ''
  isLoading.value = true

  try {
    // Clear existing auth info
    localStorage.removeItem('authToken')
    localStorage.removeItem('user_data')

    const response = await axios.post('http://127.0.0.1:8000/api/login', {
      email: email.value,
      password: password.value,
    })

    // Save token and user data
    localStorage.setItem('authToken', response.data.token)
    localStorage.setItem('user_data', JSON.stringify(response.data.user))

    // Show success message
    showSuccess('Login successful! Redirecting to dashboard...')

    // Redirect based on user role
    const role = response.data.user.role; // Assuming role is part of user data
    setTimeout(() => {
      if (role === 'teacher') {
        router.push('/educator/teacher-dashboard'); // Redirect to teacher dashboard
      } else {
        router.push('/dashboard'); // Redirect for students
      }
    }, 2000)

  } catch (error) {
    if (error.response?.status === 422 && error.response?.data?.errors) {
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
    } else {
      errorMessage.value = 'Login failed. Please check your credentials and try again.'
    }
  } finally {
    isLoading.value = false
  }
}
</script>

<style scoped>
/* Success alert animation */
.fixed.top-4.right-4 {
  animation: slideInRight 0.3s ease-out;
}

@keyframes slideInRight {
  from {
    transform: translateX(100%);
    opacity: 0;
  }
  to {
    transform: translateX(0);
    opacity: 1;
  }
}

/* Button hover effects */
button:hover:not(:disabled) {
  transform: translateY(-1px);
  transition: all 0.2s ease-in-out;
}

/* Input focus effects */
input:focus {
  outline: none;
  border-color: #3b82f6;
  box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
  transition: all 0.2s ease-in-out;
}
</style>