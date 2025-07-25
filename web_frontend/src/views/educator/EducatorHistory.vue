<template>
  <div class="min-h-screen bg-gradient-to-b from-gray-50 to-gray-100 text-gray-800">
    <!-- Main Content -->
    <div class="max-w-5xl mx-auto px-6 py-10">
      <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-2">Leave Request Dashboard</h1>
        <p class="text-gray-600">Review and manage all processed leave applications</p>
      </div>

      <!-- Enhanced Filters Card -->
      <div class="bg-white p-6 rounded-lg shadow-md mb-8 border border-gray-100">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-4">
          <div class="relative flex-1">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 absolute left-3 top-3 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
            <input 
              v-model="search" 
              type="text" 
              placeholder="Search by student name or reason..."
              class="w-full pl-10 pr-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all"
            />
          </div>
          <span class="text-sm font-medium text-gray-600 bg-gray-100 px-3 py-1 rounded-full">
            {{ filteredRequests.length }} {{ filteredRequests.length === 1 ? 'request' : 'requests' }}
          </span>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
            <select 
              v-model="statusFilter" 
              class="w-full border border-gray-200 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all"
            >
              <option value="">All Statuses</option>
              <option class="text-green-600">Approved</option>
              <option class="text-red-600">Rejected</option>
            </select>
          </div>
          
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Leave Type</label>
            <select 
              v-model="typeFilter" 
              class="w-full border border-gray-200 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all"
            >
              <option value="">All Leave Types</option>
              <option>Sick Leave</option>
              <option>Family Emergency</option>
              <option>Personal Leave</option>
              <option>Vacation Leave</option>
              <option>Emergency Leave</option>
              <option>Maternity Leave</option>
              <option>Paternity Leave</option>
              <option>Bereavement Leave</option>
              <option>Unpaid Leave</option>
              <option>Compassionate Leave</option>
              <option>Study Leave</option>
              <option>Public Holiday Leave</option>
            </select>
          </div>
        </div>
      </div>

      <!-- Leave Request List -->
      <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center">
          <h2 class="text-lg font-semibold text-gray-800">Processed Requests</h2>
          <button 
            @click="resetFilters"
            class="text-sm font-medium text-blue-600 hover:text-blue-800 flex items-center gap-1 transition-colors"
          >
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
            </svg>
            Reset Filters
          </button>
        </div>

        <div v-if="filteredRequests.length === 0" class="text-center py-12">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
          <p class="mt-4 text-gray-500 font-medium">No matching leave requests found</p>
          <p class="text-sm text-gray-400">Try adjusting your search or filters</p>
        </div>

        <div v-for="(request, index) in filteredRequests" :key="index" class="px-6 py-4 border-b border-gray-100 hover:bg-gray-50 transition-colors">
          <div class="flex items-start justify-between">
            <div class="flex items-center gap-3">
              <div class="p-2 rounded-full" :class="{
                'bg-blue-100 text-blue-600': request.type === 'Sick Leave',
                'bg-purple-100 text-purple-600': request.type === 'Family Emergency',
                'bg-green-100 text-green-600': request.type === 'Personal Leave',
                'bg-gray-100 text-gray-600': !['Sick Leave', 'Family Emergency', 'Personal Leave'].includes(request.type)
              }">
                <span v-if="request.type === 'Sick Leave'" class="text-lg">🩺</span>
                <span v-else-if="request.type === 'Family Emergency'" class="text-lg">❤️</span>
                <span v-else class="text-lg">📅</span>
              </div>
              <div>
                <h3 class="font-medium text-gray-900">{{ request.type }}</h3>
                <p class="text-sm text-gray-500">{{ request.student }}</p>
              </div>
            </div>
            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium" :class="{
              'bg-green-100 text-green-800': request.status === 'Approved',
              'bg-red-100 text-red-800': request.status === 'Rejected',
            }">
              {{ request.status }}
            </span>
          </div>

          <div class="mt-3 pl-11">
            <div class="flex flex-wrap items-center gap-4 text-sm text-gray-600 mb-2">
              <div class="flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                {{ request.from }} - {{ request.to }}
              </div>
              <div class="flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                Submitted {{ request.submitted }}
              </div>
            </div>

            <div class="mb-2">
              <p class="text-sm text-gray-700"><span class="font-medium">Reason:</span> {{ request.reason }}</p>
            </div>

            <div v-if="request.status === 'Approved'" class="flex items-center text-sm text-green-600 mt-2">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
              </svg>
              Approved on {{ request.approvedDate }}
            </div>

            <div v-if="request.status === 'Rejected'" class="mt-2">
              <div class="flex items-center text-sm text-red-600">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
                Rejected on {{ request.rejectedDate || request.approvedDate }}
              </div>
              <div v-if="request.rejectionComment" class="mt-2 text-xs bg-red-50 p-2 rounded-md border border-red-100">
                <p class="font-medium text-red-800">Administrator's Note:</p>
                <p class="text-red-700">{{ request.rejectionComment }}</p>
              </div>
            </div>

            <div class="mt-3">
              <button class="text-sm font-medium text-blue-600 hover:text-blue-800 flex items-center gap-1 transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                </svg>
                View Full Details
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'

