<template>
  <div class="min-h-screen bg-gray-50 text-gray-800">
    <!-- Main Content -->
    <div class="max-w-5xl mx-auto px-6 py-10">
      <h1 class="text-2xl font-bold mb-1">Leave Requests History</h1>
      <p class="text-gray-500 mb-6">Track all leave requests you've approved or rejected</p>

      <!-- Enhanced Filters -->
      <div class="bg-white p-4 rounded-md shadow-sm mb-6">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
          <div class="flex flex-col sm:flex-row gap-3 w-full">
            <input v-model="search" type="text" placeholder="Search by student name or reason..."
              class="border border-gray-300 rounded-md px-4 py-2 flex-grow" />
            <select v-model="statusFilter"
              class="border border-gray-300 rounded-md px-3 py-2 text-gray-600 w-full sm:w-40">
              <option value="all">All Statuses</option>
              <option value="Approved">Approved</option>
              <option value="Rejected">Rejected</option>
            </select>
            <select v-model="typeFilter"
              class="border border-gray-300 rounded-md px-3 py-2 text-gray-600 w-full sm:w-40">
              <option value="all">All Types</option>
              <option value="Sick Leave">Sick Leave</option>
              <option value="Family Emergency">Family Emergency</option>
              <option value="Personal Leave">Personal Leave</option>
              <option value="Vacation">Vacation</option>
              <option value="Paternity Leave">Paternity Leave</option>
              <option value="Maternity Leave">Maternity Leave</option>
              <option value="Bereavement Leave">Bereavement Leave</option>
              <option value="Unpaid Leave">Unpaid Leave</option>
              <option value="Compassionate Leave">Compassionate Leave</option>
              <option value="Study Leave">Study Leave</option>
              <option value="Public Holiday Leave">Public Holiday Leave</option>
            </select>
          </div>
          <div class="flex items-center gap-3">
            <div class="text-sm text-gray-600 whitespace-nowrap">{{ filteredRequests.length }} requests</div>
            <button @click="resetFilters" class="text-sm text-blue-600 hover:underline whitespace-nowrap">
              Reset filters
            </button>
          </div>
        </div>
      </div>

      <!-- Leave Request List -->
      <div class="bg-white p-6 rounded-md shadow-sm">

        <div v-if="filteredRequests.length === 0" class="text-center py-10 text-gray-500">
          No leave requests match your current filters
        </div>

        <div v-for="(request, index) in filteredRequests" :key="index" class="mb-6 border-b pb-4 last:border-b-0">
          <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-2">
            <div class="flex items-center mb-1 text-gray-800 font-medium">
              <span v-if="request.type === 'Sick Leave'" class="mr-2">🩺</span>
              <span v-else-if="request.type === 'Family Emergency'" class="mr-2">❤️</span>
              <span v-else class="mr-2">📅</span>
              {{ request.type }}
              <span class="ml-3 inline-block px-2 py-0.5 rounded-full text-xs font-medium" :class="{
                'bg-yellow-100 text-yellow-700': request.status === 'Pending',
                'bg-green-100 text-green-700': request.status === 'Approved',
                'bg-red-100 text-red-700': request.status === 'Rejected',
              }">
                {{ request.status }}
              </span>
            </div>
            <div class="text-sm text-gray-500">
              📅 {{ request.from }} - {{ request.to }}
            </div>
          </div>

          <div class="text-sm text-gray-600 mb-1">
            <span class="font-medium">Student:</span> {{ request.student }}
          </div>
          <div class="text-sm text-gray-700 mb-2">
            <span class="font-medium">Reason:</span> {{ request.reason }}
          </div>

          <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between text-xs text-gray-500">
            <div>Submitted {{ request.submitted }}</div>
            <div v-if="request.status === 'Approved'" class="mt-1 sm:mt-0">
              ✅ Approved by you on {{ request.approvedDate }}
            </div>
            <div v-if="request.status === 'Rejected'" class="mt-1 sm:mt-0">
              ❌ Rejected by you on {{ request.rejectedDate }}
            </div>
          </div>

          <div v-if="request.notes" class="mt-2 p-2 bg-blue-50 text-sm text-gray-700 rounded">
            <span class="font-medium">Your note:</span> {{ request.notes }}
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'

