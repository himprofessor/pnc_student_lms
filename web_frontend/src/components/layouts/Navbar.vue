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
      <div class="flex items-center space-x-2 border border-blue-500 px-3 py-1 rounded-full cursor-pointer hover:bg-blue-50 transition">
        <div class="w-7 h-7 rounded-full bg-blue-600 text-white flex items-center justify-center font-semibold text-sm">J</div>
        <span class="text-gray-800 font-medium text-sm">John Doe</span>
        <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
        </svg>
      </div>
 <!-- Dropdown menu -->
 <div v-if="dropdownOpen" class="absolute right-0 mt-2 w-40 bg-white rounded-md shadow-lg border border-gray-200 z-50">
        <router-link to="/profile" class="block px-4 py-2 text-gray-700 hover:bg-gray-100 rounded-t-md">Profile</router-link>
        <button @click="handleSignOut" class="block w-full text-left px-4 py-2 text-gray-700 hover:bg-gray-100 rounded-b-md">
  Sign out
</button>

      </div>
    </div>
  </nav>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'

const router = useRouter()
const dropdownOpen = ref(false)

const toggleDropdown = () => {
  dropdownOpen.value = !dropdownOpen.value
}

const handleSignOut = () => {
  localStorage.removeItem('token') // remove token
  router.push('/login') // redirect to login page
}
</script>



