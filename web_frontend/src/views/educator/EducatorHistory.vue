ខឿម, [7/29/2025 7:55 PM]
<template>
  <div class="min-h-screen bg-gradient-to-b from-gray-50 to-gray-100 text-gray-800">
    <!-- Main Content -->
    <div class="max-w-5xl mx-auto px-6 py-10">
      <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-2">Leave Request Dashboard</h1>
        <p class="text-gray-600">Review and manage all processed leave applications.</p>
      </div>

      <!-- Enhanced Filters Card -->
      <div class="bg-white p-6 rounded-lg shadow-md mb-8 border border-gray-100">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-4">
          <div class="relative flex-1">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 absolute left-3 top-3 text-gray-400" fill="none"
              viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
            <input v-model="search" type="text" placeholder="Search by student name..."
              class="w-full pl-10 pr-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all" />
          </div>
          <div class="flex items-center gap-2">
            <span class="text-sm font-medium text-gray-600 bg-gray-100 px-3 py-1 rounded-full">
              {{ filteredRequests.length }} {{ filteredRequests.length === 1 ? 'request' : 'requests' }}
            </span>
            <button @click="toggleHiddenVisibility"
              class="text-sm font-medium flex items-center gap-1 px-4 py-1 rounded-full" :class="{
                'bg-yellow-100 text-yellow-800': showHidden,
                'bg-gray-100 text-gray-600': !showHidden
              }">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                stroke="currentColor">
                <path v-if="showHidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
              </svg>
              {{ showHidden ? 'Show Hidden' : 'Show All' }}
            </button>
          </div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
            <div class="flex gap-10">
              <button @click="statusFilter = ''"
                class="px-4 py-2 rounded-lg border transition-colors text-sm font-medium" :class="{
                  'bg-blue-100 border-blue-300 text-blue-800': statusFilter === '',
                  'bg-white border-gray-200 text-gray-600 hover:bg-gray-50': statusFilter !== ''
                }">
                All Statuses
              </button>
              <button @click="statusFilter = 'Approved'"
                class="px-4 py-2 rounded-lg border transition-colors text-sm font-medium" :class="{
                  'bg-blue-100 border-blue-300 text-blue-800': statusFilter === 'Approved',
                  'bg-white border-gray-200 text-gray-600 hover:bg-gray-50': statusFilter !== 'Approved'
                }">
                Approved
              </button>
              <button @click="statusFilter = 'Rejected'"
                class="px-4 py-2 rounded-lg border transition-colors text-sm font-medium" :class="{
                  'bg-blue-100 border-blue-300 text-blue-800': statusFilter === 'Rejected',
                  'bg-white border-gray-200 text-gray-600 hover:bg-gray-50': statusFilter !== 'Rejected'
                }">
                Rejected
              </button>
            </div>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Leave Type</label>
            <select v-model="typeFilter"
              class="w-full border border-gray-200 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">
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
          <h2 class="text-lg font-semibold text-gray-800">
            {{ showHidden ? 'Hidden Requests' : 'Processed Requests' }}
          </h2>
          <div class="flex items-center gap-3">
            <button @click="resetFilters"
              class="text-sm font-medium text-blue-600 hover:text-blue-800 flex items-center gap-1 transition-colors">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
              </svg>
              Reset Filters
            </button>
          </div>
        </div>
        <div v-if="filteredRequests.length === 0" class="text-center py-12">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto text-gray-400" fill="none"
            viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
              d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
          <p class="mt-4 text-gray-500 font-medium">
            {{ showHidden ? 'No hidden requests found' : 'No matching leave requests found' }}
          </p>
          <p class="text-sm text-gray-400">Try adjusting your search or filters</p>
        </div>

        <div v-for="(request, index) in paginatedRequests" :key="index"
          class="px-6 py-4 border-b border-gray-100 transition-colors" :class="{
            'bg-blue-50': request.is_pinned,
            'hidden-item': request.is_hidden && !showHidden,
            'hover:bg-gray-50': !(request.is_hidden && !showHidden)
          }">
          <div class="flex items-start justify-between">
            <div class="flex items-center gap-3">
              <div class="flex flex-col items-center gap-1">
                <button @click="togglePin(request)" class="text-gray-400 hover:text-yellow-500 transition-colors">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"
                      :class="{ 'text-yellow-500 fill-yellow-200': request.is_pinned }" />
                  </svg>
                </button>
                <button @click="toggleHide(request)" class="text-gray-400 hover:text-red-500 transition-colors">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                  </svg>
                </button>
              </div>
              <div class="p-2 rounded-full" :class="{
                'bg-blue-100 text-blue-600': request.leave_type === 'Sick Leave',
                'bg-purple-100 text-purple-600': request.leave_type === 'Family Emergency',
                'bg-green-100 text-green-600': request.leave_type === 'Personal Leave',
                'bg-gray-100 text-gray-600': !['Sick Leave', 'Family Emergency', 'Personal Leave'].includes(request.leave_type)
              }">
                <span v-if="request.leave_type === 'Sick Leave'" class="text-lg">🩺</span>
                <span v-else-if="request.leave_type === 'Family Emergency'" class="text-lg">❤️</span>
                <span v-else-if="request.leave_type === 'Personal Leave'" class="text-lg">🧘</span>
                <span v-else-if="request.leave_type === 'Vacation Leave'" class="text-lg">🏖</span>
                <span v-else-if="request.leave_type === 'Emergency Leave'" class="text-lg">🚨</span>
                <span v-else-if="request.leave_type === 'Maternity Leave'" class="text-lg">🤰</span>
                <span v-else-if="request.leave_type === 'Paternity Leave'" class="text-lg">👨</span>
                <span v-else-if="request.leave_type === 'Bereavement Leave'" class="text-lg">⚰️</span>
                <span v-else-if="request.leave_type === 'Unpaid Leave'" class="text-lg">💸</span>
                <span v-else-if="request.leave_type === 'Compassionate Leave'" class="text-lg">🤝</span>
                <span v-else-if="request.leave_type === 'Study Leave'" class="text-lg">📚</span>
                <span v-else-if="request.leave_type === 'Public Holiday Leave'" class="text-lg">🎉</span>
                <span v-else class="text-lg">📅</span>
              </div>
              <div>
                <h3 class="font-medium text-gray-900">{{ request.leave_type }}</h3>
                <p class="text-sm text-gray-500">{{ request.student }}</p>
              </div>
            </div>
            <div class="flex items-center gap-2">
              <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium" :class="{
                'bg-green-100 text-green-800': request.status === 'Approved',
                'bg-red-100 text-red-800': request.status === 'Rejected',
              }">
                {{ request.status }}
              </span>
            </div>
          </div>

          <div class="mt-3 pl-11">
            <div class="flex flex-wrap items-center gap-4 text-sm text-gray-600 mb-2">
              <div class="flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1 text-gray-400" fill="none"
                  viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                {{ formatDuration(request.duration) }}
              </div>
              <div class="flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1 text-gray-400" fill="none"
                  viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                Submitted {{ formatDate(request.submitted) }}
              </div>
            </div>

            <div v-if="request.status === 'Approved'" class="flex items-center text-sm text-green-600 mt-2">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24"
                stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
              </svg>
              Approved by {{ request.approved_by || 'Administrator' }}
            </div>

            <div v-if="request.status === 'Rejected'" class="mt-2">
              <div class="flex items-center text-sm text-red-600">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24"
                  stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
                Rejected
              </div>
              <div v-if="request.rejection_reason" class="mt-2 text-xs bg-red-50 p-2 rounded-md border border-red-100">
                <p class="font-medium text-red-800">Your Note:</p>
                <p class="text-red-700">{{ request.rejection_reason }}</p>
              </div>
            </div>

            <div class="mt-3">
              <button @click="viewDetail(request)"
                class="text-sm font-medium text-blue-600 hover:text-blue-800 flex items-center gap-1 transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                  stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                </svg>
                View Full Details
              </button>
            </div>
          </div>
        </div>

        <!-- Pagination -->
        <div v-if="filteredRequests.length > 0"
          class="px-6 py-4 border-t border-gray-100 flex items-center justify-center gap-4">
          <button @click="prevPage" :disabled="currentPage === 1"
            class="text-sm font-medium text-gray-600 hover:text-blue-600 disabled:text-gray-400 disabled:cursor-not-allowed">
            Prev
          </button>

          <span class="text-sm text-gray-600">
            Page {{ currentPage }} of {{ totalPages }}
          </span>

          <button @click="nextPage" :disabled="currentPage === totalPages"
            class="text-sm font-medium text-gray-600 hover:text-blue-600 disabled:text-gray-400 disabled:cursor-not-allowed">
            Next
          </button>
        </div>
      </div>
    </div>

    <!-- Enhanced Detail Modal -->
    <div v-if="showDetail" class="fixed inset-0 z-50 bg-black bg-opacity-50 flex items-center justify-center p-4">
      <div class="bg-white rounded-lg shadow-xl w-full max-w-4xl overflow-hidden">
        <!-- Modal Header -->
        <div class="bg-gray-50 px-6 py-4 border-b border-gray-200 flex justify-between items-center">
          <h3 class="text-xl font-bold text-gray-800">Leave Request Details</h3>
          <button @click="showDetail = false" class="text-gray-500 hover:text-gray-700">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
              stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>

        <!-- Modal Content -->
        <div class="flex flex-col md:flex-row">
          <!-- Left Column - Request Details -->
          <div class="p-6 md:w-2/4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <!-- Student Information -->
              <div class="col-span-2 flex items-center space-x-4 mb-4">
                <div v-if="detail.profile_image" class="flex-shrink-0">
                  <img :src="detail.profile_image" alt="Profile Image"
                    class="h-16 w-16 rounded-full object-cover border border-gray-200">
                </div>
                <div>
                  <h4 class="text-lg font-semibold text-gray-900">{{ detail.student }}</h4>
                  <p class="text-sm text-gray-500">Student</p>
                </div>
              </div>

              <!-- Leave Type -->
              <div>
                <p class="text-sm font-medium text-gray-500">Leave Type</p>
                <p class="mt-1 text-sm text-gray-900">{{ detail.leave_type }}</p>
              </div>

              <!-- Status -->
              <div>
                <p class="text-sm font-medium text-gray-500">Status</p>
                <span class="mt-1 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium" :class="{
                  'bg-green-100 text-green-800': detail.status === 'Approved',
                  'bg-red-100 text-red-800': detail.status === 'Rejected',
                }">
                  {{ detail.status }}
                </span>
              </div>

              <!-- Dates -->
              <div>
                <p class="text-sm font-medium text-gray-500">From Date</p>
                <p class="mt-1 text-sm text-gray-900">{{ formatDate(detail.from_date) }}</p>
              </div>

              <div>
                <p class="text-sm font-medium text-gray-500">To Date</p>
                <p class="mt-1 text-sm text-gray-900">{{ formatDate(detail.to_date) }}</p>
              </div>

              <!-- Duration -->
              <div class="col-span-2">
                <p class="text-sm font-medium text-gray-500">Duration</p>
                <p class="mt-1 text-sm text-gray-900">{{ formatDuration(`${detail.from_date} to ${detail.to_date}`) }}
                </p>
              </div>

              <!-- Contact Information -->
              <div class="col-span-2">
                <p class="text-sm font-medium text-gray-500">Contact Information</p>
                <p class="mt-1 text-sm text-gray-900">{{ detail.contact_info }}</p>
              </div>

              <!-- Reason -->
              <div class="col-span-2">
                <p class="text-sm font-medium text-gray-500">Reason</p>
                <p class="mt-1 text-sm text-gray-900 whitespace-pre-line">{{ detail.reason }}</p>
              </div>

              <!-- Additional Notes (if rejected) -->
              <div v-if="detail.status === 'Rejected' && detail.rejection_reason" class="col-span-2">
                <p class="text-sm font-medium text-gray-500">Rejection Reason</p>
                <p class="mt-1 text-sm text-red-600">{{ detail.rejection_reason }}</p>
              </div>

              <!-- Approved By -->
              <div v-if="detail.status === 'Approved'" class="col-span-2">
                <p class="text-sm font-medium text-gray-500">Approved By</p>
                <p class="mt-1 text-sm text-gray-900">{{ detail.approved_by || 'Administrator' }}</p>
              </div>
            </div>
          </div>

          <!-- Right Column - Document Preview -->
          <div class="border-t md:border-t-0 md:border-l border-gray-200 p-6 md:w-2/4 bg-gray-50">
            <h4 class="text-lg font-semibold text-gray-900 mb-4">Supporting Documents</h4>

            <div v-if="detail.supporting_documents" class="space-y-4">
              <!-- Image Preview -->
              <div v-if="isImage(detail.supporting_documents)"
                class="border border-gray-200 rounded-lg overflow-hidden">
                <img :src="detail.supporting_documents" alt="Supporting Document"
                  class="w-full h-auto object-contain max-h-64">
              </div>

              <!-- PDF Preview Placeholder -->
              <div v-else-if="isPDF(detail.supporting_documents)"
                class="border border-gray-200 rounded-lg p-4 bg-white">
                <div class="flex flex-col items-center justify-center py-8">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-red-500" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                  </svg>
                  <p class="mt-2 text-sm font-medium text-gray-700">PDF Document</p>
                </div>
              </div>

              <!-- Download Button -->
              <a :href="detail.supporting_documents" target="_blank"
                class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                <svg xmlns="http://www.w3.org/2000/svg" class="-ml-1 mr-2 h-5 w-5 text-gray-500" fill="none"
                  viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                </svg>
                Download Document
              </a>
            </div>


            <div v-else class="text-center py-8 bg-white rounded-lg border border-gray-200">
              <svg xmlns="http://www.w3.org/2000/svg" class="mx-auto h-12 w-12 text-gray-400" fill="none"
                viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
              </svg>
              <h4 class="mt-2 text-sm font-medium text-gray-900">No documents attached</h4>
              <p class="mt-1 text-sm text-gray-500">This leave request has no supporting documents.</p>
            </div>
          </div>
        </div>

        <!-- Modal Footer -->
        <div class="bg-gray-50 px-6 py-3 border-t border-gray-200 flex justify-end">
          <button @click="showDetail = false"
            class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
            Close
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import axios from 'axios'

