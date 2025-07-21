<template>
  <div class="min-h-screen bg-gray-50 text-gray-800">

    <div class="px-6 py-6 max-w-3xl mx-auto">
      <h1 class="text-2xl font-bold mb-2">Submit Leave Request</h1>
      <p class="text-sm text-gray-500 mb-6">Fill out the form below to request leave from your studies</p>

      <div class="bg-white p-6 rounded-lg shadow border">
        <div class="mb-4">
          <label for="leave-type" class="block text-sm font-medium text-gray-700 mb-1">Leave Type *</label>
          <select id="leave-type"
            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
            @change="e => e.target.value ? e.target.classList.add('border-blue-500') : e.target.classList.remove('border-blue-500')">
            <option value="">Select leave type</option>
            <option value="sick">Sick Leave</option>
            <option value="vacation">Vacation Leave</option>
            <option value="personal">Personal Leave</option>
          </select>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
          <div>
            <label for="start-date" class="block text-sm font-medium text-gray-700 mb-1">Start Date *</label>
            <input type="date" id="start-date"
              class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
          </div>
          <div>
            <label for="end-date" class="block text-sm font-medium text-gray-700 mb-1">End Date *</label>
            <input type="date" id="end-date"
              class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
          </div>
        </div>

        <div class="mb-4">
          <label for="reason" class="block text-sm font-medium text-gray-700 mb-1">Reason for Leave *</label>
          <textarea id="reason" rows="4"
            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
            placeholder="Please provide a detailed reason for your leave request..."></textarea>
        </div>

        <div class="mb-4">
          <label for="contact-info" class="block text-sm font-medium text-gray-700 mb-1">Contact Information During
            Leave</label>
          <input type="text" id="contact-info"
            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
            placeholder="Phone number or email where you can be reached (optional)">
        </div>

        <div class="mb-6">
          <label class="block text-sm font-medium text-gray-700 mb-1">Supporting Documents</label>
          <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md">
            <div class="space-y-1 text-center">
              <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48"
                aria-hidden="true">
                <path
                  d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02"
                  stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
              </svg>
              <div class="flex text-sm text-gray-600">
                <label for="file-upload"
                  class="relative cursor-pointer bg-white rounded-md font-medium text-blue-600 hover:text-blue-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-blue-500">
                  <span>Upload files</span>
                  <input id="file-upload" name="file-upload" type="file" class="sr-only">
                </label>
                <p class="pl-1">or drag and drop</p>
              </div>
              <p class="text-xs text-gray-500">
                PDF, DOC, DOCX, JPG, PNG up to 10MB each
              </p>
            </div>
          </div>
        </div>

        <div class="flex justify-end space-x-3">
          <button type="button"
            class="px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
            Cancel
          </button>
          <button type="submit"
            class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
            Submit Request
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
// No specific script logic needed for this static form example
</script>