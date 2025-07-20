<template>
  <div class="min-h-screen bg-gray-100 p-8">
    <div class="max-w-4xl mx-auto">
      <!-- Header -->
      <h1 class="text-3xl font-bold mb-2">Profile Settings</h1>
      <p class="text-gray-600 mb-6">Manage your personal information and account settings</p>

      <!-- Loading State -->
      <div v-if="loading" class="text-center py-8">
        <p>Loading profile...</p>
      </div>

      <!-- Profile Info Card -->
      <div v-else class="bg-white p-6 rounded-lg shadow-md mb-6">
        <div class="flex items-center space-x-4 mb-4">
          <div class="w-16 h-16 rounded-full bg-blue-500 text-white flex items-center justify-center text-2xl font-semibold">
            {{ getInitials(user.name) }}
          </div>
          <div>
            <h2 class="text-lg font-semibold">{{ user.name || 'N/A' }}</h2>
            <p class="text-sm text-gray-600">{{ user.email || 'N/A' }}</p>
            <p class="text-sm text-gray-600">{{ getRoleName(user.role_id) }} - ID: {{ user.id || 'N/A' }}</p>
          </div>
        </div>

        <!-- Update Profile Form -->
        <form class="grid grid-cols-1 md:grid-cols-2 gap-4" @submit.prevent="updateProfile">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Full Name *</label>
            <input 
              type="text" 
              placeholder="Full Name" 
              v-model="profileForm.name" 
              class="border p-2 rounded w-full focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
              required 
            />
          </div>
          
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Email Address *</label>
            <input 
              type="email" 
              placeholder="Email Address" 
              v-model="profileForm.email" 
              class="border p-2 rounded w-full focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
              required 
            />
          </div>
          
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Role</label>
            <select 
              v-model="profileForm.role_id" 
              class="border p-2 rounded w-full focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
              :disabled="!canChangeRole"
            >
              <option value="">Select Role</option>
              <option v-for="role in roles" :key="role.id" :value="role.id">
                {{ role.name }}
              </option>
            </select>
            <p v-if="!canChangeRole" class="text-xs text-gray-500 mt-1">
              Role changes require admin privileges
            </p>
          </div>
          
          <div></div> <!-- Empty div for grid spacing -->
          
          <div class="md:col-span-2">
            <div v-if="profileError" class="mb-4 p-3 bg-red-100 border border-red-400 text-red-700 rounded">
              {{ profileError }}
            </div>
            <div v-if="profileSuccess" class="mb-4 p-3 bg-green-100 border border-green-400 text-green-700 rounded">
              {{ profileSuccess }}
            </div>
            <div class="text-right">
              <button 
                type="submit" 
                class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 disabled:bg-gray-400 disabled:cursor-not-allowed"
                :disabled="profileLoading"
              >
                {{ profileLoading ? 'Updating...' : 'Update Profile' }}
              </button>
            </div>
          </div>
        </form>
      </div>

      <!-- Change Password -->
      <div class="bg-white p-6 rounded-lg shadow-md">
        <h2 class="text-lg font-semibold mb-4">Change Password</h2>
        <form class="grid grid-cols-1 md:grid-cols-2 gap-4" @submit.prevent="changePassword">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Current Password *</label>
            <input 
              type="password" 
              placeholder="Current Password" 
              v-model="passwordForm.currentPassword" 
              class="border p-2 rounded w-full focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
              required 
            />
          </div>
          <div></div>
          
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">New Password *</label>
            <input 
              type="password" 
              placeholder="New Password" 
              v-model="passwordForm.newPassword" 
              class="border p-2 rounded w-full focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
              required 
              minlength="8"
            />
            <p class="text-xs text-gray-500 mt-1">Minimum 8 characters</p>
          </div>
          
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Confirm New Password *</label>
            <input 
              type="password" 
              placeholder="Confirm New Password" 
              v-model="passwordForm.confirmNewPassword" 
              class="border p-2 rounded w-full focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
              required 
            />
          </div>
          
          <div class="md:col-span-2">
            <div v-if="passwordError" class="mb-4 p-3 bg-red-100 border border-red-400 text-red-700 rounded">
              {{ passwordError }}
            </div>
            <div v-if="passwordSuccess" class="mb-4 p-3 bg-green-100 border border-green-400 text-green-700 rounded">
              {{ passwordSuccess }}
            </div>
            <div class="text-right">
              <button 
                type="submit" 
                class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 disabled:bg-gray-400 disabled:cursor-not-allowed"
                :disabled="passwordLoading"
              >
                {{ passwordLoading ? 'Changing...' : 'Change Password' }}
              </button>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';


// User data
const user = ref({
  id: null,
  name: '',
  email: '',
  role_id: null,
});

// Available roles
const roles = ref([
  { id: 1, name: 'Admin' },
  { id: 2, name: 'Teacher' },
  { id: 3, name: 'Student' },
]);

