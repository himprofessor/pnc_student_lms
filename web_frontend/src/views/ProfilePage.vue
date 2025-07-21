<template>
  <div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-4xl mx-auto px-4">
      <!-- Header -->
      <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-2">Profile Settings</h1>
        <p class="text-gray-600">Manage your personal information and account settings</p>
      </div>

      <!-- Loading State -->
      <div v-if="isLoadingUser" class="bg-white rounded-lg shadow-md p-8">
        <div class="flex items-center justify-center">
          <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600 mr-3"></div>
          <span class="text-gray-600">Loading your profile...</span>
        </div>
      </div>

      <!-- Error State -->
      <div v-else-if="!user && !isLoadingUser" class="bg-white rounded-lg shadow-md p-8">
        <div class="text-center">
          <h3 class="text-lg font-semibold text-gray-900 mb-2">Failed to Load Profile</h3>
          <p class="text-gray-600 mb-4">{{ debugError || 'Unable to load your profile data' }}</p>
          <button @click="fetchUserData" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700">
            Try Again
          </button>
        </div>
      </div>

      <!-- Profile Content -->
      <div v-else class="space-y-6">
        <!-- Profile Header Card -->
        <div class="bg-white rounded-lg shadow-md p-6">
          <div class="flex items-center space-x-6">
            <div class="w-24 h-24 rounded-full bg-gradient-to-r from-blue-500 to-blue-600 text-white flex items-center justify-center text-3xl font-bold shadow-lg">
              {{ getUserInitials() }}
            </div>
            <div class="flex-1">
              <h2 class="text-3xl font-bold text-gray-900">{{ user.name || user.full_name || 'N/A' }}</h2>
              <p class="text-gray-600 text-lg mb-2">{{ user.email || 'N/A' }}</p>
              
               
             
            </div>
            <div class="text-right text-sm text-gray-500">
              <p>Member since</p>
              <p class="font-semibold">{{ formatDate(user.created_at) }}</p>
            </div>
          </div>
        </div>

        <!-- Account Information Card -->
        <div class="bg-white rounded-lg shadow-md p-6">
          <h3 class="text-xl font-semibold text-gray-900 mb-4">Account Information</h3>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="space-y-4">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Full Name</label>
                <p class="text-gray-900 bg-gray-50 p-3 rounded-md">{{ user.name || user.full_name || 'Not provided' }}</p>
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                <p class="text-gray-900 bg-gray-50 p-3 rounded-md">{{ user.email || 'Not provided' }}</p>
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Role</label>
                <p class="text-gray-900 bg-gray-50 p-3 rounded-md">{{ user.role.name }}</p>
              </div>
            </div>
            <div class="space-y-4">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Student/User ID</label>
                <p class="text-gray-900 bg-gray-50 p-3 rounded-md">{{ user.student_id || user.id || 'Not assigned' }}</p>
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Contact Information</label>
                <p class="text-gray-900 bg-gray-50 p-3 rounded-md">{{ user.contact_info || user.phone || 'Not provided' }}</p>
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Emergency Contact</label>
                <p class="text-gray-900 bg-gray-50 p-3 rounded-md">{{ user.emergency_contact || 'Not provided' }}</p>
              </div>
            </div>
          </div>
        </div>

        <!-- Update Profile Form -->
        <div class="bg-white rounded-lg shadow-md p-6">
          <h3 class="text-xl font-semibold text-gray-900 mb-4">Update Profile Information</h3>
          <form @submit.prevent="updateProfile" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <label for="fullName" class="block text-sm font-medium text-gray-700 mb-1">Full Name *</label>
                <input 
                  id="fullName"
                  type="text" 
                  v-model="profileForm.fullName" 
                  class="w-full p-3 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                  placeholder="Enter your full name"
                  required
                />
              </div>
              <div>
                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email Address *</label>
                <input 
                  id="email"
                  type="email" 
                  v-model="profileForm.email" 
                  class="w-full p-3 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                  placeholder="Enter your email address"
                  required
                />
              </div>
              <div>
                <label for="contactInfo" class="block text-sm font-medium text-gray-700 mb-1">Contact Information</label>
                <input 
                  id="contactInfo"
                  type="text" 
                  v-model="profileForm.contactInfo" 
                  class="w-full p-3 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                  placeholder="Phone number, address, etc."
                />
              </div>
              <div>
                <label for="emergencyContact" class="block text-sm font-medium text-gray-700 mb-1">Emergency Contact</label>
                <input 
                  id="emergencyContact"
                  type="text" 
                  v-model="profileForm.emergencyContact" 
                  class="w-full p-3 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                  placeholder="Emergency contact information"
                />
              </div>
            </div>
            <div class="flex justify-end pt-4">
              <button 
                type="submit" 
                :disabled="isUpdatingProfile"
                class="bg-blue-600 text-white px-6 py-3 rounded-md hover:bg-blue-700 disabled:opacity-50 flex items-center"
              >
                <svg v-if="isUpdatingProfile" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                {{ isUpdatingProfile ? 'Updating...' : 'Update Profile' }}
              </button>
            </div>
          </form>
        </div>

            <!-- Change Password Form -->
            <div class="bg-white rounded-lg shadow-md p-6">
          <h3 class="text-xl font-semibold text-gray-900 mb-4">Change Password</h3>
          <form @submit.prevent="changePassword" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div class="md:col-span-2">
                <label for="currentPassword" class="block text-sm font-medium text-gray-700 mb-1">Current Password *</label>
                <div class="relative">
                  <input 
                    id="currentPassword"
                    :type="showCurrentPassword ? 'text' : 'password'" 
                    v-model="passwordForm.currentPassword" 
                    class="w-full p-3 pr-12 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                    placeholder="Enter your current password"
                    required
                  />
                  <button 
                    type="button"
                    @click="showCurrentPassword = !showCurrentPassword"
                    class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600"
                  >
                    <svg v-if="showCurrentPassword" class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L3 3m6.878 6.878L21 21" />
                    </svg>
                    <svg v-else class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                      <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                  </button>
                </div>
              </div>
              <div>
                <label for="newPassword" class="block text-sm font-medium text-gray-700 mb-1">New Password *</label>
                <div class="relative">
                  <input 
                    id="newPassword"
                    :type="showNewPassword ? 'text' : 'password'" 
                    v-model="passwordForm.newPassword" 
                    class="w-full p-3 pr-12 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                    placeholder="Enter new password (min 8 characters)"
                    minlength="8"
                    required
                  />
                  <button 
                    type="button"
                    @click="showNewPassword = !showNewPassword"
                    class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600"
                  >
                    <svg v-if="showNewPassword" class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L3 3m6.878 6.878L21 21" />
                    </svg>
                    <svg v-else class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                      <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                  </button>
                </div>
              </div>
              <div>
                <label for="confirmNewPassword" class="block text-sm font-medium text-gray-700 mb-1">Confirm New Password *</label>
                <div class="relative">
                  <input 
                    id="confirmNewPassword"
                    :type="showConfirmPassword ? 'text' : 'password'" 
                    v-model="passwordForm.confirmNewPassword" 
                    class="w-full p-3 pr-12 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                    placeholder="Confirm your new password"
                    minlength="8"
                    required
                  />
                  <button 
                    type="button"
                    @click="showConfirmPassword = !showConfirmPassword"
                    class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600"
                  >
                    <svg v-if="showConfirmPassword" class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L3 3m6.878 6.878L21 21" />
                    </svg>
                    <svg v-else class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                      <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                  </button>
                </div>
              </div>
            </div>
            <div class="flex justify-end pt-4">
              <button 
                type="submit" 
                :disabled="isChangingPassword"
                class="bg-red-600 text-white px-6 py-3 rounded-md hover:bg-red-700 disabled:opacity-50 flex items-center"
              >
                <svg v-if="isChangingPassword" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                {{ isChangingPassword ? 'Changing...' : 'Change Password' }}
              </button>
            </div>
          </form>
        </div>
      </div>

      <!-- Success/Error Messages -->
      <div v-if="successMessage" class="fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50">
        {{ successMessage }}
      </div>
      <div v-if="errorMessage" class="fixed top-4 right-4 bg-red-500 text-white px-6 py-3 rounded-lg shadow-lg z-50">
        {{ errorMessage }}
      </div>
    </div>
  </div>
