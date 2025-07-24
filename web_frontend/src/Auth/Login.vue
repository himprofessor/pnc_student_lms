<template>
  <div class="login">
    <h2>Login</h2>
    <form @submit.prevent="loginUser">
      <input type="email" v-model="email" placeholder="Email" required />
      <input type="password" v-model="password" placeholder="Password" required />
      <button type="submit" :disabled="loading">Login</button>
      <p v-if="error" class="error">{{ error }}</p>
      <p v-if="loading" class="loading">Logging in...</p>
    </form>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import axios from 'axios'

const email = ref('')
const password = ref('')
const error = ref(null)
const loading = ref(false)
const router = useRouter()

const loginUser = async () => {
  error.value = null  // Reset error message
  loading.value = true // Set loading state

  try {
    const response = await axios.post('/api/login', {
      email: email.value,
      password: password.value,
    })

    // Save token in localStorage or sessionStorage
    const token = response.data.token
    localStorage.setItem('token', token)

    // Optionally, redirect based on role
    const role = response.data.role
    if (role === 'teacher') {
      router.push('/teacher-dashboard') // Redirect to teacher dashboard
    } else if (role === 'student') {
      router.push('/student-dashboard') // Redirect to student dashboard
    } else {
      // Handle unexpected roles
      error.value = 'Unauthorized role'
    }

    console.log('Login successful:', response.data)
  } catch (err) {
    error.value = err.response?.data?.error || 'Login failed'
  } finally {
    loading.value = false // Reset loading state
  }
}
</script>

<style scoped>
.error {
  color: red;
}
.loading {
  color: blue;
}
</style>