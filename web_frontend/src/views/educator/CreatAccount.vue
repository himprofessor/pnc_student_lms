<template>
  <!-- Buttons on top right -->
<!-- Import Students Button -->
<div class="flex justify-end space-x-2 pt-6 pr-6 mb-4">
  <router-link
    to="/educator-importdata"
    class="bg-gray-500 text-white font-bold py-2 px-4 rounded-md hover:bg-blue-500 transition duration-300"
  >
    Import Students
  </router-link>
</div>


  <div class="flex h-screen bg-gray-100">
    
    <!-- Sidebar -->
    <aside class="w-64 bg-white shadow-md p-4 flex flex-col -mt-0">
  <h2 class="text-xl font-bold mb-6">Student Generations</h2>

  <div class="mt-2 mb-6">
    <button
      @click="addAndSelectNewGeneration"
      class="w-full bg-blue-500 text-white font-bold py-2 px-4 rounded-md hover:bg-gray-600 transition duration-300"
    >
      + New Generation
    </button>
  </div>

  <nav class="flex-grow">
    <ul>
      <li v-for="year in visibleGenerations" :key="year" class="mb-2">
        <a
          href="#"
          @click.prevent="selectGeneration(year)"
          :class="{
            'bg-gray-500 text-white rounded-md': selectedGeneration === year,
            'text-gray-700 hover:bg-gray-300 p-2 block': true
          }"
        >
          Student {{ year }}
        </a>
      </li>
      <li v-if="generations.length > maxVisibleGenerations">
        <button
          @click="toggleShowAllGenerations"
          class="text-blue-500 hover:underline mt-2 p-2 flex items-center space-x-2 w-full text-left"
        >
          <svg v-if="!showAllGenerations" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
            <path d="M4 3a2 2 0 100 4h12a2 2 0 100-4H4z" />
            <path fill-rule="evenodd" d="M3 8h14v7a2 2 0 01-2 2H5a2 2 0 01-2-2V8zm5 3a1 1 0 011-1h2a1 1 0 110 2H9a1 1 0 01-1-1z" clip-rule="evenodd" />
          </svg>
          <svg v-else xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M15.707 15.707a1 1 0 01-1.414 0L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 011.414-1.414L10 8.586l4.293-4.293a1 1 0 011.414 1.414L11.414 10l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
          </svg>
          <span>{{ showAllGenerations ? 'Show Less' : 'Archived' }}</span>
        </button>
      </li>
    </ul>
  </nav>
</aside>
    <!-- Main Form -->
    <main class="flex-1  p-4 bg-gray-100 flex justify-center items-start">
      <div class="w-full max-w-2xl p-12 space-y-6 bg-white rounded-2xl shadow-2xl border border-gray-200">
        <h2 class="text-3xl font-bold text-center text-gray-800">Create Student Account</h2>
        <p class="text-center text-gray-600">Fill in the details to create a new student account.</p>

        <form @submit.prevent="createAccount" class="space-y-4">
          <!-- Student Name -->
          <div class="space-y-2">
            <label for="name" class="block text-sm font-medium text-gray-700">Student Name</label>
            <input
              type="text"
              id="name"
              v-model="student.name"
              placeholder="Enter student name"
              required
              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
            />
          </div>

          <div class="space-y-2">
  <label for="email" class="block text-sm font-medium text-gray-700">Email Address</label>
  <input
    type="email"
    id="email"
    v-model="student.email"
    placeholder="Enter your name@student.passerellessnumeriques.org"
    required
    class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2"
    :class="{
      'border-red-500 ring-red-500': emailError,
      'border-gray-300 ring-blue-500': !emailError
    }"
  />
  <p v-if="emailError" class="mt-1 text-sm text-red-600">{{ emailError }}</p>
</div>
          <!-- Generation Field -->
          <div class="space-y-2" v-if="!isGenerationLocked">
            <label for="generation" class="block text-sm font-medium text-gray-700">Generation</label>
            <select
              id="generation"
              v-model="student.generation"
              required
              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
            >
              <option value="">Select Generation</option>
              <option v-for="year in generations" :key="year" :value="year">{{ year }}</option>
            </select>
          </div>

          <!-- Show selected generation if locked -->
          <div class="space-y-2" v-else>
            <label class="block text-sm font-medium text-gray-700">Generation</label>
            <p class="px-4 py-2 bg-gray-100 rounded-lg">{{ student.generation }}</p>
          </div>

          <!-- Password -->
          <div class="space-y-2">
  <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
  <input
    type="password"
    id="password"
    v-model="student.password"
    placeholder="Enter a strong password"
    required
    class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2"
    :class="{
      'border-red-500 ring-red-500': passwordError,
      'border-gray-300 ring-blue-500': !passwordError
    }"
  />
  <p v-if="passwordError" class="mt-1 text-sm text-red-600">{{ passwordError }}</p>
</div>