</template>

<script setup>

import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import axios from 'axios'

const router = useRouter()

// Reactive data
const user = ref(null)
const isLoadingUser = ref(false)
const debugError = ref(null)
const isUpdatingProfile = ref(false)
const isChangingPassword = ref(false)
const successMessage = ref('')
const errorMessage = ref('')

// Password visibility toggles
const showCurrentPassword = ref(false)
const showNewPassword = ref(false)
const showConfirmPassword = ref(false)

// Form data
const profileForm = ref({
  fullName: '',
  email: '',
  contactInfo: '',
  emergencyContact: ''
})

const passwordForm = ref({
  currentPassword: '',
  newPassword: '',
  confirmNewPassword: ''
})



// Helper functions
const getUserInitials = () => {
  if (!user.value) return 'U'
  const name = user.value.name || user.value.full_name || user.value.email || 'User'
  if (name.includes('@')) return name.charAt(0).toUpperCase()
  const words = name.split(' ')
  if (words.length >= 2) return (words[0].charAt(0) + words[1].charAt(0)).toUpperCase()
  return name.charAt(0).toUpperCase()
}

const formatDate = (dateString) => {
  if (!dateString) return 'N/A'
  try {
    return new Date(dateString).toLocaleDateString('en-US', {
      year: 'numeric', month: 'long', day: 'numeric'
    })
  } catch (error) {
    return 'Invalid date'
  }
}