const search = ref('')
const statusFilter = ref('')
const typeFilter = ref('')
const showDetail = ref(false)
const detail = ref({})
const currentPage = ref(1)
const itemsPerPage = ref(5)
const showHidden = ref(false)
const showDeleteConfirm = ref(false)
const requestToDelete = ref(null)

const leaveRequests = ref([])

const formatDate = (dateStr) => {
  const options = { year: 'numeric', month: 'long', day: 'numeric' }
  return new Date(dateStr).toLocaleDateString('en-US', options)
}

const formatDuration = (range) => {
  const [from, to] = range.split(' to ')
  return `${formatDate(from)} to ${formatDate(to)}`
}

const isImage = (url) => {
  if (!url) return false
  const imageExtensions = ['.jpg', '.jpeg', '.png', '.gif', '.webp']
  return imageExtensions.some(ext => url.toLowerCase().endsWith(ext))
}

const isPDF = (url) => {
  if (!url) return false
  return url.toLowerCase().endsWith('.pdf')
}

const fetchLeaveRequests = async () => {
  try {
    const res = await axios.get('http://127.0.0.1:8000/api/educator/leave-requests', {
      headers: { Authorization: `Bearer ${localStorage.getItem('token')}` }
    })
    leaveRequests.value = res.data
      .filter(request => request.status === 'Approved' || request.status === 'Rejected')
      .map(request => ({
        ...request,
        is_pinned: localStorage.getItem(`pinned_${request.id}`) === 'true',
        is_hidden: localStorage.getItem(`hidden_${request.id}`) === 'true'
      }))
      .sort((a, b) => {
        // Pinned items first, then by submission date
        if (a.is_pinned && !b.is_pinned) return -1
        if (!a.is_pinned && b.is_pinned) return 1
        return new Date(b.submitted) - new Date(a.submitted)
      })
    currentPage.value = 1
  } catch (err) {
    console.error('Error loading requests:', err)
  }
}

