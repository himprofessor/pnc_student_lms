<template>
  <header class="flex justify-between items-center py-4 px-8 bg-white shadow-sm">
    <!-- Logo -->
    <div class="flex items-center space-x-2">
      <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" d="M12 14l9-5-9-5-9 5 9 5z" />
        <path stroke-linecap="round" stroke-linejoin="round" d="M12 14l6.16-3.422A12.042 12.042 0 0118 14.5C18 17.538 15.538 20 12.5 20S7 17.538 7 14.5c0-.697.096-1.374.273-2.017L12 14z" />
      </svg>
      <span class="text-lg font-semibold text-gray-800">LeaveMS</span>
    </div>

    <!-- Educator Navigation -->
    <nav>
      <ul class="flex space-x-5">
        <li>
          <router-link
            to="/educator-dashboard"
            class="text-gray-600 font-medium pb-1.5 border-b-2 border-transparent hover:text-blue-600 transition-colors duration-200"
            active-class="text-blue-600 border-blue-600"
            exact-active-class="text-blue-600 border-blue-600"
          >
            Dashboard
          </router-link>
        </li>
        <li>
          <router-link
            to="/educator-history"
            class="text-gray-600 font-medium pb-1.5 border-b-2 border-transparent hover:text-blue-600 transition-colors duration-200"
            active-class="text-blue-600 border-blue-600"
            exact-active-class="text-blue-600 border-blue-600"
          >
            History
          </router-link>
        </li>
      </ul>
    </nav>

    <!-- User Section -->
    <div class="relative">
      <button class="flex items-center cursor-pointer" @click="toggleDropdown">
        <span class="bg-blue-600 text-white rounded-full w-8 h-8 flex justify-center items-center mr-2 font-bold text-sm">
          {{ getUserInitials() }}
        </span>
        <span class="text-gray-700">{{ displayName }}</span>
        <i class="ml-1.5 border-solid border-gray-600 border-b-2 border-r-2 p-1 inline-block transform rotate-45"></i>
      </button>

      <!-- Dropdown -->
      <div
        v-if="dropdownOpen"
        class="absolute right-0 mt-2 w-44 bg-white rounded-md shadow-lg ring-1 ring-black ring-opacity-5 z-50"
      >
        <router-link
          to="/educator-profile"
          class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
          @click="dropdownOpen = false"
        >
          Profile
        </router-link>
        <button
          class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-gray-100"
          @click="handleSignOut"
        >
          Sign out
        </button>
      </div>
    </div>
  </header>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import { useRouter } from 'vue-router'
import axios from 'axios'

const router = useRouter()
const dropdownOpen = ref(false)
const user = ref(null)
const isLoading = ref(false)
const apiError = ref(null)
const imageError = ref(false)

const displayName = computed(() => {
  if (isLoading.value) return 'Loading...'
  if (apiError.value) return 'Error'
  if (!user.value) return 'Guest'
  return user.value.name || user.value.full_name || user.value.first_name ||
         (user.value.email ? user.value.email.split('@')[0] : null) || 'User'
})

const toggleDropdown = () => dropdownOpen.value = !dropdownOpen.value

const getUserInitials = () => {
  if (!user.value) return 'G'
  const name = user.value.name || user.value.full_name || user.value.first_name || user.value.email || 'Guest'
  const parts = name.split(' ')
  return parts.length >= 2
    ? (parts[0].charAt(0) + parts[1].charAt(0)).toUpperCase()
    : name.charAt(0).toUpperCase()
}

const fetchUser = async () => {
  const token = localStorage.getItem('authToken')
  if (!token) {
    router.push('/login')
    return
  }

  isLoading.value = true
  apiError.value = null

  try {
    const response = await axios.get('http://127.0.0.1:8000/api/user', {
      headers: { 'Authorization': `Bearer ${token}` }
    })
    user.value = response.data
    localStorage.setItem('user_data', JSON.stringify(response.data))
  } catch (error) {
    apiError.value = error.response?.data?.message || error.message
    if (error.response?.status === 401) {
      localStorage.removeItem('authToken')
      localStorage.removeItem('user_data')
      router.push('/login')
    }
  } finally {
    isLoading.value = false
  }
}

const handleSignOut = async () => {
  const token = localStorage.getItem('authToken')
  if (token) {
    try {
      await axios.post('http://127.0.0.1:8000/api/logout', {}, {
        headers: { 'Authorization': `Bearer ${token}` }
      })
    } catch (error) {
      console.error('Logout API error:', error)
    }
  }
  localStorage.clear()
  user.value = null
  router.push('/login')
}

onMounted(fetchUser)
</script>
