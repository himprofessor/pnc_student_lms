<template>
  <div class="min-h-screen bg-gray-50 text-gray-800">

    <!-- Main Content -->
    <div class="max-w-5xl mx-auto px-6 py-10">
      <h1 class="text-2xl font-bold mb-1">Leave History</h1>
      <p class="text-gray-500 mb-6">View all your previous leave requests and their status</p>

      <!-- Filters -->
      <div class="bg-white p-4 rounded-md shadow-sm mb-6 flex items-center justify-between">
        <div class="flex gap-4">
          <input
            v-model="search"
            type="text"
            placeholder="Search leave requests..."
            class="border border-gray-300 rounded-md px-4 py-2 w-72"
          />
          <select class="border border-gray-300 rounded-md px-3 py-2 text-gray-600">
            <option>Status</option>
          </select>
          <select class="border border-gray-300 rounded-md px-3 py-2 text-gray-600">
            <option>Sort</option>
          </select>
        </div>
        <span class="text-sm text-gray-600">{{ filteredRequests.length }} requests</span>
      </div>

      <!-- Leave Request List -->
      <div class="bg-white p-6 rounded-md shadow-sm">
        <h2 class="text-lg font-semibold mb-4">Leave Requests</h2>

        <div v-for="(request, index) in filteredRequests" :key="index" class="mb-6 border-b pb-4">
          <div class="flex items-center mb-1 text-gray-800 font-medium">
            <span v-if="request.type === 'Sick Leave'" class="mr-2">🩺</span>
            <span v-else class="mr-2">❤️</span>
            {{ request.type }}
          </div>
          <div class="text-sm text-gray-600 mb-1">
            {{ request.from }} - {{ request.to }}
            <span
              class="ml-2 inline-block px-2 py-0.5 rounded-full text-xs font-medium"
              :class="{
                'bg-yellow-100 text-yellow-700': request.status === 'Pending',
                'bg-green-100 text-green-700': request.status === 'Approved',
                'bg-red-100 text-red-700': request.status === 'Rejected',
              }"
            >
              {{ request.status }}
            </span>
            <router-link to="#" class="ml-2 text-blue-600 hover:underline">View</router-link>
          </div>
          <div class="text-sm text-gray-600 mb-1">Student: {{ request.student }}</div>
          <div class="text-sm text-gray-700 mb-1">{{ request.reason }}</div>
          <div class="text-xs text-gray-500">📅 Submitted {{ request.submitted }}</div>
          <div v-if="request.status === 'Pending'" class="mt-2 space-x-3 text-sm">
            <button class="text-green-600 hover:underline">Approve</button>
            <button class="text-red-600 hover:underline">Reject</button>
          </div>
          <div v-if="request.status === 'Approved'" class="text-xs text-gray-500">✅ Approved {{ request.approvedDate }}</div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'

const search = ref('')
const leaveRequests = ref([
  {
    type: 'Sick Leave',
    from: 'Jan 15, 2024',
    to: 'Jan 17, 2024',
    status: 'Pending',
    student: 'John Doe (1)',
    reason: 'Flu symptoms',
    submitted: 'Jan 10, 2024',
  },
  {
    type: 'Family Emergency',
    from: 'Jan 20, 2024',
    to: 'Jan 22, 2024',
    status: 'Approved',
    student: 'John Doe (1)',
    reason: 'Family emergency',
    submitted: 'Jan 8, 2024',
    approvedDate: 'Jan 9, 2024',
  },
])

const filteredRequests = computed(() => {
  return leaveRequests.value.filter((req) =>
    req.type.toLowerCase().includes(search.value.toLowerCase()) ||
    req.reason.toLowerCase().includes(search.value.toLowerCase())
  )
})
</script>

<style scoped>
/* Optional styling */
</style>
