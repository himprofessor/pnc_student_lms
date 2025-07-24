<template>
  <div class="teacher-dashboard">
    <h1>Teacher Dashboard</h1>
    <p>Welcome, {{ teacherName }}!</p>
    
    <div v-if="loading">Loading data...</div>
    <div v-if="error" class="error">{{ error }}</div>
    
    <div v-if="data">
      <h2>Your Classes</h2>
      <ul>
        <li v-for="classItem in data.classes" :key="classItem.id">
          {{ classItem.name }}
        </li>
      </ul>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import axios from 'axios'

const teacherName = ref('') // To store the teacher's name
const data = ref(null) // To store teacher-specific data
const loading = ref(true) // To indicate loading state
const error = ref(null) // To store error messages
const router = useRouter()

const fetchTeacherData = async () => {
  const token = localStorage.getItem('authToken')

  if (!token) {
    error.value = 'Unauthorized access. Please log in.'
    return router.push('/login') // Redirect to login if no token is found
  }

  try {
    const response = await axios.get('http://127.0.0.1:8000/api/teacher/dashboard', {
      headers: {
        Authorization: `Bearer ${token}`,
      },
    })

    // Assuming the response contains teacher's name and classes
    teacherName.value = response.data.teacherName
    data.value = response.data // Store the data for rendering
  } catch (err) {
    console.error('Failed to fetch teacher data:', err)
    error.value = 'Failed to load data'
  } finally {
    loading.value = false // Reset loading state
  }
}

// Fetch teacher data when the component is mounted
onMounted(() => {
  fetchTeacherData()
})
</script>

<style scoped>
.error {
  color: red;
}
</style>