const search = ref('')
const statusFilter = ref('')
const typeFilter = ref('')

const leaveRequests = ref([
  {
    type: 'Sick Leave',
    from: 'Jan 15, 2025',
    to: 'Jan 17, 2025',
    status: 'Approved',
    student: 'Sela Torm (Web 2025A)',
    reason: 'Diagnosed with influenza, doctor\'s note provided',
    submitted: 'Jan 10, 2025',
    approvedDate: 'Jan 11, 2025'
  },
  {
    type: 'Family Emergency',
    from: 'Jan 20, 2025',
    to: 'Jan 22, 2025',
    status: 'Approved',
    student: 'Nangkhoeum Champhai (Web 2025B)',
    reason: 'Immediate family member hospitalized',
    submitted: 'Jan 8, 2025',
    approvedDate: 'Jan 9, 2025'
  },
  {
    type: 'Personal Leave',
    from: 'Feb 5, 2025',
    to: 'Feb 6, 2025',
    status: 'Rejected',
    student: 'Mesa Nath (Web 2025C)',
    reason: 'Personal travel plans',
    submitted: 'Jan 28, 2025',
    rejectedDate: 'Jan 30, 2025',
    rejectionComment: "Personal leave cannot be granted during mid-term assessment period (Feb 1-15)."
  },
  {
    type: 'Sick Leave',
    from: 'Mar 10, 2025',
    to: 'Mar 12, 2025',
    status: 'Rejected',
    student: 'Darin Hoy (Web 2025A)',
    reason: 'Severe migraine headaches',
    submitted: 'Mar 5, 2025',
    rejectedDate: 'Mar 6, 2025',
    rejectionComment: "Medical certificate required for sick leave exceeding one day."
  },
  {
    type: 'Family Emergency',
    from: 'Apr 2, 2025',
    to: 'Apr 5, 2025',
    status: 'Approved',
    student: 'Vanda Mann (Web 2025B)',
    reason: 'Sibling wedding in another province',
    submitted: 'Mar 20, 2025',
    approvedDate: 'Mar 22, 2025'
  },
  {
    type: 'Study Leave',
    from: 'May 15, 2025',
    to: 'May 16, 2025',
    status: 'Approved',
    student: 'Chhorrina Thin (Web 2025C)',
    reason: 'National academic competition participation',
    submitted: 'May 1, 2025',
    approvedDate: 'May 2, 2025'
  }
])

const filteredRequests = computed(() => {
  return leaveRequests.value.filter((req) => {
    const matchesSearch =
      req.student.toLowerCase().includes(search.value.toLowerCase()) ||
      req.reason.toLowerCase().includes(search.value.toLowerCase())

    const matchesStatus = statusFilter.value === '' || req.status === statusFilter.value
    const matchesType = typeFilter.value === '' || req.type === typeFilter.value

    return matchesSearch && matchesStatus && matchesType
  })
})

const resetFilters = () => {
  search.value = ''
  statusFilter.value = ''
  typeFilter.value = ''
}
</script>

<style scoped>
/* Smooth transitions for interactive elements */
button, select, input {
  transition: all 0.2s ease;
}

/* Better focus states */
input:focus, select:focus {
  outline: none;
  box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.2);
}
</style>