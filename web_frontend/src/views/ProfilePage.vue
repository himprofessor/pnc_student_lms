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
            <!-- Profile Image -->
            <div class="relative">
              <div class="w-24 h-24 rounded-full overflow-hidden bg-gradient-to-r from-blue-500 to-blue-600 text-white flex items-center justify-center text-3xl font-bold shadow-lg">
                <img 
                  v-if="getProfileImageUrl() && !imageError" 
                  :src="getProfileImageUrl()" 
                  :alt="user.name"
                  class="w-full h-full object-cover"
                  @error="handleImageError"
                />
                <span v-else>{{ getUserInitials() }}</span>
              </div>
              <!-- Change Photo Button -->
              <button 
                @click="triggerFileInput"
                class="absolute -bottom-2 -right-2 bg-blue-600 text-white rounded-full p-2 hover:bg-blue-700 transition-colors shadow-lg"
                title="Change profile photo"
              >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0118.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                  <path stroke-linecap="round" stroke-linejoin="round" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
              </button>
              <!-- Hidden file input -->
              <input 
                ref="fileInput"
                type="file" 
                accept="image/*" 
                @change="handleImageUpload"
                class="hidden"
              />
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
          <div class="flex justify-between items-center mb-4">
            <h3 class="text-xl font-semibold text-gray-900">Account Information</h3>
            <button 
              @click="toggleEditMode"
              class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors flex items-center"
            >
              <svg v-if="!isEditMode" class="w-4 h-4 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
              </svg>
              <svg v-else class="w-4 h-4 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
              </svg>
              {{ isEditMode ? 'Cancel' : 'Edit Profile' }}
            </button>
          </div>

          <form @submit.prevent="updateProfile" class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="space-y-4">
              <!-- Full Name -->
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Full Name</label>
                <input 
                  v-if="isEditMode"
                  v-model="editForm.name"
                  type="text"
                  class="w-full p-3 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                  placeholder="Enter your full name"
                />
                <p v-else class="text-gray-900 bg-gray-50 p-3 rounded-md">{{ user.name || user.full_name || 'Not provided' }}</p>
              </div>

              <!-- Email (Read-only) -->
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                <p class="text-gray-900 bg-gray-100 p-3 rounded-md border">{{ user.email || 'Not provided' }}</p>
              
              </div>

              <!-- Role (Read-only) -->
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Role</label>
                <p class="text-gray-900 bg-gray-50 p-3 rounded-md">{{ user.role?.name || 'Not assigned' }}</p>
              </div>
            </div>

            <div class="space-y-4">
              <!-- Student/User ID (Read-only) -->
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Student/User ID</label>
                <p class="text-gray-900 bg-gray-100 p-3 rounded-md border">{{ user.student_id || user.id || 'Not assigned' }}</p>
              
              </div>

              <!-- Contact Information -->
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Contact Information</label>
                <input 
                  v-if="isEditMode"
                  v-model="editForm.contact_info"
                  type="text"
                  class="w-full p-3 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                  placeholder="Enter your phone number or contact info"
                />
                <p v-else class="text-gray-900 bg-gray-50 p-3 rounded-md">{{ user.contact_info || user.phone || 'Not provided' }}</p>
              </div>

              <!-- Emergency Contact -->
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Emergency Contact</label>
                <input 
                  v-if="isEditMode"
                  v-model="editForm.emergency_contact"
                  type="text"
                  class="w-full p-3 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                  placeholder="Enter emergency contact information"
                />
                <p v-else class="text-gray-900 bg-gray-50 p-3 rounded-md">{{ user.emergency_contact || 'Not provided' }}</p>
              </div>
            </div>

            <!-- Save/Cancel Buttons -->
            <div v-if="isEditMode" class="md:col-span-2 flex justify-end space-x-4 pt-4 border-t">
              <button 
                type="button"
                @click="cancelEdit"
                class="bg-gray-500 text-white px-6 py-3 rounded-md hover:bg-gray-600 transition-colors"
              >
                Cancel
              </button>
              <button 
                type="submit"
                :disabled="isUpdatingProfile"
                class="bg-green-600 text-white px-6 py-3 rounded-md hover:bg-green-700 disabled:opacity-50 transition-colors flex items-center"
              >
                <svg v-if="isUpdatingProfile" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                {{ isUpdatingProfile ? 'Saving...' : 'Save Changes' }}
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
                class="bg-green-600 text-white px-6 py-3 rounded-md hover:bg-green-700 disabled:opacity-50 transition-colors flex items-center"
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

      <!-- Image Upload Loading Overlay -->
      <div v-if="isUploadingImage" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg p-6 flex items-center space-x-4">
          <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
          <span class="text-gray-700">Uploading image...</span>
        </div>
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

