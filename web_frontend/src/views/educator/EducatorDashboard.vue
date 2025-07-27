<template>
  <div class="min-h-screen bg-gray-100 text-gray-800 font-sans">
    <div class="px-6 py-6">
      <h2 class="text-2xl font-bold mb-1">Educator Dashboard</h2>
      <p class="text-gray-600">Review and manage student leave requests</p>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 px-6 mb-6">
      <!-- Pending -->
      <div class="bg-white p-5 rounded-lg shadow-sm flex items-center space-x-4">
        <div class="bg-yellow-100 p-2 rounded-full">
          <svg class="w-6 h-6 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
        </div>
        <div>
          <div class="text-sm text-gray-500 font-semibold">Pending Review</div>
          <p class="text-gray-900 text-xl font-bold">{{ pendingCount }}</p>
        </div>
      </div>

      <!-- Approved Today -->
      <div class="bg-white p-5 rounded-lg shadow-sm flex items-center space-x-4">
        <div class="bg-green-100 p-2 rounded-full">
          <svg class="w-6 h-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
        </div>
        <div>
          <div class="text-sm text-gray-500 font-semibold">Approved Today</div>
          <p class="text-gray-900 text-xl font-bold">{{ approvedTodayCount }}</p>
        </div>
      </div>

      <!-- Total -->
      <div class="bg-white p-5 rounded-lg shadow-sm flex items-center space-x-4">
        <div class="bg-blue-100 p-2 rounded-full">
          <svg class="w-6 h-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
          </svg>
        </div>
        <div>
          <div class="text-sm text-gray-500 font-semibold">Total Requests</div>
          <p class="text-gray-900 text-xl font-bold">{{ leaveRequests.length }}</p>
        </div>
      </div>
    </div>

    <!-- Search -->
    <div class="px-6 mt-4 flex flex-wrap gap-4 items-center">
      <div class="relative flex items-center">
        <svg class="absolute left-3 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
        </svg>
        <input v-model="search" type="text" placeholder="Search by student name..."
          class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg w-full sm:w-80 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" />
      </div>
    </div>

    <!-- Table -->
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
            <tr v-for="(request, index) in paginatedRequests" :key="index">
              <td class="py-4 px-6 flex items-center space-x-3">
                <img
                  :src="request.profile_image"
                  alt="Profile Image"
                  class="w-10 h-10 rounded-full object-cover border border-gray-300"
                />
                <div>
                  <div class="font-medium text-gray-900">{{ request.student }}</div>
                  <div class="text-xs text-gray-500">ID: {{ request.student_id }}</div>
                </div>
              </td>
              <td class="py-4 px-6">{{ request.leave_type }}</td>
              <td class="py-4 px-6">{{ formatDuration(request.duration) }}</td>
              <td class="py-4 px-6">
                <span class="px-3 py-1 rounded-full text-xs font-semibold"
                  :class="{
                    'bg-yellow-100 text-yellow-700': request.status === 'Pending',
                    'bg-green-100 text-green-700': request.status === 'Approved',
                    'bg-red-100 text-red-700': request.status === 'Rejected'
                  }">
                  {{ request.status }}
                </span>
              </td>
              <td class="py-4 px-6">{{ formatDate(request.submitted) }}</td>
              <td class="py-4 px-6 space-x-3">
                <button @click="viewDetail(request.id)" class="text-blue-600 hover:underline font-semibold">View</button>
                <button v-if="request.status === 'Pending'" @click="approve(request.id)" class="text-green-600 hover:underline font-semibold">Approve</button>
                <button v-if="request.status === 'Pending'" @click="reject(request.id)" class="text-red-600 hover:underline font-semibold">Reject</button>
              </td>
            </tr>
          </tbody>
        </table>

        <!-- Pagination -->
        <div class="flex justify-center items-center space-x-4 py-4">
          <button
            :disabled="currentPage === 1"
            @click="currentPage--"
            class="px-3 py-1 rounded bg-gray-200 hover:bg-gray-300 "
          >
             Prev
          </button>
          <div>Page {{ currentPage }} of {{ totalPages }}</div>
          <button
            :disabled="currentPage === totalPages"
            @click="currentPage++"
            class="px-3 py-1 rounded bg-gray-200 hover:bg-gray-300 disabled:opacity-50"
          >
            Next
          </button>
        </div>
      </div>
    </div>

    <!-- Detail Modal -->
    <div v-if="showDetail" class="fixed inset-0 z-50 bg-black bg-opacity-50 flex items-center justify-center">
      <div class="bg-white p-6 rounded-lg w-full max-w-md space-y-3">
        <h3 class="text-xl font-bold flex items-center space-x-3">
          <img
            v-if="detail.profile_image"
            :src="detail.profile_image"
            alt="Profile Image"
            class="w-16 h-16 rounded-full object-cover border border-gray-300"
          />
          <span>{{ detail.student }} - {{ detail.leave_type }}</span>
        </h3>
        <p><strong>From:</strong> {{ formatDate(detail.from_date) }}</p>
        <p><strong>To:</strong> {{ formatDate(detail.to_date) }}</p>
        <p><strong>Reason:</strong> {{ detail.reason }}</p>
        <p><strong>Contact:</strong> {{ detail.contact_info }}</p>
        <p v-if="detail.supporting_documents">
          <a :href="detail.supporting_documents" target="_blank" class="text-blue-600 underline">View Document</a>
        </p>
        <p><strong>Status:</strong> {{ detail.status }}</p>
        <p v-if="detail.approved_by"><strong>Approved by:</strong> {{ detail.approved_by }}</p>
        <p v-if="detail.rejection_reason"><strong>Rejection Reason:</strong> {{ detail.rejection_reason }}</p>
        <button @click="showDetail = false" class="mt-2 text-gray-600 underline">Close</button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import axios from 'axios'

