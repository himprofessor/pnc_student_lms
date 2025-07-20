<template>
  <div class="min-h-screen bg-gray-50 text-gray-800">
   
    <div class="px-6 py-6 max-w-4xl mx-auto">
      <div class="flex justify-between items-center mb-2">
        <div>
          <h1 class="text-2xl font-bold">Leave History</h1>
          <p class="text-sm text-gray-500">View all your previous leave requests and their status</p>
        </div>
        <button class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm font-medium flex items-center space-x-1">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
          <span>New Leave Request</span>
        </button>
      </div>

      <div class="bg-white p-4 rounded-lg shadow border mb-6 flex items-center space-x-4">
        <div class="relative flex-1">
          <input type="text" placeholder="Search leave requests..." class="border border-gray-300 p-2 pl-10 rounded-md w-full focus:outline-none focus:ring-blue-500 focus:border-blue-500" />
          <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
          </div>
        </div>
        <div class="relative">
          <select class="border border-gray-300 p-2 pr-8 rounded-md appearance-none focus:outline-none focus:ring-blue-500 focus:border-blue-500">
            <option>All Statuses</option>
            <option>Pending</option>
            <option>Approved</option>
            <option>Rejected</option>
          </select>
          <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700">
            <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
              <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
            </svg>
          </div>
        </div>
        <div class="relative">
          <select class="border border-gray-300 p-2 pr-8 rounded-md appearance-none focus:outline-none focus:ring-blue-500 focus:border-blue-500">
            <option>All Types</option>
            <option>Sick Leave</option>
            <option>Family Emergency</option>
          </select>
          <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700">
            <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
              <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
            </svg>
          </div>
        </div>
        <span class="text-gray-600 text-sm whitespace-nowrap">2 requests</span>
      </div>


      <div class="bg-white rounded-lg shadow border">
        <div class="border-b px-6 py-4">
          <h2 class="text-lg font-semibold">Leave Requests</h2>
          <p class="text-sm text-gray-500">Your leave request history</p>
        </div>

        <div class="divide-y">
          <div class="flex justify-between items-center px-6 py-4">
            <div class="flex items-start space-x-3">
              <svg class="w-6 h-6 text-gray-400 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
              <div>
                <h3 class="font-semibold">Sick Leave</h3>
                <p class="text-sm text-gray-500">Jan 15, 2024 - Jan 17, 2024</p>
                <p class="text-sm text-gray-500 mt-1">Flu symptoms</p>
                <p class="text-xs text-gray-400 mt-1">Submitted Jan 10, 2024</p>
              </div>
            </div>
            <div class="flex items-center space-x-3">
              <span class="bg-yellow-100 text-yellow-700 text-xs font-medium px-2 py-1 rounded">Pending</span>
              <a href="#" class="text-blue-600 text-sm font-medium hover:underline">View</a>
              <button class="text-red-500 text-xs font-medium hover:underline">Cancel</button>
            </div>
          </div>

          <div class="flex justify-between items-center px-6 py-4">
            <div class="flex items-start space-x-3">
              <svg class="w-6 h-6 text-gray-400 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h2a2 2 0 002-2V7a2 2 0 00-2-2h-3M7 11v10a2 2 0 002 2h6a2 2 0 002-2v-3m-7-3V7a2 2 0 012-2h6a2 2 0 012 2v6m-7 3v4"></path></svg>
              <div>
                <h3 class="font-semibold">Family Emergency</h3>
                <p class="text-sm text-gray-500">Jan 20, 2024 - Jan 22, 2024</p>
                <p class="text-sm text-gray-500 mt-1">Family emergency</p>
                <p class="text-xs text-gray-400 mt-1">Submitted Jan 8, 2024</p>
                <p class="text-xs text-gray-400">Approved Jan 9, 2024</p>
              </div>
            </div>
            <div class="flex items-center space-x-3">
              <span class="bg-green-100 text-green-700 text-xs font-medium px-2 py-1 rounded">Approved</span>
              <a href="#" class="text-blue-600 text-sm font-medium hover:underline">View</a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'

const leaveRequests = ref([
  {
    title: 'Sick Leave',
    dateRange: 'Jan 15, 2024 - Jan 17, 2024',
    reason: 'Flu symptoms',
    submittedDate: 'Jan 10, 2024',
    approvedDate: null,
    status: 'Pending',
  },
  {
    title: 'Family Emergency',
    dateRange: 'Jan 20, 2024 - Jan 22, 2024',
    reason: 'Family emergency',
    submittedDate: 'Jan 8, 2024',
    approvedDate: 'Jan 9, 2024',
    status: 'Approved',
  },
])
</script>

<style scoped>
/* You might need to include Tailwind CSS in your project for these classes to work */
</style>