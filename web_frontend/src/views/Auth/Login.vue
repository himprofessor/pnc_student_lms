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
  const fieldErrors = ref({}) // Object to hold field-specific validation errors

  const login = async () => {
    // Clear previous messages and errors on each attempt
    errorMessage.value = ''
    fieldErrors.value = {}

    try {
      // Make the POST request to your login API endpoint
      const response = await axios.post('/login', { // Using relative path, baseURL from main.js will be applied
        email: email.value,
        password: password.value,
      });

      // Successfully received token, store it in localStorage
      // *** CRITICAL: Use 'authToken' key consistently with interceptor and other components ***
      localStorage.setItem('authToken', response.data.token);
      console.log('Login successful. Token stored as authToken:', response.data.token);

      // Redirect to the dashboard page
      router.push('/dashboard');
    } catch (error) {
      console.error('Login failed:', error); // Log the full error for debugging

      // Handle different types of errors from the server response
      if (error.response) {
        // Server responded with a status code that falls out of the range of 2xx
        const { status, data } = error.response;

        if (status === 422 && data.errors) {
          // Validation errors (e.g., missing email, weak password)
          console.warn('Validation errors during login:', data.errors);
          if (data.errors.email) {
            fieldErrors.value.email = data.errors.email[0];
          }
          if (data.errors.password) {
            fieldErrors.value.password = data.errors.password[0];
          }
          errorMessage.value = data.message || 'Please correct the errors in the form.';
        } else if (status === 401) {
          // Unauthorized (e.g., incorrect email or password)
          console.warn('Authentication failed (401):', data.message);
          errorMessage.value = data.message || 'Login failed. Invalid credentials.';
        } else {
          // Other server errors (e.g., 500 Internal Server Error)
          errorMessage.value = `Error ${status}: ${data.message || 'An unexpected server error occurred.'}`;
        }
      } else if (error.request) {
        // The request was made but no response was received (e.g., network issue, CORS, backend down)
        console.error('No response received from server for login request:', error.request);
        errorMessage.value = 'No response from server. Please check your network connection or the server status.';
      } else {
        // Something happened in setting up the request that triggered an error
        console.error('Error setting up login request:', error.message);
        errorMessage.value = 'An unexpected error occurred while processing your request.';
      }
    }
  }
  </script>

  <style scoped>
  /* Add any specific styles here if needed */
  </style>