<div class="space-y-2">
  <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirm Password</label>
  <input
    type="password"
    id="password_confirmation"
    v-model="student.password_confirmation"
    placeholder="Re-enter your password"
    required
    class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2"
    :class="{
      'border-red-500 ring-red-500': passwordError,
      'border-gray-300 ring-blue-500': !passwordError
    }"
  />
</div>

         
          <!-- Submit Button -->
          <button
            type="submit"
            class="w-full py-3 mt-4 text-white bg-blue-600 rounded-lg font-semibold hover:bg-blue-700 transition-colors"
          >
            Create Account
          </button>
        </form>
      </div>

      <!-- Toast Notifications -->
      <Toast 
        v-if="toast.isVisible"
        :message="toast.message"
        :type="toast.type"
        :position="toast.position"
      />
    </main>
  </div>
</template>
<script>
import axios from 'axios';
import Toast from '@/components/Toast.vue';

export default {
  name: 'CreateAccountStudent',
  components: { Toast },
  data() {
    return {
      student: {
        name: '',
        email: '',
        generation: '',
        password: '',
        password_confirmation: ''
      },
      generations: [],
      isGenerationLocked: false,
      selectedGeneration: null,
      maxVisibleGenerations: 3,
      showAllGenerations: false,
      toast: { isVisible: false, message: '', type: '', position: 'top-right' },
      emailError: null,
      passwordError: null // New data property for password errors
    };
  },
  mounted() {
    this.fetchGenerations();

    const generationFromQuery = this.$route.query.generation;
    if (generationFromQuery) {
      this.student.generation = parseInt(generationFromQuery);
      this.isGenerationLocked = true;
    }
  },
  computed: {
    visibleGenerations() {
      const sortedGenerations = [...this.generations].sort((a, b) => b - a);
      return this.showAllGenerations
        ? sortedGenerations
        : sortedGenerations.slice(0, this.maxVisibleGenerations);
    }
  },
  methods: {
    addAndSelectNewGeneration() {
      const currentYear = new Date().getFullYear();
      const latestGeneration = this.generations.length > 0
        ? Math.max(...this.generations)
        : currentYear - 1;
      const newYear = latestGeneration < currentYear ? currentYear : latestGeneration + 1;

      if (!this.generations.includes(newYear)) {
        this.generations.push(newYear);
      }
      this.generations.sort((a, b) => b - a);
      this.selectedGeneration = newYear;
      this.student.generation = newYear;
      this.isGenerationLocked = true;
    },
    fetchGenerations() {
      const mockGenerations = [2025, 2026, 2027];
      this.generations = mockGenerations;
      if (this.generations.length > 0) {
        this.selectedGeneration = Math.max(...this.generations);
      }
    },
    selectGeneration(year) {
      this.selectedGeneration = year;
      this.student.generation = year;
      this.isGenerationLocked = true;
    },
    toggleShowAllGenerations() {
      this.showAllGenerations = !this.showAllGenerations;
    },

    validateEmailDomain(email) {
      const requiredDomain = '@student.passerellessnumeriques.org';
      return email.endsWith(requiredDomain);
    },
    
    validatePassword() {
        if (this.student.password.length < 8) {
            this.passwordError = 'Password must be at least 8 characters long.';
            return false;
        }
        if (this.student.password !== this.student.password_confirmation) {
            this.passwordError = 'Passwords do not match.';
            return false;
        }
        this.passwordError = null;
        return true;
    },

    async createAccount() {
      this.toast.isVisible = false;
      
      // Reset validation errors
      this.emailError = null;
      this.passwordError = null;

      // Perform client-side validation
      if (!this.validateEmailDomain(this.student.email)) {
        this.emailError = 'Email must end with @student.passerellessnumeriques.org.';
        return;
      }

      if (!this.validatePassword()) {
        return;
      }

      const token = localStorage.getItem('authToken');
      if (!token) {
        this.showToast('Authentication token is missing.', 'error');
        return;
      }

      try {
        const response = await axios.post(
          'http://127.0.0.1:8000/api/educator/students',
          this.student,
          { headers: { 'Authorization': `Bearer ${token}`, 'Content-Type': 'application/json' } }
        );
        this.showToast(response.data.message, 'success');
        this.resetForm();
      } catch (error) {
        let firstError = 'An unexpected error occurred.';
        if (error.response?.status === 422) {
          firstError = Object.values(error.response.data.errors).flat()[0];
        } else if (error.response?.status === 403) {
          firstError = 'Forbidden: You do not have permission.';
        }
        this.showToast(firstError, 'error');
      }
    },
    resetForm() {
      this.student.name = '';
      this.student.email = '';
      if (!this.isGenerationLocked) this.student.generation = '';
      this.student.password = '';
      this.student.password_confirmation = '';
      this.emailError = null;
      this.passwordError = null; // Reset password error on form reset
    },
    showToast(message, type) {
      this.toast.isVisible = true;
      this.toast.message = message;
      this.toast.type = type;
    }
  }
};
</script>