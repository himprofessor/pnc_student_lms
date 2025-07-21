<template>
  <nav class="flex items-center justify-between px-6 py-3 shadow bg-white">
    <!-- Logo -->
    <div class="flex items-center space-x-2">
      <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" d="M12 14l9-5-9-5-9 5 9 5z" />
        <path stroke-linecap="round" stroke-linejoin="round" d="M12 14l6.16-3.422A12.042 12.042 0 0118 14.5C18 17.538 15.538 20 12.5 20S7 17.538 7 14.5c0-.697.096-1.374.273-2.017L12 14z" />
      </svg>
      <span class="text-lg font-semibold text-gray-800">LeaveMS</span>
    </div>

    <!-- Menu -->
    <div class="flex space-x-6 items-center">
      <router-link to="/dashboard" class="text-gray-700 hover:bg-blue-300 px-3 py-1 rounded-md transition">Dashboard</router-link>
      <router-link to="/request-leave" class="text-gray-700 hover:bg-blue-300 px-3 py-1 rounded-md transition">Request Leave</router-link>
      <router-link to="/history" class="text-gray-700 hover:bg-blue-300 px-3 py-1 rounded-md transition">History</router-link>
    </div>

    <!-- Debug Info (remove in production) -->
    <div class="text-xs text-gray-500 mr-4 bg-yellow-100 p-2 rounded">
      <div>Token: {{ hasToken ? 'Yes' : 'No' }}</div>
      <div>User: {{ user ? 'Loaded' : 'None' }}</div>
      <div>Loading: {{ isLoading ? 'Yes' : 'No' }}</div>
      <div>Error: {{ apiError || 'None' }}</div>
      <button @click="manualFetch" class="bg-blue-500 text-white px-2 py-1 rounded text-xs mt-1">
        Retry Fetch
      </button>
    </div>

    <!-- User Dropdown -->
    <div class="relative" @click="toggleDropdown">
      <div class="flex items-center space-x-2 border border-blue-500 px-3 py-1 rounded-full cursor-pointer hover:bg-blue-50 transition">
        <div class="w-7 h-7 rounded-full bg-blue-600 text-white flex items-center justify-center font-semibold text-sm">
          {{ getUserInitials() }}
        </div>
        <span class="text-gray-800 font-medium text-sm">
          {{ userName }}
        </span>
        <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
        </svg>
      </div>

      <!-- Dropdown menu -->
      <div v-if="dropdownOpen" class="absolute right-0 mt-2 w-40 bg-white rounded-md shadow-lg border border-gray-200 z-50">
        <router-link to="/profile" class="block px-4 py-2 text-gray-700 hover:bg-gray-100 rounded-t-md">Profile</router-link>
        <button @click="handleSignOut" class="block w-full text-left px-4 py-2 text-gray-700 hover:bg-gray-100 rounded-b-md">Sign out</button>
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
  console.log('=== USERNAME COMPUTED ===')
  console.log('isLoading:', isLoading.value)
  console.log('user object:', user.value)
  console.log('apiError:', apiError.value)
  
  if (isLoading.value) return 'Loading...'
  if (apiError.value) return 'Error'
  if (!user.value) {
    console.log('No user data available')
    return 'Guest'
  }
  
  // Try different possible name fields
  const possibleNames = [
    user.value.name,
    user.value.full_name,
    user.value.first_name,
    user.value.username,
    user.value.email
  ]
  
  console.log('Possible names:', possibleNames)
  
  const foundName = possibleNames.find(name => name && name.trim())
  console.log('Selected name:', foundName)
  
  return foundName || 'User'
})

const toggleDropdown = () => {
  dropdownOpen.value = !dropdownOpen.value
}

const getUserInitials = () => {
  if (!user.value) return 'G'
  
  const name = user.value.name || user.value.full_name || user.value.email || 'Guest'
  
  if (name.includes('@')) {
    return name.charAt(0).toUpperCase()
  }
  
  return name
    .split(' ')
    .map(word => word.charAt(0).toUpperCase())
    .slice(0, 2)
    .join('')
}

const fetchUser = async () => {
  const token = localStorage.getItem('token')
  
  console.log('=== NAVBAR FETCH USER ===')
  console.log('Token exists:', !!token)
  console.log('Token value (first 20 chars):', token ? token.substring(0, 20) + '...' : 'null')
  
  if (!token) {
    console.log('No token, redirecting to login')
    apiError.value = 'No token'
    router.push('/login')
    return
  }

  isLoading.value = true
  apiError.value = null
  
  try {
    console.log('Making API request to /api/user...')
    console.log('Request URL: http://localhost:8000/api/user')
    console.log('Request headers:', {
      'Authorization': `Bearer ${token.substring(0, 20)}...`,
      'Accept': 'application/json',
      'Content-Type': 'application/json'
    })
    
    const response = await axios.get('http://localhost:8000/api/users', {
      headers: {
        'Authorization': `Bearer ${token}`,
        'Accept': 'application/json',
        'Content-Type': 'application/json'
      }
    })
    
    console.log('API Response Status:', response.status)
    console.log('API Response Headers:', response.headers)
    console.log('API Response Data:', response.data)
    
    user.value = response.data
    localStorage.setItem('user_data', JSON.stringify(response.data))
    
    console.log('User data set successfully:', user.value)
    
  } catch (error) {
    console.error('=== NAVBAR API ERROR ===')
    console.error('Error object:', error)
    console.error('Error message:', error.message)
    console.error('Response status:', error.response?.status)
    console.error('Response data:', error.response?.data)
    console.error('Response headers:', error.response?.headers)
    console.error('Request config:', error.config)
    
    apiError.value = `${error.response?.status || 'Network'}: ${error.response?.data?.message || error.message}`
    
    if (error.response?.status === 401) {
      console.log('Unauthorized, clearing storage')
      localStorage.removeItem('token')
      localStorage.removeItem('user_data')
      router.push('/login')
    }
  } finally {
    isLoading.value = false
  }
}

const manualFetch = async () => {
  console.log('Manual fetch triggered')
  await fetchUser()
}

const handleSignOut = () => {
  localStorage.removeItem('token')
  localStorage.removeItem('user_data')
  user.value = null
  router.push('/login')
}

onMounted(async () => {
  console.log('=== NAVBAR MOUNTED ===')
  
  // First load from localStorage
  const storedUser = localStorage.getItem('user_data')
  console.log('Stored user data:', storedUser)
  
  if (storedUser) {
    try {
      user.value = JSON.parse(storedUser)
      console.log('Loaded user from storage:', user.value)
    } catch (e) {
      console.error('Error parsing stored user:', e)
      localStorage.removeItem('user_data')
    }
  }
  
  // Then fetch fresh data
  await fetchUser()
})
</script>