const search = ref('')
const statusFilter = ref('all')
const typeFilter = ref('all')
const dateFrom = ref('')
const dateTo = ref('')

const leaveRequests = ref([
  {
    type: 'Sick Leave',
    from: '2024-01-15',
    to: '2024-01-17',
    status: 'Approved',
    student: 'Sela Torm (Web A)',
    reason: 'Flu symptoms with doctor\'s note provided',
    submitted: 'Jan 10, 2024 at 10:30 AM',
    approvedDate: 'Jan 10, 2024 at 3:15 PM',
  },
  {
    type: 'Family Emergency',
    from: '2024-01-20',
    to: '2024-01-22',
    status: 'Approved',
    student: 'Chhorrina Thin (Web C)',
    reason: 'Family emergency out of town',
    submitted: 'Jan 8, 2024 at 2:45 PM',
    approvedDate: 'Jan 9, 2024 at 9:00 AM',
  },
  {
    type: 'Maternity Leave',
    from: '2024-02-20',
    to: '2024-02-25',
    status: 'Rejected',
    student: 'Tena (Web A)',
    reason: 'New baby born',
    submitted: 'Feb 10, 2024 at 9:30 AM',
    approvedDate: 'Feb 11, 2024 at 1:00 PM',
  },
  {
    type: 'Personal Leave',
    from: '2024-02-05',
    to: '2024-02-05',
    status: 'Approved',
    student: 'Nangkhoeum Champhai (Web B)',
    reason: 'Attending concert',
    submitted: 'Jan 30, 2024 at 11:20 AM',
    rejectedDate: 'Jan 31, 2024 at 10:15 AM',
  },
  {
    type: 'Bereavement Leave',
    from: '2024-01-28',
    to: '2024-01-30',
    status: 'Rejected',
    student: 'Yuhai (Web B)',
    reason: 'Family bereavement',
    submitted: 'Jan 25, 2024 at 3:00 PM',
    approvedDate: 'Jan 26, 2024 at 10:00 AM',
  },
  {
    type: 'Public Holiday Leave',
    from: '2024-02-05',
    to: '2024-02-05',
    status: 'Approved',
    student: 'Mesa Nath (Web C)',
    reason: 'I go go to my hometown',
    submitted: 'Jan 30, 2024 at 11:20 AM',
    rejectedDate: 'Jan 31, 2024 at 10:15 AM',
  },
  {
    type: 'Vacation',
    from: '2024-01-25',
    to: '2024-01-30',
    status: 'Rejected',
    student: 'Darin Hoy (Web C)',
    reason: 'Family vacation planned',
    submitted: 'Jan 20, 2024 at 9:00 AM',
    rejectedDate: 'Jan 21, 2024 at 4:00 PM',
  },
  {
    type: 'Paternity Leave',
    from: '2024-02-10',
    to: '2024-02-15',
    status: 'Rejected',
    student: 'Vanda Mann (Web B)',
    reason: 'New baby born',
    submitted: 'Feb 1, 2024 at 8:00 AM',
    approvedDate: 'Feb 2, 2024 at 12:00 PM',
  },
])

const filteredRequests = computed(() => {
  return leaveRequests.value.filter(request => {
    // Search filter
    const matchesSearch =
      request.student.toLowerCase().includes(search.value.toLowerCase()) ||
      request.reason.toLowerCase().includes(search.value.toLowerCase())

    // Status filter
    const matchesStatus =
      statusFilter.value === 'all' ||
      request.status === statusFilter.value

    // Type filter
    const matchesType =
      typeFilter.value === 'all' ||
      request.type === typeFilter.value

    // Date range filter
    const matchesDate = (!dateFrom.value || request.from >= dateFrom.value) &&
      (!dateTo.value || request.to <= dateTo.value)

    return matchesSearch && matchesStatus && matchesType && matchesDate
  })
})

const resetFilters = () => {
  search.value = ''
  statusFilter.value = 'all'
  typeFilter.value = 'all'
  dateFrom.value = ''
  dateTo.value = ''
}
</script>

<style scoped>
/* Responsive adjustments */
@media (max-width: 640px) {
  .flex-col>* {
    width: 100%;
  }
}
</style>