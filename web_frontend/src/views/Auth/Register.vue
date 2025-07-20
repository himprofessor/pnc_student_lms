<template>
  <div class="flex items-center justify-center min-h-screen bg-gray-100">
    <div class="w-full max-w-md bg-white rounded-lg shadow-lg p-8">
      <h1 class="text-2xl font-bold text-center mb-6">Sign up for your account</h1>
      <form @submit.prevent="register" class="space-y-4">
        <div>
          <label for="name" class="block text-gray-700">Name</label>
          <input v-model="name" id="name" type="text" placeholder="Enter your name" class="w-full p-2 border rounded" required />
          <p v-if="fieldErrors.name" class="text-red-600 text-sm mt-1">{{ fieldErrors.name }}</p>
        </div>
        <div>
          <label for="email" class="block text-gray-700">Email address</label>
          <input v-model="email" id="email" type="email" placeholder="Enter your email" class="w-full p-2 border rounded" required />
          <p v-if="fieldErrors.email" class="text-red-600 text-sm mt-1">{{ fieldErrors.email }}</p>
        </div>
        <div>
          <label for="password" class="block text-gray-700">Password</label>
          <input v-model="password" id="password" type="password" placeholder="Enter your password" class="w-full p-2 border rounded" :class="{'border-red-600': fieldErrors.password}" required />
          <p v-if="fieldErrors.password" class="text-red-600 text-sm mt-1">{{ fieldErrors.password }}</p>
        </div>
        <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded hover:bg-blue-700">Register</button>
      </form>
      <p v-if="errorMessage" class="text-red-600 mt-4 text-center">{{ errorMessage }}</p>
      <p class="mt-4 text-center">
        Already have an account? 
        <router-link to="/login" class="text-blue-600">Sign In</router-link>
      </p>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import axios from 'axios'
import { useRouter } from 'vue-router'

// Reactive state variables
const router = useRouter()
const name = ref('')
const email = ref('')
const password = ref('')
const fieldErrors = ref({}) // Changed from errorMessage to fieldErrors

// Function to register a new user
const register = async () => {
  fieldErrors.value = {} // Reset errors

  try {
    const { apiEndpoint, roleId } = determineEndpointAndRole(email.value)

    const response = await axios.post(apiEndpoint, {
      name: name.value,
      email: email.value,
      password: password.value,
      role_id: roleId
    })

    alert(response.data.message)
    router.push('/login')
  } catch (error) {
    // Populate fieldErrors with specific messages
    if (error.response?.data?.error) {
      if (error.response.data.error.password) {
        fieldErrors.value.password = error.response.data.error.password[0];
      }
      if (error.response.data.error.email) {
        fieldErrors.value.email = error.response.data.error.email[0];
      }
      if (error.response.data.error.name) {
        fieldErrors.value.name = error.response.data.error.name[0];
      }
    } else {
      fieldErrors.value.general = 'An unexpected error occurred.'
    }
  }
}

// Helper function to determine API endpoint and role based on email
const determineEndpointAndRole = (email) => {
  if (email.endsWith('@student.passerellesnumeriques.org')) {
    return {
      apiEndpoint: 'http://localhost:8000/api/register/student',
      roleId: 3 // Student
    }
  } else if (email.endsWith('@passerellesnumeriques.org')) {
    return {
      apiEndpoint: 'http://localhost:8000/api/register/teacher',
      roleId: 2 // Teacher
    }
  } else {
    throw new Error("Invalid email domain. Please use a valid student or teacher email.")
  }
}
</script>

<style scoped>
/* Add any specific styles here if needed */
</style>