const isChangingPassword = ref(false)
const isUploadingImage = ref(false)
const isUpdatingProfile = ref(false)
const isEditMode = ref(false)
const successMessage = ref('')
const errorMessage = ref('')
const imageError = ref(false)

// File input reference
const fileInput = ref(null)

// Password visibility toggles
const showCurrentPassword = ref(false)
const showNewPassword = ref(false)
const showConfirmPassword = ref(false)

const passwordForm = ref({
  currentPassword: '',
  newPassword: '',
  confirmNewPassword: ''
})

// Edit form for profile data
const editForm = ref({
  name: '',
  contact_info: '',
  emergency_contact: ''
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

const getProfileImageUrl = () => {
  if (!user.value || imageError.value) return null
  
  const imageField = user.value.img_url ||
                    user.value.img || 
                    user.value.profile_image || 
                    user.value.avatar || 
                    user.value.image || 
                    user.value.photo ||
                    user.value.profile_photo

  if (!imageField) return null

  if (imageField.startsWith('http://') || imageField.startsWith('https://')) {
    return imageField
  }

  if (imageField.startsWith('/')) {
    return `http://127.0.0.1:8000${imageField}`
  }

  return `http://127.0.0.1:8000/storage/${imageField}`
}

const handleImageError = () => {
  console.log('Image failed to load, falling back to initials')
  imageError.value = true
}

// Edit mode functions
const toggleEditMode = () => {
  if (isEditMode.value) {
    cancelEdit()
  } else {
    isEditMode.value = true
    // Populate edit form with current user data
    editForm.value = {
      name: user.value.name || user.value.full_name || '',
      contact_info: user.value.contact_info || user.value.phone || '',
      emergency_contact: user.value.emergency_contact || ''
    }
  }
}

const cancelEdit = () => {
  isEditMode.value = false
  editForm.value = {
    name: '',
    contact_info: '',
    emergency_contact: ''
  }
}

const updateProfile = async () => {
  if (!editForm.value.name.trim()) {
    showError('Name is required')
    return
  }

  isUpdatingProfile.value = true

  try {
    const token = localStorage.getItem('authToken')
    
    const response = await axios.put('http://127.0.0.1:8000/api/user/profile', {
      name: editForm.value.name.trim(),
      contact_info: editForm.value.contact_info.trim(),
      emergency_contact: editForm.value.emergency_contact.trim()
    }, {
      headers: {
        'Authorization': `Bearer ${token}`,
        'Accept': 'application/json',
        'Content-Type': 'application/json'
      }
    })

    // Update user data with response
    user.value = { ...user.value, ...response.data.data || response.data }
    localStorage.setItem('user_data', JSON.stringify(user.value))
    
    // Emit event to update navbar
    window.dispatchEvent(new CustomEvent('userDataUpdated', { detail: user.value }))
    
    isEditMode.value = false
    showSuccess('Profile updated successfully!')
    
  } catch (error) {
    console.error('Failed to update profile:', error)
    if (error.response?.data?.errors) {
      const errors = error.response.data.errors
      const errorMessages = Object.values(errors).flat().join(', ')
      showError(`Validation Error: ${errorMessages}`)
    } else {
      showError(error.response?.data?.message || 'Failed to update profile. Please try again.')
    }
  } finally {
    isUpdatingProfile.value = false
  }
}

// Image upload functions
const triggerFileInput = () => {
  fileInput.value?.click()
}

const handleImageUpload = async (event) => {
  const file = event.target.files[0]
  if (!file) return

  console.log('=== IMAGE UPLOAD DEBUG ===')
  console.log('Selected file:', {
    name: file.name,
    size: file.size,
    type: file.type,
    lastModified: file.lastModified
  })

  // Validate file type
  if (!file.type.startsWith('image/')) {
    showError('Please select a valid image file')
    return
  }

  // Validate file size (5MB max)
  if (file.size > 5 * 1024 * 1024) {
    showError('Image size must be less than 5MB')
    return
  }

  isUploadingImage.value = true

  try {
    const token = localStorage.getItem('authToken')
    console.log('Token exists:', !!token)
    console.log('Token preview:', token ? token.substring(0, 20) + '...' : 'null')

    const formData = new FormData()
    formData.append('img', file)

    // Debug FormData
    console.log('FormData entries:')
    for (let [key, value] of formData.entries()) {
      console.log(key, value)
    }

    const url = 'http://127.0.0.1:8000/api/user/upload-image'
    console.log('Upload URL:', url)

    const headers = {
      'Authorization': `Bearer ${token}`,
      'Accept': 'application/json'
      // Don't set Content-Type for FormData, let browser set it with boundary
    }
    console.log('Request headers:', headers)

    console.log('Making request...')
    const response = await axios.post(url, formData, { headers })

    console.log('Upload successful!')
    console.log('Response status:', response.status)
    console.log('Response data:', response.data)
    console.log('Does response have img?:', response.data?.data?.img || response.data?.img)
    console.log('Does response have img_url?:', response.data?.data?.img_url || response.data?.img_url)

    // Reset image error state
    imageError.value = false
    
    showSuccess('Profile image updated successfully!')
    
    // Refetch user data to get updated image path
    await fetchUserData()

  } catch (error) {
    console.error('=== UPLOAD ERROR ===')
    console.error('Error object:', error)
    console.error('Error message:', error.message)
    console.error('Response status:', error.response?.status)
    console.error('Response headers:', error.response?.headers)
    console.error('Response data:', error.response?.data)
    console.error('Request config:', error.config)
    
    if (error.response?.data?.errors) {
      // Handle validation errors
      const errors = error.response.data.errors
      console.error('Validation errors:', errors)
      const errorMessages = Object.values(errors).flat().join(', ')
      showError(`Validation Error: ${errorMessages}`)
    } else if (error.response?.data?.message) {
      showError(error.response.data.message)
    } else if (error.response?.status === 404) {
         showError('Upload endpoint not found. Please check the API route.')
    } else if (error.response?.status === 401) {
      showError('Unauthorized. Please login again.')
      // Redirect to login
      localStorage.removeItem('authToken')
      localStorage.removeItem('token')
      localStorage.removeItem('user_data')
      router.push('/login')
    } else {
      showError('Failed to upload image. Please try again.')
    }
  } finally {
    isUploadingImage.value = false
    // Clear the file input
    if (fileInput.value) {
      fileInput.value.value = ''
    }
  }
}

// API functions
const fetchUserData = async () => {
  const token = localStorage.getItem('authToken')
  if (!token) {
    router.push('/login')
    return
  }

  isLoadingUser.value = true
  debugError.value = null
  
  try {
    const response = await axios.get('http://127.0.0.1:8000/api/user', {
      headers: {
        'Authorization': `Bearer ${token}`,
        'Accept': 'application/json',
        'Content-Type': 'application/json'
      }
    })
    
    user.value = response.data
    localStorage.setItem('user_data', JSON.stringify(user.value))
    
    // Emit event to update navbar
    window.dispatchEvent(new CustomEvent('userDataUpdated', { detail: user.value }))
    
  } catch (error) {
    console.error('Failed to fetch user data:', error)
    debugError.value = error.response?.data?.message || error.message
    
    if (error.response?.status === 401) {
      localStorage.removeItem('authToken')
      localStorage.removeItem('token')
      localStorage.removeItem('user_data')
      router.push('/login')
    }
  } finally {
    isLoadingUser.value = false
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
    const token = localStorage.getItem('authToken')
    
    await axios.put('http://127.0.0.1:8000/api/user/password', {
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

/* Custom styles for file input and image upload */
.relative:hover .absolute {
  opacity: 1;
}

.absolute {
  transition: opacity 0.2s ease-in-out;
}

/* Image upload overlay styles */
.fixed.inset-0 {
  backdrop-filter: blur(2px);
}

/* Edit mode styles */
.bg-gray-100 {
  background-color: #f3f4f6;
  border: 1px solid #d1d5db;
}

/* Transition effects for edit mode */
input {
  transition: all 0.2s ease-in-out;
}

input:focus {
  box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

/* Button hover effects */
button {
  transition: all 0.2s ease-in-out;
}

button:hover {
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

/* Form validation styles */
input:invalid {
  border-color: #ef4444;
}

input:valid {
  border-color: #10b981;
}
</style>
