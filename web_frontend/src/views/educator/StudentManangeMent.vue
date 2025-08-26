<template>
  <div class="flex h-screen bg-gray-100">
    <!-- Sidebar -->
    <aside class="w-64 bg-white shadow-md p-4 flex flex-col -mt-0">
  <h2 class="text-xl font-bold mb-6">Student Generations</h2>

  <div class="mt-2 mb-6 relative">
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
          class="text-blue-500 hover:underline mt-2 p-2 block w-full text-left"
        >
          {{ showAllGenerations ? 'Show Less' : 'See More' }}
        </button>
      </li>
    </ul>
  </nav>
</aside>

    <!-- Main content -->
    <main class="flex-1 p-8 overflow-y-auto">
      <header class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold">Students {{ selectedGeneration }}</h1>
        <div class="flex space-x-4">
          <router-link
            to="/create-account"
            class="bg-blue-500 text-white font-bold py-2 px-4 rounded-md hover:bg-gray-600 transition duration-300"
          >
            Create Student
          </router-link>
          <router-link
            to="/educator-importdata"
            class="bg-gray-500 text-white font-bold py-2 px-4 rounded-md hover:bg-blue-500 transition duration-300"
          >
            Import Students
          </router-link>
        </div>
      </header>

      <!-- Success Alert -->
      <div
        v-if="successMessage"
        class="fixed top-4 right-4 bg-blue-500 text-white px-6 py-3 rounded-lg shadow-lg z-50 flex items-center"
      >
        <svg
          class="w-5 h-5 mr-2"
          fill="none"
          stroke="currentColor"
          stroke-width="2"
          viewBox="0 0 24 24"
        >
          <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        {{ successMessage }}
      </div>

      <!-- Error Alert -->
      <div
        v-if="errorMessage"
        class="fixed top-4 right-4 bg-red-500 text-white px-6 py-3 rounded-lg shadow-lg z-50 flex items-center"
      >
        <svg
          class="w-5 h-5 mr-2"
          fill="none"
          stroke="currentColor"
          stroke-width="2"
          viewBox="0 0 24 24"
        >
          <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
        </svg>
        {{ errorMessage }}
      </div>

      <div class="bg-white shadow-md rounded-lg p-6">
        <div v-if="loading" class="text-center py-4">Loading students...</div>
        <div v-else-if="filteredStudents.length === 0" class="text-center py-4">
          No students found for this generation.
        </div>
        <table v-else class="min-w-full">
  <thead>
    <tr>
      <th class="py-2 px-4 border-b text-left">ID</th>
      <th class="py-2 px-4 border-b text-left">Name</th>
      <th class="py-2 px-4 border-b text-left">Email</th>
      <th class="py-2 px-4 border-b text-left">Generation</th>
      <th class="py-2 px-4 border-b text-left">Action</th>
    </tr>
  </thead>
  <tbody>
    <tr v-for="(student, index) in filteredStudents" :key="student.id">
      <td class="py-2 px-4 border-b">{{ index + 1 }}</td>
      <td class="py-2 px-4 border-b">{{ student.name }}</td>
      <td class="py-2 px-4 border-b">{{ student.email }}</td>
      <td class="py-2 px-4 border-b">{{ student.generation }}</td>
      <td class="py-2 px-4 border-b">
        <div class="flex items-center space-x-2">
          <button @click="editStudent(student)" class="text-blue-500 hover:text-blue-700 transition duration-300">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
              <path d="M13.586 3.586a2 2 0 112.828 2.828l-7.243 7.242a2 2 0 01-1.414.586H4a1 1 0 01-1-1v-2.172a2 2 0 01.586-1.414l7.243-7.242zM15 5l1.5-1.5" />
            </svg>
          </button>
          <button @click="deleteStudent(student.id)" class="text-red-500 hover:text-red-700 transition duration-300">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
              <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
            </svg>
          </button>
        </div>
      </td>
    </tr>
  </tbody>