const search = ref('')
const leaveRequests = ref([])
const showDetail = ref(false)
const detail = ref({})
const currentPage = ref(1)
const perPage = 5

const formatDate = (dateStr) => {
  const options = { year: 'numeric', month: 'long', day: 'numeric' }
  return new Date(dateStr).toLocaleDateString('en-US', options)
}

const formatDuration = (range) => {
  const [from, to] = range.split(' to ')
  return `${formatDate(from)} to ${formatDate(to)}`
}

const fetchLeaveRequests = async () => {
  try {
    const res = await axios.get('http://127.0.0.1:8000/api/educator/leave-requests', {
      headers: { Authorization: `Bearer ${localStorage.getItem('token')}` }
    })
    leaveRequests.value = res.data
  } catch (err) {
    console.error('Error loading requests:', err)
  }
}

const viewDetail = async (id) => {
  const res = await axios.get(`http://127.0.0.1:8000/api/educator/leave-request/${id}`, {
    headers: { Authorization: `Bearer ${localStorage.getItem('token')}` }
  })
  detail.value = res.data.leave_request
  showDetail.value = true
}

const approve = async (id) => {
  await axios.post(`http://127.0.0.1:8000/api/educator/leave-request/${id}/approve`, {}, {
    headers: { Authorization: `Bearer ${localStorage.getItem('token')}` }
  })
  alert('Approved!')
  showDetail.value = false
  fetchLeaveRequests()
}

const reject = async (id) => {
  const reason = prompt('Enter reason for rejection:')
  if (!reason) return alert('Rejection reason is required')
  await axios.post(`http://127.0.0.1:8000/api/educator/leave-request/${id}/reject`, {
    rejection_reason: reason
  }, {
    headers: { Authorization: `Bearer ${localStorage.getItem('token')}` }
  })
  alert('Rejected')
  showDetail.value = false
  fetchLeaveRequests()
}

const filteredRequests = computed(() => {
  if (!search.value) return leaveRequests.value
  return leaveRequests.value.filter(r =>
    r.student.toLowerCase().includes(search.value.toLowerCase())
  )
})

const paginatedRequests = computed(() => {
  const start = (currentPage.value - 1) * perPage
  return filteredRequests.value.slice(start, start + perPage)
})

const totalPages = computed(() =>
  Math.ceil(filteredRequests.value.length / perPage)
)

const pendingCount = computed(() =>
  leaveRequests.value.filter(r => r.status === 'Pending').length
)

const approvedTodayCount = computed(() => {
  const today = new Date().toISOString().split('T')[0]
  return leaveRequests.value.filter(r =>
    r.status === 'Approved' && r.submitted.startsWith(today)
  ).length
})

onMounted(fetchLeaveRequests)
</script>

<style scoped>
/* Tailwind CSS is used */
</style>
