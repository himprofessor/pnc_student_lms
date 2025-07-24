<template>
  <div class="min-h-screen bg-gray-100 p-8">
    <div class="max-w-4xl mx-auto">
      <!-- Header -->
      <h1 class="text-3xl font-bold mb-2">Profile Settings</h1>
      <p class="text-gray-600 mb-6">
        Manage your personal information and account settings
      </p>

      <!-- Profile Info Card -->
      <div class="bg-white p-6 rounded-lg shadow-md mb-6">
        <div class="flex items-center space-x-4 mb-4">
          <div class="w-16 h-16 rounded-full bg-blue-500 text-white flex items-center justify-center text-2xl font-semibold overflow-hidden">
            <template v-if="profile.img_url">
              <img :src="profile.img_url" alt="Profile Image" class="w-full h-full object-cover" />
            </template>
            <template v-else>
              {{ profile.name.charAt(0) }}
            </template>
          </div>
          <div>
            <h2 class="text-lg font-semibold">{{ profile.name }}</h2>
            <p class="text-sm text-gray-600">{{ profile.email }}</p>
            <p class="text-sm text-gray-600">
              {{ profile.role?.name || "Student" }}
            </p>
          </div>
        </div>

        <!-- Upload & Delete Image Buttons -->
        <div class="mb-4">
          <input type="file" ref="imageInput" @change="onImageSelected" class="hidden" />
          <button @click="selectImage" class="mr-2 bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
            Upload New Image
          </button>
          <button
            @click="deleteImage"
            :disabled="!profile.img_url"
            class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700 disabled:opacity-50"
          >
            Delete Image
          </button>
        </div>

        <!-- Update Profile Form -->
        <form
          class="grid grid-cols-1 md:grid-cols-2 gap-4"
          @submit.prevent="updateProfile"
        >
          <input
            type="text"
            placeholder="Full Name"
            v-model="profile.name"
            class="border p-2 rounded"
          />
          <input
            type="email"
            placeholder="Email Address"
            v-model="profile.email"
            class="border p-2 rounded"
          />
          <input
            type="text"
            placeholder="Contact Information"
            v-model="profile.contact_info"
            class="border p-2 rounded"
          />
          <input
            type="text"
            placeholder="Emergency Contact"
            v-model="profile.emergency_contact"
            class="border p-2 rounded"
          />
          <div class="md:col-span-2 text-right">
            <button
              type="submit"
              class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700"
            >
              Update Profile
            </button>
          </div>
        </form>
      </div>

      <!-- Change Password -->
      <div class="bg-white p-6 rounded-lg shadow-md">
        <h2 class="text-lg font-semibold mb-4">Change Password</h2>
        <form
          class="grid grid-cols-1 md:grid-cols-2 gap-4"
          @submit.prevent="changePassword"
        >
          <input
            type="password"
            placeholder="Current Password"
            v-model="passwordForm.current_password"
            class="border p-2 rounded"
          />
          <div></div>
          <input
            type="password"
            placeholder="New Password"
            v-model="passwordForm.new_password"
            class="border p-2 rounded"
          />
          <input
            type="password"
            placeholder="Confirm New Password"
            v-model="passwordForm.new_password_confirmation"
            class="border p-2 rounded"
          />
          <div class="md:col-span-2 text-right">
            <button
              type="submit"
              class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700"
            >
              Change Password
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import axios from "@/axios";

const profile = ref({
  name: "",
  email: "",
  contact_info: "",
  emergency_contact: "",
  img_url: null,
  role: {},
});

const passwordForm = ref({
  current_password: "",
  new_password: "",
  new_password_confirmation: "",
});

// Load profile data on mount
onMounted(async () => {
  try {
    const { data } = await axios.get("/profile");
    profile.value = data.data;
  } catch (error) {
    alert("Error loading profile");
    console.error(error);
  }
});

// Update profile data
const updateProfile = async () => {
  try {
    await axios.put("/profile", {
      name: profile.value.name,
      email: profile.value.email,
      contact_info: profile.value.contact_info,
      emergency_contact: profile.value.emergency_contact,
    });
    alert("Profile updated successfully!");
  } catch (error) {
    alert("Error updating profile!");
    console.error(error);
  }
};

// Change password
const changePassword = async () => {
  if (
    !passwordForm.value.current_password ||
    !passwordForm.value.new_password ||
    !passwordForm.value.new_password_confirmation
  ) {
    return alert("Please fill all password fields");
  }
  try {
    await axios.put("/profile/password", passwordForm.value);
    alert("Password changed successfully!");
    passwordForm.value = {
      current_password: "",
      new_password: "",
      new_password_confirmation: "",
    };
  } catch (error) {
    alert(error.response?.data?.message || "Error changing password!");
    console.error(error);
  }
};

// Image upload
const imageFile = ref(null);
const imageInput = ref(null);

const selectImage = () => {
  imageInput.value.click();
};

const onImageSelected = async (event) => {
  if (!event.target.files.length) return;

  imageFile.value = event.target.files[0];
  const formData = new FormData();
  formData.append("img", imageFile.value);

  try {
    const { data } = await axios.post("/user/upload-image", formData, {
      headers: {
        "Content-Type": "multipart/form-data",
      },
    });
    profile.value.img_url = data.img_url;
    alert("Profile image uploaded successfully!");
  } catch (error) {
    alert("Error uploading profile image!");
    console.error(error);
  }
};

// Delete image
const deleteImage = async () => {
  if (!confirm("Are you sure you want to delete your profile image?")) return;

  try {
    await axios.delete("/user/delete-image");
    profile.value.img_url = null;
    alert("Profile image deleted successfully!");
  } catch (error) {
    alert("Error deleting profile image!");
    console.error(error);
  }
};
</script>

<style scoped>
/* Add any additional styling if needed */
</style>
