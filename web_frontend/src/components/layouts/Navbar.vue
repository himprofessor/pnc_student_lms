<template>
  <nav class="flex items-center justify-between px-6 py-3 shadow bg-white">
    <!-- Logo -->
    <div class="flex items-center space-x-3">
      <img src="@/assets/logo.svg" alt="LMS Logo" class="w-8 h-8" />
      <span class="text-xl font-bold text-gray-800">Student LMS</span>
    </div>

    <!-- Menu -->
    <div class="flex space-x-6 items-center">
      <router-link to="/dashboard" class="text-gray-700 hover:bg-blue-100 px-3 py-2 rounded-md transition font-medium">
        Dashboard
      </router-link>
      <router-link to="/request-leave" class="text-gray-700 hover:bg-blue-100 px-3 py-2 rounded-md transition font-medium">
        Request Leave
      </router-link>
      <router-link to="/history" class="text-gray-700 hover:bg-blue-100 px-3 py-2 rounded-md transition font-medium">
        History
      </router-link>
    </div>

    <!-- User Dropdown -->
    <div class="relative">
      <div 
        @click="toggleDropdown" 
        class="flex items-center space-x-3 border border-blue-500 px-4 py-2 rounded-full cursor-pointer hover:bg-blue-50 transition-all duration-200"
      >
        <div class="w-8 h-8 rounded-full bg-gradient-to-r from-blue-500 to-blue-600 text-white flex items-center justify-center font-bold text-sm shadow-md">
          {{ getUserInitials() }}
        </div>
        <div class="flex flex-col items-start">
          <span class="text-gray-800 font-semibold text-sm">
            {{ userName }}
          </span>
          <span class="text-gray-500 text-xs">
            {{ userRole }}
          </span>
        </div>
        <svg 
          class="w-4 h-4 text-gray-600 transition-transform duration-200" 
          :class="{ 'rotate-180': dropdownOpen }"
          fill="none" 
          stroke="currentColor" 
          stroke-width="2" 
          viewBox="0 0 24 24"
        >
          <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
        </svg>
      </div>

      <!-- Dropdown menu -->
      <div 
        v-if="dropdownOpen" 
        class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-200 z-50 py-1"
      >
        <div class="px-4 py-2 border-b border-gray-100">
          <p class="text-sm font-semibold text-gray-800">{{ userName }}</p>
          <p class="text-xs text-gray-500">{{ userEmail }}</p>
        </div>
        <router-link 
          to="/profile" 
          class="flex items-center px-4 py-2 text-gray-700 hover:bg-gray-100 transition-colors"
          @click="dropdownOpen = false"
        >
          <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
          </svg>
          Profile Settings
        </router-link>
        <button 
          @click="handleSignOut" 
          class="flex items-center w-full px-4 py-2 text-gray-700 hover:bg-red-50 hover:text-red-600 transition-colors"
        >
          <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
          </svg>
          Sign Out
        </button>
      </div>
    </div>

    <!-- Loading Overlay -->
    <div v-if="isLoading" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
      <div class="bg-white p-6 rounded-lg shadow-xl">
        <div class="flex items-center space-x-3">
          <div class="animate-spin rounded-full h-6 w-6 border-b-2 border-blue-600"></div>
          <span class="text-gray-700">Loading user data...</span>
        </div>
      </div>
    </div>
  </nav>
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

const hasToken = computed(() => !!localStorage.getItem('token'))

const userName = computed(() => {
  if (isLoading.value) return 'Loading...'
  if (apiError.value) return 'Error Loading'
  if (!user.value) return 'Guest User'
  
  return user.value.name || user.value.full_name || user.value.email?.split('@')[0] || 'User'
})

const userEmail = computed(() => {
  if (!user.value) return 'No email'
  return user.value.email || 'No email provided'
})

const userRole = computed(() => {
  if (!user.value) return 'Guest'
  return user.value.role || user.value.role_name || 'Student'
})

const toggleDropdown = () => {
  dropdownOpen.value = !dropdownOpen.value
}

// Close dropdown when clicking outside
const closeDropdown = (event) => {
  if (!event.target.closest('.relative')) {
    dropdownOpen.value = false
  }
}

onMounted(() => {
  document.addEventListener('click', closeDropdown)
})

const getUserInitials = () => {
  if (!user.value) return 'G'
  
  const name = user.value.name || user.value.full_name || user.value.email || 'Guest User'
  
  if (name.includes('@')) {
    return name.charAt(0).toUpperCase()
  }
  
  const words = name.split(' ')
  if (words.length >= 2) {
    return (words[0].charAt(0) + words[1].charAt(0)).toUpperCase()
  }
  
  return name.charAt(0).toUpperCase()
}

const fetchUser = async () => {
  const token = localStorage.getItem('token')
  
  console.log('=== FETCHING USER DATA ===')
  console.log('Token exists:', !!token)
  
  if (!token) {
    console.log('No token found, redirecting to login')
    router.push('/login')
    return
  }

  isLoading.value = true
  apiError.value = null
  
  try {
    console.log('Making API request to fetch user...')
    
    const response = await axios.get('http://localhost:8000/api/user', {
      headers: {
        'Authorization': `Bearer ${token}`,
        'Accept': 'application/json',
        'Content-Type': 'application/json'
      }
    })
    
    console.log('User data received:', response.data)
    
    user.value = response.data
    localStorage.setItem('user_data', JSON.stringify(response.data))
    
    // Emit user data to other components if needed
    window.dispatchEvent(new CustomEvent('userDataLoaded', { detail: response.data }))
    
  } catch (error) {
    console.error('Failed to fetch user data:', error)
    apiError.value = error.response?.data?.message || error.message
    
    if (error.response?.status === 401) {
      console.log('Token expired, clearing storage')
      localStorage.removeItem('token')
      localStorage.removeItem('user_data')
      router.push('/login')
    }
  } finally {
    isLoading.value = false
  }
}

const handleSignOut = () => {
  if (confirm('Are you sure you want to sign out?')) {
    localStorage.removeItem('token')
    localStorage.removeItem('user_data')
    user.value = null
    dropdownOpen.value = false
    router.push('/login')
  }
}

onMounted(async () => {
  console.log('Navbar component mounted')
  
  // Try to load user from localStorage first for immediate display
  const storedUser = localStorage.getItem('user_data')
  if (storedUser) {
    try {
      user.value = JSON.parse(storedUser)
      console.log('Loaded user from localStorage:', user.value)
    } catch (e) {
      console.error('Error parsing stored user data:', e)
      localStorage.removeItem('user_data')
    }
  }
  
  // Fetch fresh user data from API
  await fetchUser()
})
</script>
