<template>
  <div class="flex h-screen bg-gray-100">
    <aside class="w-64 bg-white shadow-md p-4 flex flex-col">
      <h2 class="text-xl font-bold mb-6">Student Generations</h2>

      <div class="mt-2 mb-6">
        <button
          @click="createNewGeneration"
          class="w-full bg-green-500 text-white font-bold py-2 px-4 rounded-md hover:bg-green-600 transition duration-300"
        >
          + New Generation
        </button>
      </div>

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

    <main class="flex-1 p-8 overflow-y-auto">
      <header class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold">Students {{ selectedGeneration }}</h1>
        <div class="flex space-x-4">
          <router-link
            to="/create-account"
            class="bg-blue-500 text-white font-bold py-2 px-4 rounded-md hover:bg-blue-600 transition duration-300"
          >
            Create Student
          </router-link>
          <router-link
            to="/educator-importdata"
            class="bg-purple-500 text-white font-bold py-2 px-4 rounded-md hover:bg-purple-600 transition duration-300"
          >
            Import Students
          </router-link>
        </div>
      </header>

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
            <tr v-for="student in filteredStudents" :key="student.id">
              <td class="py-2 px-4 border-b">{{ student.id }}</td>
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

const generations = ref([]);
const selectedGeneration = ref(null);
const students = ref([]);
const maxVisibleGenerations = ref(5);
const showAllGenerations = ref(false);

// Fetch students and generations from API
const fetchStudents = async () => {
  try {
    const token = localStorage.getItem('authToken');
    const response = await axios.get('http://127.0.0.1:8000/api/educator/students', {
      headers: {
        'Authorization': `Bearer ${token}`
      }
    });
    students.value = response.data.students;
    
    // Extract unique generations
    const uniqueGenerations = [...new Set(students.value.map(student => student.generation))].sort();
    generations.value = uniqueGenerations.length > 0 ? uniqueGenerations : [new Date().getFullYear()];
    
    // Set default selected generation
    if (generations.value.length > 0 && !selectedGeneration.value) {
      selectedGeneration.value = Math.max(...generations.value);
    }
  } catch (error) {
    console.error('Error fetching students:', error);
  }
};

const filteredStudents = computed(() => {
  if (!selectedGeneration.value) return [];
  return students.value.filter(student => student.generation === selectedGeneration.value);
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

function createNewGeneration() {
  const newYear = generations.value.length > 0
    ? Math.max(...generations.value) + 1
    : new Date().getFullYear();
  generations.value.push(newYear);
  selectedGeneration.value = newYear;
}

function editStudent(studentId) {
  console.log(`Editing student with ID: ${studentId}`);
  // Implement your edit logic here
}

async function deleteStudent(studentId) {
  if (confirm(`Are you sure you want to delete student with ID: ${studentId}?`)) {
    try {
      const token = localStorage.getItem('authToken');
      await axios.delete(`http://127.0.0.1:8000/api/educator/students/${studentId}`, {
        headers: {
          'Authorization': `Bearer ${token}`
        }
      });
      
      // Remove student from local list
      students.value = students.value.filter(student => student.id !== studentId);
      console.log(`Deleted student with ID: ${studentId}`);
    } catch (error) {
      console.error('Error deleting student:', error);
      alert('Failed to delete student');
    }
  }
}

// Add API route to EducatorController for fetching students
// Add this to your EducatorController
/*
public function getStudents(Request $request)
{
    $students = User::where('role_id', 3)
        ->orderBy('generation', 'desc')
        ->orderBy('name')
        ->get();
    
    return response()->json(['students' => $students]);
}
*/

onMounted(() => {
  fetchStudents();
});
</script>