const viewDetail = async (request) => {
  try {
    const res = await axios.get(`http://127.0.0.1:8000/api/educator/leave-request/${request.id}`, {
      headers: { Authorization: `Bearer ${localStorage.getItem('token')}` }
    })
    detail.value = res.data.leave_request
    showDetail.value = true
  } catch (err) {
    console.error('Error loading request details:', err)
  }
}

const togglePin = async (request) => {
  request.is_pinned = !request.is_pinned
  localStorage.setItem(`pinned_${request.id}`, request.is_pinned.toString())
  // Re-sort the list
  leaveRequests.value.sort((a, b) => {
    if (a.is_pinned && !b.is_pinned) return -1
    if (!a.is_pinned && b.is_pinned) return 1
    return new Date(b.submitted) - new Date(a.submitted)
  })
}

const toggleHide = async (request) => {
  request.is_hidden = !request.is_hidden
  localStorage.setItem(`hidden_${request.id}`, request.is_hidden.toString())
  if (showHidden.value && !request.is_hidden) {
    fetchLeaveRequests()
  }
}



const toggleHiddenVisibility = () => {
  showHidden.value = !showHidden.value
  currentPage.value = 1 // Reset to first page when changing view mode
}

const confirmDelete = (request) => {
  requestToDelete.value = request
  showDeleteConfirm.value = true
}