// API functions
const fetchUserData = async () => {
  const token = localStorage.getItem('token')
  if (!token) {
    router.push('/login')
    return
  }

  isLoadingUser.value = true
  debugError.value = null
  
  try {
        const response = await axios.get('http://localhost:8000/api/login', {
      headers: {
        'Authorization': `Bearer ${token}`,
        'Accept': 'application/json',
        'Content-Type': 'application/json'
      }
    })
    
    user.value = response.data
    
    // Populate form with current user data
    profileForm.value = {
      fullName: user.value.name || user.value.full_name || '',
      email: user.value.email || '',
      contactInfo: user.value.contact_info || user.value.phone || '',
      emergencyContact: user.value.emergency_contact || ''
    }
    
  } catch (error) {
    console.error('Failed to fetch user data:', error)
    debugError.value = error.response?.data?.message || error.message
    
    if (error.response?.status === 401) {
      localStorage.removeItem('token')
      localStorage.removeItem('user_data')
      router.push('/login')
    }
  } finally {
    isLoadingUser.value = false
  }
}

const updateProfile = async () => {
  if (!profileForm.value.fullName || !profileForm.value.email) {
    showError('Please fill in required fields: Full Name and Email')
    return
  }

  isUpdatingProfile.value = true
  
  try {
    const token = localStorage.getItem('token')
    
    const response = await axios.put('http://localhost:8000/api/user/profile', {
      name: profileForm.value.fullName,
      email: profileForm.value.email,
      contact_info: profileForm.value.contactInfo,
      emergency_contact: profileForm.value.emergencyContact
    }, {
      headers: {
        'Authorization': `Bearer ${token}`,
        'Accept': 'application/json',
        'Content-Type': 'application/json'
      }
    })
    
    // Update local user data
    user.value = response.data
    localStorage.setItem('user_data', JSON.stringify(response.data))
    
    showSuccess('Profile updated successfully!')
    
    // Emit event to update navbar
    window.dispatchEvent(new CustomEvent('userDataUpdated', { detail: response.data }))
    
  } catch (error) {
    console.error('Failed to update profile:', error)
    showError(error.response?.data?.message || 'Failed to update profile. Please try again.')
  } finally {
    isUpdatingProfile.value = false
  }
}

const changePassword = async () => {
  if (!passwordForm.value.currentPassword || !passwordForm.value.newPassword || !passwordForm.value.confirmNewPassword) {
    showError('Please fill all password fields.')
    return
  }
  
  if (passwordForm.value.newPassword !== passwordForm.value.confirmNewPassword) {
    showError('New password and confirm password do not match!')
    return
  }
  
  if (passwordForm.value.newPassword.length < 8) {
    showError('New password must be at least 8 characters long.')
    return
  }

  isChangingPassword.value = true
  
  try {
    const token = localStorage.getItem('token')
    
    await axios.put('http://localhost:8000/api/user/password', {
      current_password: passwordForm.value.currentPassword,
      new_password: passwordForm.value.newPassword,
      new_password_confirmation: passwordForm.value.confirmNewPassword
    }, {
      headers: {
        'Authorization': `Bearer ${token}`,
        'Accept': 'application/json',
        'Content-Type': 'application/json'
      }
    })
    
    // Clear password form
    passwordForm.value = {
      currentPassword: '',
      newPassword: '',
      confirmNewPassword: ''
    }
    
    showSuccess('Password changed successfully!')
    
  } catch (error) {
    console.error('Failed to change password:', error)
    showError(error.response?.data?.message || 'Failed to change password. Please try again.')
  } finally {
    isChangingPassword.value = false
  }
}

const showSuccess = (message) => {
  successMessage.value = message
  setTimeout(() => {
    successMessage.value = ''
  }, 5000)
}

const showError = (message) => {
  errorMessage.value = message
  setTimeout(() => {
    errorMessage.value = ''
  }, 5000)
}

// Lifecycle
onMounted(async () => {
  // Try to load user from localStorage first
  const storedUser = localStorage.getItem('user_data')
  if (storedUser) {
    try {
      user.value = JSON.parse(storedUser)
      profileForm.value = {
        fullName: user.value.name || user.value.full_name || '',
        email: user.value.email || '',
        contactInfo: user.value.contact_info || user.value.phone || '',
        emergencyContact: user.value.emergency_contact || ''
      }
    } catch (e) {
      console.error('Error parsing stored user data:', e)
      localStorage.removeItem('user_data')
    }
  }
  
  // Fetch fresh user data from API
  await fetchUserData()
})
</script>

<style scoped>
.animate-spin {
  animation: spin 1s linear infinite;
}

@keyframes spin {
  to {
    transform: rotate(360deg);
  }
}

input:focus {
  outline: none;
}

button:hover:not(:disabled) {
  transform: translateY(-1px);
}

.fixed.top-4.right-4 {
  animation: slideInRight 0.3s ease-out;
}

@keyframes slideInRight {
  from {
    transform: translateX(100%);
    opacity: 0;
  }
  to {
    transform: translateX(0);
    opacity: 1;
  }
}

@media (max-width: 768px) {
  .flex.items-center.space-x-6 {
    flex-direction: column;
    gap: 1rem;
    text-align: center;
  }
  
  .text-right {
    text-align: center;
  }
}
</style>

