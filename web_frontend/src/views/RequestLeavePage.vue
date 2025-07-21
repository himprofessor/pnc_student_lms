<template>
  <div class="min-h-screen bg-gray-50 text-gray-800">
    <div class="px-6 py-6 max-w-3xl mx-auto">
      <h1 class="text-2xl font-bold mb-2">Submit Leave Request</h1>
      <p class="text-sm text-gray-500 mb-6">Fill out the form below to request leave from your studies</p>

      <div class="bg-white p-6 rounded-lg shadow border">
        <div v-if="successMessage" class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
          <span class="block sm:inline">{{ successMessage }}</span>
          <span class="absolute top-0 bottom-0 right-0 px-4 py-3 cursor-pointer" @click="successMessage = ''">
            <svg class="fill-current h-6 w-6 text-green-500" role="button" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><title>Close</title><path d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.414l-2.651 2.651a1.2 1.2 0 1 1-1.697-1.697L8.586 10l-2.651-2.651a1.2 1.2 0 0 1 1.697-1.697L10 8.586l2.651-2.651a1.2 1.2 0 0 1 0 1.697z"/></svg>
          </span>
        </div>
        <div v-if="errorMessage" class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
          <span class="block sm:inline">{{ errorMessage }}</span>
          <span class="absolute top-0 bottom-0 right-0 px-4 py-3 cursor-pointer" @click="errorMessage = ''">
            <svg class="fill-current h-6 w-6 text-red-500" role="button" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><title>Close</title><path d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.414l-2.651 2.651a1.2 1.2 0 1 1-1.697-1.697L8.586 10l-2.651-2.651a1.2 1.2 0 0 1 1.697-1.697L10 8.586l2.651-2.651a1.2 1.2 0 0 1 0 1.697z"/></svg>
          </span>
        </div>
        <ul v-if="validationErrors.length > 0" class="list-disc list-inside text-red-600 mb-4">
          <li v-for="(error, index) in validationErrors" :key="index">{{ error }}</li>
        </ul>

        <form @submit.prevent="submitLeaveRequest">
          <div class="mb-4">
            <label for="leave-type" class="block text-sm font-medium text-gray-700 mb-1">Leave Type *</label>
            <div class="relative">
              <select
                id="leave-type"
                v-model="form.leave_type_id"
                class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md"
                :class="{'border-red-500': validationErrors.some(e => e.includes('leave type'))}"
              >
                <option value="">Select leave type</option>
                <option v-for="type in leaveTypes" :key="type.id" :value="type.id">
                  {{ type.name }}
                </option>
              </select>
              <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700">
                <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                  <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                </svg>
              </div>
            </div>
          </div>

          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
            <div>
              <label for="from-date" class="block text-sm font-medium text-gray-700 mb-1">Start Date *</label>
              <input
                type="date"
                id="from-date"
                v-model="form.from_date"
                class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                :class="{'border-red-500': validationErrors.some(e => e.includes('from date'))}"
              >
            </div>
            <div>
              <label for="to-date" class="block text-sm font-medium text-gray-700 mb-1">End Date *</label>
              <input
                type="date"
                id="to-date"
                v-model="form.to_date"
                class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                :class="{'border-red-500': validationErrors.some(e => e.includes('to date'))}"
              >
            </div>
          </div>

          <div class="mb-4">
            <label for="reason" class="block text-sm font-medium text-gray-700 mb-1">Reason for Leave *</label>
            <textarea
              id="reason"
              rows="4"
              v-model="form.reason"
              class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
              :class="{'border-red-500': validationErrors.some(e => e.includes('reason'))}"
              placeholder="Please provide a detailed reason for your leave request..."
            ></textarea>
          </div>

          <div class="mb-4">
            <label for="contact-info" class="block text-sm font-medium text-gray-700 mb-1">Contact Information During Leave</label>
            <input
              type="text"
              id="contact-info"
              v-model="form.contact_info"
              class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
              placeholder="Phone number or email where you can be reached (optional)"
            >
          </div>

          <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-1">Supporting Documents</label>
            <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md">
              <div class="space-y-1 text-center">
                <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                  <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
                <div class="flex text-sm text-gray-600">
                  <label for="file-upload" class="relative cursor-pointer bg-white rounded-md font-medium text-blue-600 hover:text-blue-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-blue-500">
                    <span>Upload files</span>
                    <input id="file-upload" name="file-upload" type="file" class="sr-only" @change="handleFileUpload">
                  </label>
                  <p class="pl-1">or drag and drop</p>
                </div>
                <p class="text-xs text-gray-500">
                  PDF, DOC, DOCX, JPG, PNG up to 2MB each
                </p>
                <p v-if="form.supporting_documents" class="text-sm text-gray-600 mt-2">
                  Selected file: {{ form.supporting_documents.name }}
                </p>
              </div>
            </div>
          </div>

          <div class="flex justify-end space-x-3">
            <button
              type="button"
              @click="resetForm"
              class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-200 rounded-md hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500"
            >
              Cancel
            </button>
            <button
              type="submit"
              :disabled="loading"
              class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed"
            >
              <span v-if="loading">Submitting...</span>
              <span v-else>Submit Request</span>
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, reactive } from 'vue';
import axios from 'axios';
import { useRouter } from 'vue-router';

