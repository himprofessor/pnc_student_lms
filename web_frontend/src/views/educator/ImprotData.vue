<template>
  <div class="min-h-screen bg-gradient-to-br from-slate-50 to-blue-50 flex items-center justify-center p-4">
    <div class="relative w-full max-w-md bg-white rounded-2xl shadow-xl border border-slate-200 overflow-hidden">
      <!-- Close Button -->
      <button 
        @click="resetForm"
        class="absolute top-4 right-4 p-2 text-slate-400 hover:text-slate-600 hover:bg-slate-100 rounded-full transition-all duration-200 z-10"
      >
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5">
          <path fill-rule="evenodd" d="M5.47 5.47a.75.75 0 0 1 1.06 0L12 10.94l5.47-5.47a.75.75 0 1 1 1.06 1.06L13.06 12l5.47 5.47a.75.75 0 1 1-1.06 1.06L12 13.06l-5.47 5.47a.75.75 0 0 1-1.06-1.06L10.94 12 5.47 6.53a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd" />
        </svg>
      </button>

      <!-- Success State -->
      <div v-if="successMessage" class="p-8 text-center">
        <div class="w-16 h-16 bg-emerald-100 rounded-full flex items-center justify-center mx-auto mb-6">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-8 h-8 text-emerald-600">
            <path fill-rule="evenodd" d="M2.25 12c0-5.385 4.365-9.75 9.75-9.75s9.75 4.365 9.75 9.75-4.365 9.75-9.75 9.75S2.25 17.385 2.25 12Zm13.36-1.89a.75.75 0 0 0-1.06-1.06l-4.25 4.25-1.72-1.72a.75.75 0 0 0-1.06 1.06L10.5 14.25l4.81-4.81Z" clip-rule="evenodd" />
          </svg>
        </div>
        
        <h2 class="text-2xl font-bold text-slate-900 mb-8">YAY! Your data is here!</h2>
        
        <div class="space-y-3">
          <button 
            type="button" 
            @click="handleCtaClick"
            class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-6 rounded-xl transition-colors duration-200 shadow-lg hover:shadow-xl"
          >
            Let's customize your new board
          </button>
          
          <button 
            type="button" 
            @click="resetForm"
            class="w-full text-slate-600 hover:text-slate-800 font-medium py-2 transition-colors duration-200"
          >
            Upload file again
          </button>
        </div>
      </div>

      <!-- Upload Form -->
      <div v-else class="p-8">
        <div class="text-center mb-8">
          <h2 class="text-2xl font-bold text-slate-900 mb-2">Import Data</h2>
          <p class="text-slate-600">Create a new board with content from other apps.</p>
        </div>

        <form @submit.prevent="importStudents" class="space-y-6">
          <!-- File Upload Area -->
          <div class="relative">
            <label 
              for="excel_file" 
              class="group block w-full border-2 border-dashed border-slate-300 hover:border-blue-400 rounded-xl p-8 text-center cursor-pointer transition-all duration-200 hover:bg-blue-50"
              :class="{ 'border-blue-400 bg-blue-50': selectedFile }"
            >
              <div class="flex flex-col items-center space-y-3">
                <div class="w-12 h-12 bg-slate-100 group-hover:bg-blue-100 rounded-full flex items-center justify-center transition-colors duration-200">
                  <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 text-slate-500 group-hover:text-blue-600">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m6.75 12-3-3m0 0-3 3m3-3v6m-1.5-15H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                  </svg>
                </div>
                
                <div>
                  <p class="font-semibold text-slate-700 group-hover:text-blue-700 transition-colors duration-200">
                    {{ selectedFile ? selectedFile.name : 'Choose a CSV file' }}
                  </p>
                  <p class="text-sm text-slate-500 mt-1">
                    {{ selectedFile ? 'Click to change file' : 'Drag and drop or click to browse' }}
                  </p>
                </div>
              </div>
            </label>
            
            <input 
              type="file" 
              id="excel_file" 
              accept=".csv" 
              @change="handleFileChange" 
              required 
              class="absolute inset-0 w-full h-full opacity-0 cursor-pointer"
            />
          </div>

          <!-- Submit Button -->
          <button 
            type="submit" 
            :disabled="isUploading || !selectedFile"
            class="w-full bg-blue-600 hover:bg-blue-700 disabled:bg-slate-300 disabled:cursor-not-allowed text-white font-semibold py-3 px-6 rounded-xl transition-all duration-200 shadow-lg hover:shadow-xl disabled:shadow-none"
          >
            <span v-if="isUploading" class="flex items-center justify-center space-x-2">
              <svg class="animate-spin w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              <span>Uploading...</span>
            </span>
            <span v-else>Import Data</span>
          </button>
        </form>
      </div>
      
      <!-- Error State -->
      <div v-if="errorMessage" class="mx-8 mb-8 p-4 bg-red-50 border border-red-200 rounded-xl">
        <div class="flex items-start space-x-3">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5 text-red-500 mt-0.5 flex-shrink-0">
            <path fill-rule="evenodd" d="M2.25 12c0-5.385 4.365-9.75 9.75-9.75s9.75 4.365 9.75 9.75-4.365 9.75-9.75 9.75S2.25 17.385 2.25 12ZM12 8.25a.75.75 0 0 1 .75.75v3.75a.75.75 0 0 1-1.5 0V9a.75.75 0 0 1 .75-.75Zm0 8.25a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5Z" clip-rule="evenodd" />
          </svg>
          <div class="flex-1">
            <p class="font-medium text-red-800">{{ errorMessage }}</p>
            <ul v-if="importErrors.length > 0" class="mt-2 space-y-1">
              <li v-for="(error, index) in importErrors" :key="index" class="text-sm text-red-700">
                • {{ error }}
              </li>
            </ul>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import axios from 'axios';