const deleteRequest = async () => {
  try {
    await axios.delete(`http://127.0.0.1:8000/api/educator/leave-request/${requestToDelete.value.id}`, {
      headers: { Authorization: `Bearer ${localStorage.getItem('token')}` }
    })
    // Remove from local storage
    localStorage.removeItem(`pinned_${requestToDelete.value.id}`)
    localStorage.removeItem(`hidden_${requestToDelete.value.id}`)
    // Refresh the list
    await fetchLeaveRequests()
    showDeleteConfirm.value = false
  } catch (err) {
    console.error('Error deleting request:', err)
  }
}

const saveStatusChanges = async (request) => {
  try {
    const payload = {
      status: request.status,
      rejection_reason: request.status === 'Rejected' ? request.rejection_reason : null
    }

    await axios.patch(`http://127.0.0.1:8000/api/educator/leave-request/${request.id}`, payload, {
      headers: { Authorization: `Bearer ${localStorage.getItem('token')}` }
    })

    // Refresh the list
    await fetchLeaveRequests()
    showDetail.value = false
  } catch (err) {
    console.error('Error updating request status:', err)
  }
}

const filteredRequests = computed(() => {
  return leaveRequests.value.filter((req) => {
    const matchesSearch = req.student.toLowerCase().includes(search.value.toLowerCase())
    const matchesStatus = statusFilter.value === '' || req.status === statusFilter.value
    const matchesType = typeFilter.value === '' || req.leave_type === typeFilter.value

    // Show only hidden items when showHidden is true, otherwise show only non-hidden
    if (showHidden.value) {
      return matchesSearch && matchesStatus && matchesType && req.is_hidden
    } else {
      return matchesSearch && matchesStatus && matchesType && !req.is_hidden
    }
  })
})

const totalPages = computed(() => {
  return Math.ceil(filteredRequests.value.length / itemsPerPage.value)
})

const paginatedRequests = computed(() => {
  const start = (currentPage.value - 1) * itemsPerPage.value
  const end = start + itemsPerPage.value
  return filteredRequests.value.slice(start, end)
})

const nextPage = () => {
  if (currentPage.value < totalPages.value) {
    currentPage.value++
  }
}

const prevPage = () => {
  if (currentPage.value > 1) {
    currentPage.value--
  }
}

const resetFilters = () => {
  search.value = ''
  statusFilter.value = ''
  typeFilter.value = ''
  currentPage.value = 1
}

onMounted(fetchLeaveRequests)
</script>

<style scoped>
button,
select,
input {
  transition: all 0.2s ease;
}

input:focus,
select:focus {
  outline: none;
  box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.2);
}

/* Style for hidden items */
.hidden-item {
  opacity: 0.6;
  background-color: rgba(243, 244, 246, 0.5);
}

/* Animation for pinning */
@keyframes pulse {
  0% {
    transform: scale(1);
  }

  50% {
    transform: scale(1.1);
  }

  100% {
    transform: scale(1);
  }
}

.pinned {
  animation: pulse 0.5s ease-in-out;
}
</style>