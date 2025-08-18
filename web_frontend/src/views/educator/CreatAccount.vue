<template>
  <div class="flex items-center justify-center min-h-screen bg-gray-100 p-6">
    <div class="w-full max-w-md p-8 space-y-6 bg-white rounded-xl shadow-lg">
      <h2 class="text-3xl font-bold text-center text-gray-800">Create Student Account</h2>
      <p class="text-center text-gray-600">Fill in the details to create a new student account.</p>

      <form @submit.prevent="createAccount" class="space-y-4">
        <div class="space-y-2">
          <label for="name" class="block text-sm font-medium text-gray-700">Student Name</label>
          <input
            type="text"
            id="name"
            v-model="student.name"
            placeholder="e.g., Jane Doe"
            required
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
          />
        </div>

        <div class="space-y-2">
          <label for="email" class="block text-sm font-medium text-gray-700">Email Address</label>
          <input
            type="email"
            id="email"
            v-model="student.email"
            placeholder="e.g., student@example.com"
            required
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
          />
        </div>

        <div class="space-y-2">
          <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
          <input
            type="password"
            id="password"
            v-model="student.password"
            placeholder="Enter a strong password"
            required
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
          />
        </div>

        <div class="space-y-2">
          <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirm Password</label>
          <input
            type="password"
            id="password_confirmation"
            v-model="student.password_confirmation"
            placeholder="Re-enter your password"
            required
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
          />
        </div>

        <button
          type="submit"
          class="w-full py-3 mt-4 text-white bg-blue-600 rounded-lg font-semibold hover:bg-blue-700 transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
        >
          Create Account
        </button>
      </form>
    </div>
  </div>

  <Toast 
    v-if="toast.isVisible"
    :message="toast.message"
    :type="toast.type"
    :position="toast.position"
  />
</template>

<script>
import axios from 'axios';
import Toast from '@/components/Toast.vue'; // ⚠️ Import the new Toast component

export default {
  name: 'CreateAccountStudent',
  components: {
    Toast, // ⚠️ Register the Toast component
  },
  data() {
    return {
      student: {
        name: '',
        email: '',
        password: '',
        password_confirmation: ''
      },
      toast: { // ⚠️ New reactive object for toast messages
        isVisible: false,
        message: '',
        type: '',
        position: 'top-right',
      },
    };
  },
  methods: {
    async createAccount() {
      this.toast.isVisible = false; // Hide any existing toasts
      
      const token = localStorage.getItem('authToken');

      if (!token) {
        this.showToast('Authentication token is missing. Please log in as an educator.', 'error');
        return;
      }

      try {
        const response = await axios.post(
          'http://127.0.0.1:8000/api/educator/students',
          this.student,
          {
            headers: {
              'Authorization': `Bearer ${token}`,
              'Content-Type': 'application/json'
            }
          }
        );
        this.showToast(response.data.message, 'success');
        this.resetForm();
      } catch (error) {
        if (error.response) {
          if (error.response.status === 422) {
            const validationErrors = Object.values(error.response.data.errors).flat();
            const firstError = validationErrors[0];
            this.showToast(firstError, 'error');
          } else if (error.response.status === 403) {
            this.showToast('Forbidden: You do not have permission to create a student account.', 'error');
          } else {
            this.showToast('An unexpected error occurred.', 'error');
            console.error('API Error:', error.response.data);
          }
        } else {
          this.showToast('Network error. Please check your connection or server status.', 'error');
          console.error('Network Error:', error);
        }
      }
    },
    resetForm() {
      this.student.name = '';
      this.student.email = '';
      this.student.password = '';
      this.student.password_confirmation = '';
    },
    showToast(message, type, position = 'top-right') {
      this.toast.isVisible = true;
      this.toast.message = message;
      this.toast.type = type;
      this.toast.position = position;
    }
  }
};
</script>