const selectedFile = ref(null);
const isUploading = ref(false);
const successMessage = ref('');
const errorMessage = ref('');
const importErrors = ref([]);

const handleFileChange = (event) => {
  const file = event.target.files[0];
  if (!file) {
    errorMessage.value = 'No file selected.';
    selectedFile.value = null;
    return;
  }
  if (!file.name.endsWith('.csv')) {
    errorMessage.value = 'Please select a CSV file.';
    selectedFile.value = null;
    event.target.value = '';
    return;
  }
  selectedFile.value = file;
  errorMessage.value = '';
  successMessage.value = '';
  importErrors.value = [];
};

const importStudents = async () => {
  if (!selectedFile.value) {
    errorMessage.value = 'Please select a CSV file.';
    return;
  }

  isUploading.value = true;
  successMessage.value = '';
  errorMessage.value = '';
  importErrors.value = [];

  const formData = new FormData();
  formData.append('excel_file', selectedFile.value);

  const token = localStorage.getItem('token');
  if (!token) {
    errorMessage.value = 'No authentication token found. Please log in.';
    isUploading.value = false;
    return;
  }

  try {
    const response = await axios.post('http://localhost:8000/api/educator/students/import', formData, {
      headers: {
        'Authorization': `Bearer ${token}`,
      },
    });

    if (response.status === 200) {
      successMessage.value = response.data.message;
    } else if (response.status === 206) {
      successMessage.value = response.data.message;
      importErrors.value = response.data.errors;
    } else {
      errorMessage.value = 'Unexpected response from server.';
    }
  } catch (error) {
    errorMessage.value = error.response?.data?.message || 'An error occurred during import.';
    importErrors.value = error.response?.data?.errors || [error.message];
  } finally {
    isUploading.value = false;
  }
};

const resetForm = () => {
  selectedFile.value = null;
  successMessage.value = '';
  errorMessage.value = '';
  importErrors.value = [];
};

const handleCtaClick = () => {
  alert('Redirecting to customize board...');
  // Add your navigation logic here, e.g., using Vue Router
};
</script>

<style scoped>
/* Minimal custom styles - most styling is handled by Tailwind */
.animate-spin {
  animation: spin 1s linear infinite;
}

@keyframes spin {
  from {
    transform: rotate(0deg);
  }
  to {
    transform: rotate(360deg);
  }
}
</style>
