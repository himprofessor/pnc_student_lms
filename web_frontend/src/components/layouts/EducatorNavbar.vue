<template>
  <header class="bg-white shadow-sm py-3  border-b border-gray-100">
    <div class="max-w-8xl mx-auto flex justify-between items-center">
      <!-- Logo with subtle background -->
      <router-link to="/" class="ml-5 flex items-center space-x-3 hover:opacity-90 transition-opacity">


        <div class="bg-blue-50 p-2 rounded-lg">
          <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 14l9-5-9-5-9 5 9 5z" />
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 14l6.16-3.422A12.042 12.042 0 0118 14.5C18 17.538 15.538 20 12.5 20S7 17.538 7 14.5c0-.697.096-1.374.273-2.017L12 14z" />
          </svg>
        </div>
        <span class="text-xl font-bold text-gray-800">LeaveMS</span>
      </router-link>

      <!-- Navigation with pill-shaped active state -->
      <nav>
        <ul class="flex space-x-1">
          <li>
            <router-link
              to="/educator-dashboard"
              class="px-4 py-2 text-gray-600 font-medium rounded-lg transition-all duration-200 hover:bg-gray-100 hover:shadow-sm"
              active-class="bg-blue-50 text-blue-600 shadow-inner"
              exact-active-class="bg-blue-50 text-blue-600 shadow-inner"
              @click.native="closeDropdown"
            >
              Dashboard
            </router-link>
          </li>
          <li>
            <router-link
              to="/educator-history"
              class="px-4 py-2 text-gray-600 font-medium rounded-lg transition-all duration-200 hover:bg-gray-100 hover:shadow-sm"
              active-class="bg-blue-50 text-blue-600 shadow-inner"
              exact-active-class="bg-blue-50 text-blue-600 shadow-inner"
              @click.native="closeDropdown"
            >
              History
            </router-link>
          </li>
        </ul>
      </nav>

      <!-- User dropdown with card-style design -->
      <div class="relative" @click.stop="toggleDropdown">
        <div class="flex items-center space-x-2 px-3 py-2 rounded-lg cursor-pointer hover:bg-gray-50 transition-all duration-200 border border-transparent hover:border-gray-200 hover:shadow-sm">
          <div class="relative">
            <div class="w-8 h-8 rounded-full overflow-hidden border-2 border-white shadow-sm">
              <img 
                v-if="getProfileImageUrl()" 
                :src="getProfileImageUrl()" 
                :alt="displayName"
                class="w-full h-full object-cover"
                @error="handleImageError"
              />
              <div 
                v-else 
                class="w-full h-full bg-gradient-to-br from-blue-500 to-blue-600 text-white flex items-center justify-center font-semibold text-sm"
              >
                {{ getUserInitials() }}
              </div>
            </div>
            <span class="absolute -bottom-1 -right-1 w-3 h-3 bg-green-400 rounded-full border-2 border-white"></span>
          </div>
          <span class="text-gray-700 font-medium text-sm hidden md:inline-block">
            {{ displayName }}
          </span>
          <svg class="w-4 h-4 text-gray-500 transform transition-transform duration-200" :class="{'rotate-180': dropdownOpen}" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
          </svg>
        </div>

        <!-- Dropdown menu with subtle shadow -->
        <transition
          enter-active-class="transition duration-100 ease-out"
          enter-from-class="transform scale-95 opacity-0"
          enter-to-class="transform scale-100 opacity-100"
          leave-active-class="transition duration-75 ease-in"
          leave-from-class="transform scale-100 opacity-100"
          leave-to-class="transform scale-95 opacity-0"
        >
          <div 
            v-if="dropdownOpen" 
            ref="dropdownMenu"
            class="absolute right-0 mt-2 w-56 bg-white rounded-lg shadow-lg border border-gray-100 z-50 overflow-hidden py-1"
            v-click-outside="closeDropdown"
          >
            <div class="px-4 py-3 border-b border-gray-100 bg-gray-50">
              <p class="text-sm font-medium text-gray-700">Signed in as</p>
              <p class="text-sm text-gray-500 truncate">{{ user?.email || 'user@example.com' }}</p>
            </div>
            <router-link 
              to="/educator-profile" 
              class="block px-4 py-2.5 text-gray-700 hover:bg-blue-50 transition-all duration-150 flex items-center space-x-3"
              @click.native="closeDropdown"
            >
              <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
              </svg>
              <span>Your Profile</span>
            </router-link>
            <button 
              @click="handleSignOut" 
              class="w-full text-left px-4 py-2.5 text-gray-700 hover:bg-blue-50 transition-all duration-150 flex items-center space-x-3 border-t border-gray-100"
            >
              <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
              </svg>
              <span>Sign out</span>
            </button>
          </div>
        </transition>
      </div>
    </div>
  </header>
</template>

<script setup>
import { ref, onMounted, computed, watch } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import axios from 'axios'

const router = useRouter()
const route = useRoute()
const dropdownOpen = ref(false)
const dropdownMenu = ref(null)
const user = ref(null)
const isLoading = ref(false)
const apiError = ref(null)
const imageError = ref(false)

// Close dropdown when route changes
watch(() => route.path, () => {
  closeDropdown()
})

const closeDropdown = () => {
  dropdownOpen.value = false
}

const toggleDropdown = () => {
  dropdownOpen.value = !dropdownOpen.value
}

// Click outside directive
const vClickOutside = {
  beforeMount(el, binding) {
    el.clickOutsideEvent = function(event) {
      if (!(el === event.target || el.contains(event.target))) {
        binding.value()
      }
    }
    document.addEventListener('click', el.clickOutsideEvent)
  },
  unmounted(el) {
    document.removeEventListener('click', el.clickOutsideEvent)
  }
}

// Rest of your existing script...
const hasToken = computed(() => !!localStorage.getItem('authToken'))

const displayName = computed(() => {
  if (isLoading.value) return 'Loading...'
  if (apiError.value) return 'Error'
  if (!user.value) return 'Guest'
  return user.value.name || user.value.full_name || user.value.first_name ||
         (user.value.email ? user.value.email.split('@')[0] : null) || 'User'
})

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
  
  const imageField = user.value.img_url ||
                    user.value.img || 
                    user.value.profile_image || 
                    user.value.avatar || 
                    user.value.image || 
                    user.value.photo ||
                    user.value.profile_photo

  if (!imageField) {
    console.log('No image field found')
    return null
  }

  let finalUrl = ''

  if (imageField.startsWith('http://') || imageField.startsWith('https://')) {
    finalUrl = imageField
  } else if (imageField.startsWith('/')) {
    finalUrl = `http://127.0.0.1:8000${imageField}`
  } else {
    finalUrl = `http://127.0.0.1:8000/storage/${imageField}`
  }

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
  imageError.value = false
  
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