</table>
      </div>

      <!-- Edit Student Modal -->
      <div v-if="showEditModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-lg p-6 w-full max-w-md">
          <h2 class="text-xl font-bold mb-4">Edit Student</h2>

          <form @submit.prevent="updateStudent">
            <div class="mb-4">
              <label class="block text-gray-700 mb-2">Name</label>
              <input v-model="editForm.name" type="text" class="w-full px-3 py-2 border rounded-md" required>
            </div>
            <div class="mb-4">
              <label class="block text-gray-700 mb-2">Email</label>
              <input v-model="editForm.email" type="email" class="w-full px-3 py-2 border rounded-md" required>
            </div>
            <div class="mb-4">
              <label class="block text-gray-700 mb-2">Generation</label>
              <input v-model="editForm.generation" type="text" class="w-full px-3 py-2 border rounded-md" required>
            </div>
            <div class="mb-4">
              <label class="block text-gray-700 mb-2">New Password (leave blank to keep current)</label>
              <input v-model="editForm.password" type="password" class="w-full px-3 py-2 border rounded-md">
            </div>
            <div class="mb-4">
              <label class="block text-gray-700 mb-2">Confirm Password</label>
              <input v-model="editForm.password_confirmation" type="password" class="w-full px-3 py-2 border rounded-md">
            </div>

            <div class="flex justify-end space-x-4">
              <button type="button" @click="showEditModal = false" class="px-4 py-2 text-gray-600 hover:text-gray-800">Cancel</button>
              <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600" :disabled="updating">
                {{ updating ? 'Updating...' : 'Update' }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </main>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import axios from 'axios';
import { useRouter } from 'vue-router';

const router = useRouter();

const generations = ref([]);
const selectedGeneration = ref(null);
const students = ref([]);
const maxVisibleGenerations = ref(3);
const showAllGenerations = ref(false);
const loading = ref(false);
const deleting = ref(false);

const successMessage = ref('');
const errorMessage = ref('');

const showEditModal = ref(false);
const updating = ref(false);
const editForm = ref({
  id: null,
  name: '',
  email: '',
  generation: '',
  password: '',
  password_confirmation: ''
});

// Function to add a new generation and select it automatically
const addAndSelectNewGeneration = () => {
  const currentYear = new Date().getFullYear();
  const latestGeneration = generations.value.length > 0 ? Math.max(...generations.value) : currentYear - 1;
  const newYear = latestGeneration < currentYear ? currentYear : latestGeneration + 1;

  // Add the new year only if it doesn't already exist
  if (!generations.value.includes(newYear)) {
    generations.value.push(newYear);
    // Sort the generations array from newest to oldest
    generations.value.sort((a, b) => b - a);
  }
  selectedGeneration.value = newYear;
};

// Fetch students from API
const fetchStudents = async () => {
  try {
    loading.value = true;
    const token = localStorage.getItem('authToken');
    const res = await axios.get('http://127.0.0.1:8000/api/educator/students', {
      headers: { Authorization: `Bearer ${token}` }
    });
    students.value = res.data.students;
    const uniqueGen = [...new Set(students.value.map(s => s.generation))];
    generations.value = uniqueGen.length > 0 ? uniqueGen : [new Date().getFullYear()];
    
    // Sort generations from newest to oldest
    generations.value.sort((a, b) => b - a);

    if (!selectedGeneration.value) {
      selectedGeneration.value = generations.value[0];
    }
  } catch (err) {
    errorMessage.value = 'Failed to fetch students';
    setTimeout(() => errorMessage.value = '', 3000);
  } finally {
    loading.value = false;
  }
};

const filteredStudents = computed(() => {
  if (!selectedGeneration.value) return [];
  return students.value.filter(s => s.generation === selectedGeneration.value);
});

// Sort the visible generations in descending order for the sidebar list
const visibleGenerations = computed(() => {
  const sortedGenerations = generations.value.slice().sort((a, b) => b - a);
  return showAllGenerations.value ? sortedGenerations : sortedGenerations.slice(0, maxVisibleGenerations.value);
});

function toggleShowAllGenerations() { showAllGenerations.value = !showAllGenerations.value; }
function selectGeneration(year) { selectedGeneration.value = year; }

// Edit student
function editStudent(student) {
  editForm.value = { ...student, password: '', password_confirmation: '' };
  showEditModal.value = true;
}

// Update student
async function updateStudent() {
  try {
    updating.value = true;
    const token = localStorage.getItem('authToken');
    const data = { ...editForm.value };
    if (!data.password) { delete data.password; delete data.password_confirmation; }
    const res = await axios.put(`http://127.0.0.1:8000/api/educator/students/${editForm.value.id}`, data, {
      headers: { Authorization: `Bearer ${token}` }
    });
    const idx = students.value.findIndex(s => s.id === editForm.value.id);
    if (idx !== -1) students.value[idx] = { ...students.value[idx], ...res.data.student };
    showEditModal.value = false;
    successMessage.value = 'Student updated successfully';
    setTimeout(() => successMessage.value = '', 3000);
  } catch (err) {
    errorMessage.value = err.response?.data?.message || 'Failed to update student';
    setTimeout(() => errorMessage.value = '', 3000);
  } finally {
    updating.value = false;
  }
}

// Delete student with a loading state and delay
async function deleteStudent(studentId) {
  deleting.value = true;
  try {
    const token = localStorage.getItem('authToken');
    
    await new Promise(resolve => setTimeout(resolve, 2000));
    
    await axios.delete(`http://127.0.0.1:8000/api/educator/students/${studentId}`, {
      headers: { Authorization: `Bearer ${token}` }
    });

    students.value = students.value.filter(s => s.id !== studentId);
    const uniqueGen = [...new Set(students.value.map(s => s.generation))];
    generations.value = uniqueGen.length > 0 ? uniqueGen : [new Date().getFullYear()];
    generations.value.sort((a, b) => b - a);
    successMessage.value = 'Student deleted successfully';
    setTimeout(() => successMessage.value = '', 2000);
  } catch (err) {
    errorMessage.value = err.response?.data?.message || 'Failed to delete student';
    setTimeout(() => errorMessage.value = '', 3000);
  } finally {
    deleting.value = false;
  }
}

onMounted(fetchStudents);
</script>