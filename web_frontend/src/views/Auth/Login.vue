<template>
  <div class="min-h-screen flex items-center justify-center bg-gray-50">
    <div class="w-full max-w-md bg-white rounded-xl p-8 shadow-lg ring-1 ring-gray-200">

      <!-- Welcome section with centered icon and text -->
      <div class="flex flex-col items-center mb-8">
        <!-- Modern graduation cap icon -->
        <div class="mb-4 p-3 bg-blue-50 rounded-full">
          <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 14l9-5-9-5-9 5 9 5z" />
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 14v7m0 0H6a2 2 0 01-2-2v-5" />
            <path stroke-linecap="round" stroke-linejoin="round" d="M18 19v-5a2 2 0 00-2-2h-1" />
          </svg>
        </div>
        <h1 class="text-1xl font-bold text-center text-gray-800">
          Welcome to PNC LeaveMS
        </h1>
        <p class="text-gray-500 mt-2 text-sm">Sign in to manage your leave requests</p>
      </div>

      <form @submit.prevent="login" class="space-y-5">
        <div>
          <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
          <input
            id="email"
            v-model="email"
            type="email"
            placeholder="Enter your email"
            class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-200 focus:border-blue-500 transition-all duration-200"
            :class="{ 'border-red-500': fieldErrors.email }"
            required
          />
          <p v-if="fieldErrors.email" class="text-red-500 text-sm mt-1.5">{{ fieldErrors.email }}</p>
        </div>
        
        <div>
          <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
          <input
            id="password"
            v-model="password"
            type="password"
            placeholder="Enter your password"
            class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-200 focus:border-blue-500 transition-all duration-200"
            :class="{ 'border-red-500': fieldErrors.password }"
            required
          />
          <p v-if="fieldErrors.password" class="text-red-500 text-sm mt-1.5">{{ fieldErrors.password }}</p>
        </div>
        
        <button
          type="submit"
          class="w-full bg-gradient-to-r from-blue-600 to-blue-500 text-white py-3 rounded-lg font-medium hover:from-blue-700 hover:to-blue-600 transition-all duration-300 shadow-md hover:shadow-lg"
          :disabled="isLoading"
          :class="{ 'opacity-75 cursor-not-allowed': isLoading }"
        >
          <span v-if="isLoading" class="flex items-center justify-center">
            <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            Logging in...
          </span>
          <span v-else>Login</span>
        </button>
      </form>

        <!-- Success Alert -->
      <div v-if="successMessage" class="fixed top-4 right-4 bg-blue-500 text-white px-6 py-3 rounded-lg shadow-lg z-50 flex items-center">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        {{ successMessage }}
      </div>
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
  // Reset errors
  fieldErrors.value = {}
  successMessage.value = ''
  isLoading.value = true

  try {
    localStorage.removeItem('authToken')
    localStorage.removeItem('user_data')
    localStorage.removeItem('role')

    const { data } = await axios.post('/login', {
      email: email.value,
      password: password.value,
    })

    const { token, user, role, dashboard_url } = data

    localStorage.setItem('authToken', token)
    localStorage.setItem('user_data', JSON.stringify(user))
    localStorage.setItem('role', role)

    axios.defaults.headers.common['Authorization'] = `Bearer ${token}`

    showSuccess('Login successful! Redirecting...')

    setTimeout(() => {
      if (dashboard_url) {
        router.replace(dashboard_url)
      } else {
        router.replace(role === 'teacher' ? '/educator-dashboard' : '/dashboard')
      }
    }, 1000)
  } catch (error) {
    if (error.response?.status === 422 && error.response?.data?.errors) {
      // Handle validation errors
      fieldErrors.value = error.response.data.errors
    } else if (error.response?.status === 401) {
      // Handle unauthorized (invalid credentials)
      fieldErrors.value = {
        email: 'Invalid email',
        password: 'Invalid password'
      }
    } else {
      // Handle other errors
      fieldErrors.value = {
        email: 'Login failed. Please try again.',
        password: 'Login failed. Please try again.'
      }
    }
  } finally {
    isLoading.value = false
  }
}
</script>

<style scoped>
/* Notification animation */
.slide-fade-enter-active {
  transition: all 0.3s ease-out;
}

.slide-fade-leave-active {
  transition: all 0.3s cubic-bezier(1, 0.5, 0.8, 1);
}

.slide-fade-enter-from,
.slide-fade-leave-to {
  transform: translateX(20px);
  opacity: 0;
}

/* Input focus effect */
input:focus {
  outline: none;
  box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.15);
}

/* Button hover effect */
button:not(:disabled):hover {
  transform: translateY(-1px);
}

/* Loading spinner animation */
@keyframes spin {
  from {
    transform: rotate(0deg);
  }
  to {
    transform: rotate(360deg);
  }
}

.animate-spin {
  animation: spin 1s linear infinite;
}
</style>