<template>
  <nav class="flex items-center justify-between px-6 py-3 shadow bg-white">
    <!-- Logo -->
    <router-link to="/" class="flex items-center space-x-2 hover:opacity-80 transition-opacity">
      <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" d="M12 14l9-5-9-5-9 5 9 5z" />
        <path stroke-linecap="round" stroke-linejoin="round" d="M12 14l6.16-3.422A12.042 12.042 0 0118 14.5C18 17.538 15.538 20 12.5 20S7 17.538 7 14.5c0-.697.096-1.374.273-2.017L12 14z" />
      </svg>
      <span class="text-lg font-semibold text-gray-800">LeaveMS</span>
    </router-link>

    <!-- Menu -->
    <div class="flex space-x-4 items-center">
      <router-link 
        v-for="link in navLinks"
        :key="link.to"
        :to="link.to" 
        class="px-3 py-1 rounded-md transition"
        :class="{
          'text-blue-600 font-medium': $route.path === link.to,
          'text-gray-700 hover:bg-blue-50': $route.path !== link.to
        }"
      >
        {{ link.text }}
      </router-link>
    </div>

    <!-- User Dropdown -->
    <div class="relative" v-click-outside="closeDropdown">
      <div 
        @click="toggleDropdown"
        class="flex items-center space-x-2 px-3 py-1 rounded-md cursor-pointer hover:bg-blue-50 transition"
      >
        <div class="w-7 h-7 rounded-full overflow-hidden border border-gray-200 flex items-center justify-center">
          <img 
            v-if="getProfileImageUrl()" 
            :src="getProfileImageUrl()" 
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
        <svg 
          class="w-4 h-4 text-gray-500 transition-transform duration-200"
          :class="{'rotate-180': dropdownOpen}"
          fill="none" 
          stroke="currentColor" 
          viewBox="0 0 24 24"
        >
          <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
        </svg>
      </div>

      <!-- Dropdown menu -->
      <transition
        enter-active-class="transition ease-out duration-100"
        enter-from-class="transform opacity-0 scale-95"
        enter-to-class="transform opacity-100 scale-100"
        leave-active-class="transition ease-in duration-75"
        leave-from-class="transform opacity-100 scale-100"
        leave-to-class="transform opacity-0 scale-95"
      >
        <div 
          v-if="dropdownOpen" 
          class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg border border-gray-200 z-50 divide-y divide-gray-100"
        >
          <div class="px-4 py-3">
            <p class="text-sm text-gray-900 font-medium">{{ displayName }}</p>
            <p class="text-xs text-gray-500 truncate">{{ user?.email }}</p>
          </div>
          <div class="py-1">
            <router-link 
              to="/profile" 
              @click="closeDropdown"
              class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50"
            >
              Profile
            </router-link>
            <button 
              @click="handleSignOut"
              class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-blue-50"
            >
              Sign out
            </button>
          </div>
        </div>
      </transition>
    </div>
  </nav>
</template>

<script setup>
import { ref, onMounted, onUnmounted, computed } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import axios from 'axios'

const router = useRouter()
const route = useRoute()
const dropdownOpen = ref(false)
const user = ref(JSON.parse(localStorage.getItem('user_data')) || null)
const imageError = ref(false)

const navLinks = [
  { to: '/dashboard', text: 'Dashboard' },
  { to: '/request-leave', text: 'Request Leave' },
  { to: '/history', text: 'History' }
]

const displayName = computed(() => {
  if (!user.value) return 'Loading...'
  return user.value.name || 
         user.value.full_name || 
         user.value.first_name || 
         (user.value.email ? user.value.email.split('@')[0] : 'User')
})

const toggleDropdown = () => {
  dropdownOpen.value = !dropdownOpen.value
}

const closeDropdown = () => {
  dropdownOpen.value = false
}

const getUserInitials = () => {
  if (!user.value) return 'U'
  const name = displayName.value
  return name.split(' ').map(n => n[0]).join('').toUpperCase().substring(0, 2)
}

const getProfileImageUrl = () => {
  if (!user.value || imageError.value) return null
  const imgField = user.value.img_url || user.value.profile_image || user.value.avatar
  if (!imgField) return null
  
  if (imgField.startsWith('http')) return imgField
  return `http://127.0.0.1:8000${imgField.startsWith('/') ? '' : '/'}${imgField}`
}

const handleImageError = () => {
  imageError.value = true
}

const fetchUser = async () => {
  const token = localStorage.getItem('authToken')
  if (!token) return

  try {
    const { data } = await axios.get('http://127.0.0.1:8000/api/user', {
      headers: { 'Authorization': `Bearer ${token}` }
    })
    user.value = data
    localStorage.setItem('user_data', JSON.stringify(data))
    imageError.value = false
  } catch (error) {
    if (error.response?.status === 401) {
      handleSignOut()
    }
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
      console.error('Logout error:', error)
    }
  }
  localStorage.removeItem('authToken')
  localStorage.removeItem('user_data')
  user.value = null
  router.push('/login')
}

const handleUserUpdate = (event) => {
  if (event.detail) {
    user.value = event.detail
    localStorage.setItem('user_data', JSON.stringify(event.detail))
  }
}

onMounted(() => {
  window.addEventListener('userUpdated', handleUserUpdate)
  if (localStorage.getItem('authToken') && !user.value) {
    fetchUser()
  }
})

onUnmounted(() => {
  window.removeEventListener('userUpdated', handleUserUpdate)
})

// Click outside directive
const vClickOutside = {
  beforeMount(el, binding) {
    el.clickOutsideEvent = event => {
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
</script>