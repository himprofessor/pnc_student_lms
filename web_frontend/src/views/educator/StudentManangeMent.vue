<template>
  <div class="flex h-screen bg-gray-100">
    <!-- Sidebar -->
    <aside class="w-64 bg-white shadow-md p-4 flex flex-col">
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
        <div
          v-if="showDropdown"
          class="absolute mt-2 w-full bg-white border rounded-lg shadow-md z-50"
        >
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
          <li v-for="year in visibleGenerations" :key="year" class="mb-2">
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

    <!-- Main Content -->
    <main class="flex-1 p-8 overflow-y-auto">
      <header class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold">Students {{ selectedGeneration }}</h1>
        <div class="flex space-x-4">
          <router-link
            :to="`/create-account?generation=${selectedGeneration}`"
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

      <!-- Students Table -->
      <div class="bg-white shadow-md rounded-lg p-6">
        <table class="min-w-full">
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
            <tr
              v-for="(student, index) in filteredStudents"
              :key="student.id"
            >
              <!-- FIXED: ID starts from 1 for each generation -->
              <td class="py-2 px-4 border-b">{{ index + 1 }}</td>
              <td class="py-2 px-4 border-b">{{ student.name }}</td>
              <td class="py-2 px-4 border-b">{{ student.email }}</td>
              <td class="py-2 px-4 border-b">{{ student.generation }}</td>
              <td class="py-2 px-4 border-b">
                <button
                  @click="editStudent(student.id)"
                  class="text-blue-500 hover:underline mr-2"
                >
                  Edit
                </button>
                <button
                  @click="deleteStudent(student.id)"
                  class="text-red-500 hover:underline"
                >
                  Delete
                </button>
              </td>
            </tr>
          </tbody>
        </table>
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
const maxVisibleGenerations = ref(5);
const showAllGenerations = ref(false);
const showDropdown = ref(false);

// Predefined Years
const availableYears = [2025, 2026, 2027, 2028, 2029, 2030];

// Fetch Students Data
const fetchStudents = async () => {
  try {
    const token = localStorage.getItem('authToken');
    const response = await axios.get(
      'http://127.0.0.1:8000/api/educator/students',
      {
        headers: {
          Authorization: `Bearer ${token}`,
        },
      }
    );

    students.value = response.data.students;

    // Extract unique generations from students
    const uniqueGenerations = [
      ...new Set(students.value.map((student) => student.generation)),
    ].sort();

    generations.value =
      uniqueGenerations.length > 0
        ? uniqueGenerations
        : [new Date().getFullYear()];

    if (generations.value.length > 0 && !selectedGeneration.value) {
      selectedGeneration.value = Math.max(...generations.value);
    }
  } catch (error) {
    console.error('Error fetching students:', error);
  }
};

// Filter students by selected generation
const filteredStudents = computed(() => {
  if (!selectedGeneration.value) return [];
  return students.value
    .filter((student) => student.generation === selectedGeneration.value)
    .sort((a, b) => a.id - b.id);
});

const visibleGenerations = computed(() => {
  return showAllGenerations.value
    ? generations.value
    : generations.value.slice(0, maxVisibleGenerations.value);
});

function toggleShowAllGenerations() {
  showAllGenerations.value = !showAllGenerations.value;
}

function selectGeneration(year) {
  selectedGeneration.value = year;
}

function toggleDropdown() {
  showDropdown.value = !showDropdown.value;
}

function goToCreateForm(year) {
  selectedGeneration.value = year;
  showDropdown.value = false;
  router.push(`/create-account?generation=${year}`);
}

function editStudent(studentId) {
  console.log(`Editing student with ID: ${studentId}`);
}

async function deleteStudent(studentId) {
  if (
    confirm(`Are you sure you want to delete student with ID: ${studentId}?`)
  ) {
    try {
      const token = localStorage.getItem('authToken');
      await axios.delete(
        `http://127.0.0.1:8000/api/educator/students/${studentId}`,
        {
          headers: {
            Authorization: `Bearer ${token}`,
          },
        }
      );
      students.value = students.value.filter(
        (student) => student.id !== studentId
      );
    } catch (error) {
      console.error('Error deleting student:', error);
      alert('Failed to delete student');
    }
  }
}

onMounted(() => {
  fetchStudents();
});
</script>
