<template>
  <div class="min-h-screen bg-gray-100 text-gray-800 font-sans">
    <div class="px-6 py-6">
      <h2 class="text-2xl font-bold mb-1">Educator Dashboard</h2>
      <p class="text-gray-600">Review and manage student leave requests</p>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 px-6 mb-6">
      <div class="bg-white p-5 rounded-lg shadow-sm flex items-center space-x-4">
        <div class="bg-yellow-100 p-2 rounded-full">
          <svg class="w-6 h-6 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
        </div>
        <div>
          <div class="text-sm text-gray-500 font-semibold">Pending Review</div>
          <p class="text-gray-900 text-xl font-bold">1</p>
        </div>
      </div>
      <div class="bg-white p-5 rounded-lg shadow-sm flex items-center space-x-4">
        <div class="bg-green-100 p-2 rounded-full">
          <svg class="w-6 h-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
        </div>
        <div>
          <div class="text-sm text-gray-500 font-semibold">Approved Today</div>
          <p class="text-gray-900 text-xl font-bold">0</p>
        </div>
      </div>
      <div class="bg-white p-5 rounded-lg shadow-sm flex items-center space-x-4">
        <div class="bg-blue-100 p-2 rounded-full">
          <svg class="w-6 h-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
        </div>
        <div>
          <dev class="text-sm text-gray-500 font-semibold">Total Requests</dev>
          <p class="text-gray-900 text-xl font-bold">2</p>
        </div>
      </div>
    </div>

    <div class="px-6 mt-4 flex flex-wrap gap-4 items-center">
      <div class="relative flex items-center">
        <svg class="absolute left-3 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
        <input
          v-model="search"
          type="text"
          placeholder="Search by student name..."
          class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg w-full sm:w-80 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
        />
      </div>
      <div class="relative">
        <select class="block appearance-none w-full bg-white border border-gray-300 text-gray-700 py-2 px-4 pr-8 rounded-lg leading-tight focus:outline-none focus:bg-white focus:border-blue-500">
          <option>All Statuses</option>
          <option>Pending</option>
          <option>Approved</option>
          <option>Rejected</option>
        </select>
        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700">
          <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z"/></svg>
        </div>
      </div>
    </div>

    <div class="mt-6 px-6">
      <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <h3 class="text-lg font-semibold px-6 pt-4 text-gray-700">Leave Requests</h3>
        <p class="text-gray-600 px-6 pb-4">Review and manage student leave requests</p>
        <table class="min-w-full text-sm text-left">
          <thead class="bg-gray-50 text-gray-600 uppercase tracking-wider">
            <tr>
              <th class="py-3 px-6 font-medium">Student</th>
              <th class="py-3 px-6 font-medium">Leave Type</th>
              <th class="py-3 px-6 font-medium">Duration</th>
              <th class="py-3 px-6 font-medium">Status</th>
              <th class="py-3 px-6 font-medium">Submitted</th>
              <th class="py-3 px-6 font-medium">Actions</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-200">
            <tr v-for="(request, index) in filteredRequests" :key="index">
              <td class="py-4 px-6 flex items-center space-x-3">
                <div class="w-8 h-8 rounded-full bg-blue-500 text-white flex items-center justify-center text-sm font-semibold">
                  {{ request.student[0] }}
                </div>
                <div>
                  <p class="font-medium text-gray-900">{{ request.student }}</p>
                  <p class="text-xs text-gray-500">ID: {{ request.id }}</p>
                </div>
              </td>
              <td class="py-4 px-6 text-gray-700">{{ request.type }}</td>
              <td class="py-4 px-6 text-gray-700">{{ request.duration }}</td>
              <td class="py-4 px-6">
                <span
                  class="px-3 py-1 rounded-full text-xs font-semibold"
                  :class="{
                    'bg-yellow-100 text-yellow-700': request.status === 'Pending',
                    'bg-green-100 text-green-700': request.status === 'Approved',
                    'bg-red-100 text-red-700': request.status === 'Rejected',
                  }"
                >
                  {{ request.status }}
                </span>
              </td>
              <td class="py-4 px-6 text-gray-700">{{ request.submitted }}</td>
              <td class="py-4 px-6 space-x-3">
                <button class="text-blue-600 hover:text-blue-800 font-medium">View</button>
                <button
                  v-if="request.status === 'Pending'"
                  class="text-green-600 hover:text-green-800 font-medium"
                >
                  Approve
                </button>
                <button
                  v-if="request.status === 'Pending'"
                  class="text-red-600 hover:text-red-800 font-medium"
                >
                  Reject
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'

const search = ref('')

const leaveRequests = ref([
  {
    id: 1,
    student: 'John Doe',
    type: 'Sick Leave',
    duration: 'Jan 15, 2024 - Jan 17, 2024',
    status: 'Pending',
    submitted: 'Jan 10, 2024',
  },
  {
    id: 1,
    student: 'John Doe',
    type: 'Family Emergency',
    duration: 'Jan 20, 2024 - Jan 22, 2024',
    status: 'Approved',
    submitted: 'Jan 8, 2024',
  },
])

const filteredRequests = computed(() => {
  if (!search.value) return leaveRequests.value
  return leaveRequests.value.filter((r) =>
    r.student.toLowerCase().includes(search.value.toLowerCase())
  )
})
</script>

<style scoped>
/* No specific styles needed beyond Tailwind for this layout */
</style>