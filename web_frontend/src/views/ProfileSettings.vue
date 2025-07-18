<template>
  <div class="min-h-screen bg-gray-100 p-8">
    <div class="max-w-4xl mx-auto">
      <!-- Header -->
      <h1 class="text-3xl font-bold mb-2">Profile Settings</h1>
      <p class="text-gray-600 mb-6">Manage your personal information and account settings</p>

      <!-- Profile Info Card -->
      <div class="bg-white p-6 rounded-lg shadow-md mb-6">
        <div class="flex items-center space-x-4 mb-4">
          <div class="w-16 h-16 rounded-full bg-blue-500 text-white flex items-center justify-center text-2xl font-semibold">
            J
          </div>
          <div>
            <h2 class="text-lg font-semibold">Sela Torm</h2>
            <p class="text-sm text-gray-600">student@example.com</p>
            <p class="text-sm text-gray-600">Student - ID: ST001</p>
          </div>
        </div>

        <!-- Update Profile Form -->
        <form class="grid grid-cols-1 md:grid-cols-2 gap-4" @submit.prevent="updateProfile">
          <input type="text" placeholder="Full Name" v-model="fullName" class="border p-2 rounded" />
          <input type="email" placeholder="Email Address" v-model="email" class="border p-2 rounded" />
          <input type="text" placeholder="Contact Information" v-model="contactInfo" class="border p-2 rounded" />
          <input type="text" placeholder="Emergency Contact" v-model="emergencyContact" class="border p-2 rounded" />
          <div class="md:col-span-2 text-right">
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
              Update Profile
            </button>
          </div>
        </form>
      </div>

      <!-- Change Password -->
      <div class="bg-white p-6 rounded-lg shadow-md">
        <h2 class="text-lg font-semibold mb-4">Change Password</h2>
        <form class="grid grid-cols-1 md:grid-cols-2 gap-4" @submit.prevent="changePassword">
          <input type="password" placeholder="Current Password" v-model="currentPassword" class="border p-2 rounded" />
          <div></div>
          <input type="password" placeholder="New Password" v-model="newPassword" class="border p-2 rounded" />
          <input type="password" placeholder="Confirm New Password" v-model="confirmNewPassword" class="border p-2 rounded" />
          <div class="md:col-span-2 text-right">
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
              Change Password
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';

// Profile form state
const fullName = ref('');
const email = ref('');
const contactInfo = ref('');
const emergencyContact = ref('');

// Password form state
const currentPassword = ref('');
const newPassword = ref('');
const confirmNewPassword = ref('');

// Update profile handler
function updateProfile() {
  if (!fullName.value || !email.value) {
    alert('Please fill in required fields: Full Name and Email');
    return;
  }
  // TODO: Add API call or logic to save profile data here
  alert('Profile updated successfully!');
}

// Change password handler
function changePassword() {
  if (!currentPassword.value || !newPassword.value || !confirmNewPassword.value) {
    alert('Please fill all password fields.');
    return;
  }
  if (newPassword.value !== confirmNewPassword.value) {
    alert('New password and confirm password do not match!');
    return;
  }
  // TODO: Add API call or logic to change password here
  alert('Password changed successfully!');
}
</script>

<style scoped>
/* Add any extra styling if needed */
</style>
