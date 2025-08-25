<template>
  <!-- Buttons on top right -->
  <div class="flex justify-end space-x-2 pt-6 pr-6 mb-4" v-if="student.generation">
    <router-link to="/educator-importdata" class="bg-gray-500 text-white font-bold py-2 px-4 rounded-md hover:bg-blue-500 transition duration-300" > Import Students </router-link>
</div>


  <div class="flex h-screen bg-gray-100">
    
    <!-- Sidebar -->
    <aside class="w-64 bg-white shadow-md p-4 flex flex-col -mt-0">

      <h2 class="text-xl font-bold mb-6">Student Generations</h2>

      <!-- New Generation Button + Dropdown -->
      <div class="mt-2 mb-6 relative">
        <button
          @click="toggleDropdown"
          class="w-full bg-green-500 text-white font-bold py-2 px-4 rounded-md hover:bg-green-600 transition duration-300"
        >
          + New Generation
        </button>
        
        <!-- Dropdown for Selecting Year -->
        <div v-if="showDropdown" class="absolute mt-2 w-full bg-white border rounded-lg shadow-md z-50">
          <ul>
            <li
              v-for="year in availableYears"
              :key="year"
              @click="goToCreateForm(year)"
              class="px-4 py-2 hover:bg-green-100 cursor-pointer"
            >
              {{ year }}
            </li>
          </ul>
        </div>
      </div>

      <!-- List of Existing Generations -->
      <nav class="flex-grow">
        <ul>
          <li
            v-for="year in visibleGenerations"
            :key="year"
            class="mb-2"
          >
            <a
              href="#"
              @click.prevent="selectGeneration(year)"
              :class="{
                'bg-blue-500 text-white rounded-md': selectedGeneration === year,
                'text-gray-700 hover:bg-gray-200 p-2 block': true
              }"
            >
              Student {{ year }}
            </a>
          </li>
          <li v-if="generations.length > maxVisibleGenerations">
            <button
              @click="toggleShowAllGenerations"
              class="text-blue-500 hover:underline mt-2 p-2 block w-full text-left"
            >
              {{ showAllGenerations ? 'Show Less' : 'See More' }}
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
              placeholder="e.g., Jane Doe"
              required
              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
            />
          </div>

          <!-- Email -->
          <div class="space-y-2">
            <label for="email" class="block text-sm font-medium text-gray-700">Email Address</label>
            <input
              type="email"
              id="email"
              v-model="student.email"
              placeholder="e.g., student@example.com"
              required
              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
            />
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
              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
            />
          </div>

          <!-- Password Confirmation -->
          <div class="space-y-2">
            <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirm Password</label>
            <input
              type="password"
              id="password_confirmation"
              v-model="student.password_confirmation"
              placeholder="Re-enter your password"
              required
              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
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
      generations: [2025, 2026, 2027, 2028, 2029, 2030, 2031, 2032],
      isGenerationLocked: false,
      showDropdown: false,
      availableYears: [2025, 2026, 2027, 2028, 2029, 2030],
      selectedGeneration: null,
      maxVisibleGenerations: 5,
      showAllGenerations: false,
      toast: { isVisible: false, message: '', type: '', position: 'top-right' }
    };
  },
  mounted() {
    const generationFromQuery = this.$route.query.generation;
    if (generationFromQuery) {
      this.student.generation = parseInt(generationFromQuery);
      this.isGenerationLocked = true;
    }
  },
  computed: {
    visibleGenerations() {
      return this.showAllGenerations
        ? this.generations
        : this.generations.slice(0, this.maxVisibleGenerations);
    }
  },
  methods: {
    toggleDropdown() {
      this.showDropdown = !this.showDropdown;
    },
    goToCreateForm(year) {
      this.student.generation = year;
      this.isGenerationLocked = true;
      this.showDropdown = false;
    },
    selectGeneration(year) {
      this.selectedGeneration = year;
      this.student.generation = year;
      this.isGenerationLocked = true;
    },
    toggleShowAllGenerations() {
      this.showAllGenerations = !this.showAllGenerations;
    },
    async createAccount() {
      this.toast.isVisible = false;
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
    },
    showToast(message, type) {
      this.toast.isVisible = true;
      this.toast.message = message;
      this.toast.type = type;
    }
  }
};
</script>