const router = useRouter();

const leaveTypes = ref([]);
const loading = ref(false);
const successMessage = ref('');
const errorMessage = ref('');
const validationErrors = ref([]);

const form = reactive({
  leave_type_id: '',
  reason: '',
  from_date: '',
  to_date: '',
  contact_info: '',
  supporting_documents: null,
});

// Fetch leave types on component mount
onMounted(() => {
  fetchLeaveTypes();
});

// Fetch leave types from API
const fetchLeaveTypes = async () => {
  try {
    const response = await axios.get('/leave-types');
    leaveTypes.value = response.data.data;
  } catch (error) {
    console.error('Error fetching leave types:', error);
    errorMessage.value = error.response?.data?.message || 'Failed to load leave types. Please try again.';
  }
};

// Handle file input change
const handleFileUpload = (event) => {
  form.supporting_documents = event.target.files[0];
};

// Submit the leave request
const submitLeaveRequest = async () => {
  loading.value = true;
  successMessage.value = '';
  errorMessage.value = '';
  validationErrors.value = [];

  const authToken = localStorage.getItem('authToken');
  if (!authToken) {
    errorMessage.value = 'Authentication token not found. Please log in again.';
    loading.value = false;
    router.push('/login');
    return;
  }

  const formData = new FormData();
  formData.append('leave_type_id', form.leave_type_id);
  formData.append('reason', form.reason);
  formData.append('from_date', form.from_date);
  formData.append('to_date', form.to_date);
  if (form.contact_info) {
    formData.append('contact_info', form.contact_info);
  }
  if (form.supporting_documents) {
    formData.append('supporting_documents', form.supporting_documents);
  }

  try {
    const response = await axios.post('/student/request-leave', formData);

    successMessage.value = response.data.message || 'Leave request submitted successfully!';
    resetForm();

    // Delay and redirect after success
    setTimeout(() => {
      router.push('/dashboard');
    }, 1500);
  } catch (error) {
    console.error('Error submitting leave request:', error);

    if (error.response) {
      const status = error.response.status;
      const responseData = error.response.data;

      if (status === 422 && responseData.errors) {
        for (const key in responseData.errors) {
          validationErrors.value.push(responseData.errors[key][0]);
        }
        errorMessage.value = responseData.message || 'Please fix the validation errors.';
      } else if (status === 401 || status === 403) {
        errorMessage.value = responseData.message || 'Unauthorized. Please log in again.';
      } else {
        errorMessage.value = responseData.message || `Error ${status}: ${error.response.statusText || 'Something went wrong.'}`;
      }
    } else if (error.request) {
      errorMessage.value = 'No response from server. Please check your network or try again later.';
    } else {
      errorMessage.value = 'An unknown error occurred during the request.';
    }
  } finally {
    loading.value = false;
  }
};

// Reset the form after submission or error
const resetForm = () => {
  form.leave_type_id = '';
  form.reason = '';
  form.from_date = '';
  form.to_date = '';
  form.contact_info = '';
  form.supporting_documents = null;

  const fileInput = document.getElementById('file-upload');
  if (fileInput) {
    fileInput.value = '';
  }

  validationErrors.value = [];
  errorMessage.value = '';
  successMessage.value = '';
};
</script>
