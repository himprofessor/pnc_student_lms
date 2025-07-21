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
  import axios from 'axios' // Import Axios
  import { useRouter } from 'vue-router' // Import useRouter for navigation

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
    // Clear any existing tokens first
    localStorage.removeItem('token')
    localStorage.removeItem('user_data')

    const response = await axios.post('http://localhost:8000/api/login', {
      email: email.value,
      password: password.value,
    })

    console.log('Login response:', response.data)

    // Store the token
    localStorage.setItem('token', response.data.token)
    
    // Store user data if provided
    if (response.data.user) {
      localStorage.setItem('user_data', JSON.stringify(response.data.user))
    }

    // Redirect to dashboard
    router.push('/dashboard')
    
  } catch (error) {
    console.error('Login error:', error)
    
    if (error.response?.data?.errors) {
      fieldErrors.value = error.response.data.errors
    } else if (error.response?.data?.message) {
      errorMessage.value = error.response.data.message
    } else {
      errorMessage.value = 'Login failed. Please check your credentials.'
    }
  } finally {
    isLoading.value = false
  }
}
</script>
