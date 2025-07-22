<template>
  <div class="min-h-screen bg-gray-50 text-gray-800">
    <div class="px-6 py-6">
      <!-- Header -->
      <div class="flex justify-between items-center mb-6">
        <div>
          <h1 class="text-2xl font-bold">Welcome back {{ user.name || user.full_name || 'User' }}</h1>
          <p class="text-sm text-gray-500">Manage your leave requests and track their status</p>
        </div>
        <button
          class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm font-medium flex items-center space-x-1"
          @click="$router.push('/request-leave')"
        >
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
          </svg>
          <span>New Leave Request</span>
        </button>
      </div>

      <!-- Summary Cards -->
      <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4 mb-8">
        <div class="bg-white p-4 rounded-lg shadow border flex items-center space-x-3">
          <div class="text-yellow-500 text-2xl p-2 bg-yellow-100 rounded-full">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
          </div>
          <div>
            <div class="text-sm text-gray-500 font-semibold">Pending</div>
            <div class="text-2xl font-bold">{{ pendingCount }}</div>
          </div>
        </div>
        <div class="bg-white p-4 rounded-lg shadow border flex items-center space-x-3">
          <div class="text-green-500 text-2xl p-2 bg-green-100 rounded-full">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
          </div>
          <div>
            <div class="text-sm text-gray-500 font-semibold">Approved</div>
            <div class="text-2xl font-bold">{{ approvedCount }}</div>
          </div>
        </div>
        <div class="bg-white p-4 rounded-lg shadow border flex items-center space-x-3">
          <div class="text-red-500 text-2xl p-2 bg-red-100 rounded-full">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M6 18L18 6M6 6l12 12" />
            </svg>
          </div>
          <div>
            <div class="text-sm text-gray-500 font-semibold">Rejected</div>
            <div class="text-2xl font-bold">{{ rejectedCount }}</div>
          </div>
        </div>
        <div class="bg-white p-4 rounded-lg shadow border flex items-center space-x-3">
          <div class="text-blue-500 text-2xl p-2 bg-blue-100 rounded-full">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
          </div>
          <div>
            <div class="text-sm text-gray-500 font-semibold">Total</div>
            <div class="text-2xl font-bold">{{ leaveRequests.length }}</div>
          </div>
        </div>
      </div>

      <!-- Recent Leave Requests -->
      <div class="bg-white rounded-lg shadow border">
        <div class="border-b px-6 py-4">
          <h2 class="text-lg font-semibold">Recent Leave Requests</h2>
          <p class="text-sm text-gray-500">Your latest leave requests and their status</p>
        </div>

        <div class="divide-y" v-if="pagedLeaveRequests.length > 0">
          <div v-for="(request, index) in pagedLeaveRequests" :key="index" class="flex justify-between items-center px-6 py-4">
            <div class="flex items-center space-x-3">
              <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2"/>
              </svg>
              <div>
                <div class="font-semibold">{{ request.leave_type }}</div>
                <div class="text-sm text-gray-500">{{ formatDate(request.from_date) }} - {{ formatDate(request.to_date) }}</div>
                <div class="text-sm text-gray-500">{{ request.reason }}</div>
              </div>
            </div>
            <div class="flex items-center space-x-2">
              <span
                class="text-xs font-medium px-2 py-1 rounded capitalize"
                :class="{
                  'bg-yellow-100 text-yellow-700': request.status === 'pending',
                  'bg-green-100 text-green-700': request.status === 'approved',
                  'bg-red-100 text-red-700': request.status === 'rejected'
                }"
              >
                {{ request.status }}
              </span>

              <button
                v-if="request.status === 'pending'"
                @click="cancelLeaveRequest(request.id)"
                class="text-red-600 text-xs border border-red-500 px-2 py-1 rounded hover:bg-red-100"
              >
                Cancel
              </button>
            </div>
          </div>
        </div>

        <div v-else class="px-6 py-4 text-sm text-gray-500">
          No leave requests found.
        </div>

        <!-- Pagination Controls -->
        <div class="flex justify-center items-center space-x-4 py-4">
          <button
            class="px-3 py-1 border rounded disabled:opacity-50"
            :disabled="currentPage === 1"
            @click="goToPage(currentPage - 1)"
          >
            Prev
          </button>

          <span>Page {{ currentPage }} of {{ totalPages }}</span>

          <button
            class="px-3 py-1 border rounded disabled:opacity-50"
            :disabled="currentPage === totalPages"
            @click="goToPage(currentPage + 1)"
          >
            Next
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import axios from 'axios'


const user = ref({})

onMounted(async () => {
  // Load from localStorage first
  const stored = localStorage.getItem('user_data')
  if (stored) user.value = JSON.parse(stored)
  
  // Fetch fresh data from API
  try {
    const token = localStorage.getItem('authToken')
    if (token) {
      const response = await axios.get('http://127.0.0.1:8000/api/user', {
        headers: { 'Authorization': `Bearer ${token}` }
      })
      user.value = response.data
      localStorage.setItem('user_data', JSON.stringify(user.value))
    }
  } catch (error) {
    console.error('Failed to fetch user:', error)
  }
})

const leaveRequests = ref([])
const pendingCount = ref(0)
const approvedCount = ref(0)
const rejectedCount = ref(0)

const currentPage = ref(1)
const pageSize = 5

const totalPages = computed(() => Math.ceil(leaveRequests.value.length / pageSize))

const pagedLeaveRequests = computed(() => {
  const start = (currentPage.value - 1) * pageSize
  const end = start + pageSize
  return leaveRequests.value.slice(start, end)
})

const fetchLeaveRequests = async () => {
  try {
    const token = localStorage.getItem('authToken')
    const response = await axios.get('http://127.0.0.1:8000/api/student/my-leaves', {
      headers: {
        Authorization: `Bearer ${token}`
      }
    })

    leaveRequests.value = response.data.leaves
    pendingCount.value = leaveRequests.value.filter(r => r.status === 'pending').length
    approvedCount.value = leaveRequests.value.filter(r => r.status === 'approved').length
    rejectedCount.value = leaveRequests.value.filter(r => r.status === 'rejected').length
  } catch (error) {
    console.error('Failed to fetch leave requests:', error)
  }
}

function formatDate(dateStr) {
  if (!dateStr) return ''
  const date = new Date(dateStr)
  return date.toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
  })
}

function goToPage(page) {
  if (page >= 1 && page <= totalPages.value) {
    currentPage.value = page
  }
}

const cancelLeaveRequest = async (id) => {

  try {
    const token = localStorage.getItem('authToken')
    await axios.delete(`http://127.0.0.1:8000/api/student/leave-request/${id}`, {
      headers: {
        Authorization: `Bearer ${token}`
      }
    })
    alert('Leave request canceled successfully.')
    await fetchLeaveRequests()
  } catch (error) {
    console.error('Failed to cancel leave request:', error)
    alert('Failed to cancel. Please try again.')
  }
}

onMounted(() => {
  fetchLeaveRequests()
})
</script>