// Loading states
const loading = ref(true);
const profileLoading = ref(false);
const passwordLoading = ref(false);

// Profile form state
const profileForm = ref({
  name: '',
  email: '',
  role_id: null,
});

// Password form state
const passwordForm = ref({
  currentPassword: '',
  newPassword: '',
  confirmNewPassword: '',
});

// Error and success states
const profileError = ref('');
const profileSuccess = ref('');
const passwordError = ref('');
const passwordSuccess = ref('');

// Computed properties
const canChangeRole = computed(() => {
  // Only allow role changes for admins or if current user is admin
  return user.value.role_id === 1; // Assuming 1 is admin role
});

// Methods
const getInitials = (name) => {
  if (!name) return 'U';
  return name.split(' ').map(n => n[0]).join('').toUpperCase().slice(0, 2);
};

const getRoleName = (roleId) => {
  const role = roles.value.find(r => r.id === roleId);
  return role ? role.name : 'Unknown';
};

const loadUserProfile = async () => {
  try {
    loading.value = true;
    const response = await authAPI.getProfile();
    
    user.value = {
      id: response.id,
      name: response.name || '',
      email: response.email || '',
      role_id: response.role_id || null,
    };

    // Populate form with current user data
    profileForm.value = {
      name: user.value.name,
      email: user.value.email,
      role_id: user.value.role_id,
    };

  } catch (error) {
    console.error('Failed to load profile:', error);
    profileError.value = 'Failed to load profile data';
  } finally {
    loading.value = false;
  }
};

const loadRoles = async () => {
  try {
    // If you have a roles endpoint, uncomment and use this:
    // const response = await generalAPI.get('/api/roles');
    // roles.value = response.data || response;
  } catch (error) {
    console.error('Failed to load roles:', error);
    // Keep default roles if API call fails
  }
};

// Update profile handler
const updateProfile = async () => {
  profileError.value = '';
  profileSuccess.value = '';

  // Validation
  if (!profileForm.value.name || !profileForm.value.email) {
    profileError.value = 'Please fill in required fields: Name and Email';
    return;
  }

  // Email validation
  const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  if (!emailRegex.test(profileForm.value.email)) {
    profileError.value = 'Please enter a valid email address';
    return;
  }

  try {
    profileLoading.value = true;

    const updateData = {
      name: profileForm.value.name,
      email: profileForm.value.email,
    };

    // Only include role_id if user can change role and it's different
    if (canChangeRole.value && profileForm.value.role_id !== user.value.role_id) {
      updateData.role_id = profileForm.value.role_id;
    }

    const response = await generalAPI.put('/api/profile', updateData);
    
    // Update local user data
    user.value = { ...user.value, ...updateData };
    profileSuccess.value = 'Profile updated successfully!';
    
    // Clear success message after 3 seconds
    setTimeout(() => {
      profileSuccess.value = '';
    }, 3000);

  } catch (error) {
    profileError.value = error.message || 'Failed to update profile';
  } finally {
    profileLoading.value = false;
  }
};

// Change password handler
const changePassword = async () => {
  passwordError.value = '';
  passwordSuccess.value = '';

  // Validation
  if (!passwordForm.value.currentPassword || !passwordForm.value.newPassword || !passwordForm.value.confirmNewPassword) {
    passwordError.value = 'Please fill all password fields.';
    return;
  }

  if (passwordForm.value.newPassword !== passwordForm.value.confirmNewPassword) {
    passwordError.value = 'New password and confirm password do not match!';
    return;
  }

  if (passwordForm.value.newPassword.length < 8) {
    passwordError.value = 'New password must be at least 8 characters long';
    return;
  }

  if (passwordForm.value.currentPassword === passwordForm.value.newPassword) {
    passwordError.value = 'New password must be different from current password';
    return;
  }

  try {
    passwordLoading.value = true;

    await generalAPI.put('/api/change-password', {
      current_password: passwordForm.value.currentPassword,
      password: passwordForm.value.newPassword,
      password_confirmation: passwordForm.value.confirmNewPassword,
    });

    passwordSuccess.value = 'Password changed successfully!';
    
    // Clear form
    passwordForm.value = {
      currentPassword: '',
      newPassword: '',
      confirmNewPassword: '',
    };

    // Clear success message after 3 seconds
    setTimeout(() => {
      passwordSuccess.value = '';
    }, 3000);

  } catch (error) {
    passwordError.value = error.message || 'Failed to change password';
  } finally {
    passwordLoading.value = false;
  }
};

// Load data on component mount
onMounted(async () => {
  await Promise.all([
    loadUserProfile(),
    loadRoles(),
  ]);
});
</script>

<style scoped>
/* Additional custom styles if needed */
.focus\:ring-2:focus {
  outline: none;
}
</style>
