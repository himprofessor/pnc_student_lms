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

    <!-- User Dropdown -->
    <div class="relative" @click="toggleDropdown">
      <div class="flex items-center space-x-2 px-3 py-1 rounded-md cursor-pointer hover:bg-blue-50 transition">
        <!-- Profile Image or Initials -->
        <div class="w-7 h-7 rounded-full overflow-hidden flex items-center justify-center">
          <img 
            v-if="getProfileImageUrl()" 
            :src="getProfileImageUrl()" 
            :alt="displayName"
            class="w-full h-full object-cover"
            @error="handleImageError"
          />
          <div 
            v-else 
            class="w-full h-full bg-blue-600 text-white flex items-center justify-center font-semibold text-sm"
          >
            {{ getUserInitials() }}
          </div>
        </div>
        <span class="text-gray-800 font-medium text-sm">
          {{ displayName }}
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
const imageError = ref(false)

const hasToken = computed(() => !!localStorage.getItem('authToken'))

const displayName = computed(() => {
  if (isLoading.value) return 'Loading...'
  if (apiError.value) return 'Error'
  if (!user.value) return 'Guest'
  
  return user.value.name || 
         user.value.full_name || 
         user.value.first_name || 
         (user.value.email ? user.value.email.split('@')[0] : null) || 
         'User'
})

const toggleDropdown = () => {
  dropdownOpen.value = !dropdownOpen.value
}

const getUserInitials = () => {
  if (!user.value) return 'G'
  
  const name = user.value.name || 
               user.value.full_name || 
               user.value.first_name || 
               user.value.email || 
               'Guest'
  
  if (name.includes('@')) {
    return name.charAt(0).toUpperCase()
  }
  
  const words = name.split(' ')
  if (words.length >= 2) {
    return (words[0].charAt(0) + words[1].charAt(0)).toUpperCase()
  }
  
  return name.charAt(0).toUpperCase()
}

const getProfileImageUrl = () => {
  if (!user.value || imageError.value) return null
  
  // Check different possible image field names
  const imageField = user.value.img_url ||
                    user.value.img || 
                    user.value.profile_image || 
                    user.value.avatar || 
                    user.value.image || 
                    user.value.photo ||
                    user.value.profile_photo

  console.log('=== NAVBAR IMAGE DEBUG ===')
  console.log('user.value:', user.value)
  console.log('user.value.img:', user.value.img)
  console.log('user.value.img_url:', user.value.img_url)
  console.log('imageField:', imageField)

  if (!imageField) {
    console.log('No image field found')
    return null
  }

  let finalUrl = ''

  // If it's already a full URL, return as is
  if (imageField.startsWith('http://') || imageField.startsWith('https://')) {
    finalUrl = imageField
  } else if (imageField.startsWith('/')) {
    // If it's a relative path, construct the full URL
    finalUrl = `http://127.0.0.1:8000${imageField}`
  } else {
    // If it's just a filename, assume it's in storage/app/public/profile-images
    finalUrl = `http://127.0.0.1:8000/storage/${imageField}`
  }

  console.log('Final image URL:', finalUrl)
  return finalUrl
}

const handleImageError = () => {
  console.log('Image failed to load, falling back to initials')
  imageError.value = true
}

const fetchUser = async () => {
  const token = localStorage.getItem('authToken')
  
  if (!token) {
    router.push('/login')
    return
  }

  isLoading.value = true
  apiError.value = null
  imageError.value = false // Reset image error state
  
  try {
    const response = await axios.get('http://127.0.0.1:8000/api/user', {
      headers: {
        'Authorization': `Bearer ${token}`,
        'Accept': 'application/json',
        'Content-Type': 'application/json'
      }
    })
    
    user.value = response.data
    localStorage.setItem('user_data', JSON.stringify(response.data))
    
    console.log('User loaded successfully:', user.value)
    console.log('Profile image field:', getProfileImageUrl())
    
  } catch (error) {
    console.error('Error fetching user:', error)
    apiError.value = error.response?.data?.message || error.message
    
    if (error.response?.status === 401) {
      localStorage.removeItem('token')
      localStorage.removeItem('user_data')
      router.push('/login')
    }
  } finally {
    isLoading.value = false
  }
}

const handleSignOut = async () => {
  const token = localStorage.getItem('authToken')
  
  // Try to logout on the server first
  if (token) {
    try {
      await axios.post('http://127.0.0.1:8000/api/logout', {}, {
        headers: {
          'Authorization': `Bearer ${token}`,
          'Accept': 'application/json',
          'Content-Type': 'application/json'
        }
      })
    } catch (error) {
      console.error('Logout API error:', error)
    }
  }
  
  // Clear local storage
  localStorage.removeItem('authToken')
  localStorage.removeItem('token')
  localStorage.removeItem('user_data')
  
  // Clear user state
  user.value = null
  
  // Redirect to login
  router.push('/login')
}

// Listen for user data updates from profile page
const handleUserDataUpdate = (event) => {
  if (event.detail) {
    user.value = event.detail
    localStorage.setItem('user_data', JSON.stringify(event.detail))
    imageError.value = false // Reset image error when user data updates
  }
}

onMounted(async () => {
  // Listen for user data updates
  window.addEventListener('userDataUpdated', handleUserDataUpdate)
  
  // Load from localStorage first
  const storedUser = localStorage.getItem('user_data')
  if (storedUser) {
    try {
      user.value = JSON.parse(storedUser)
    } catch (e) {
      localStorage.removeItem('user_data')
    }
  }
  
  // Fetch fresh data
  await fetchUser()
})
</script>
