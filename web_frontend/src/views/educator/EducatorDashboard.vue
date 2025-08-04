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
                <svg class="absolute left-3 w-5 h-5 text-gray-400" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
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
                                <img :src="getProfileImageUrl(request)" :alt="`${request.student} profile`"
                                    class="w-10 h-10 rounded-full object-cover border border-gray-300"
                                    @error="handleImageError($event, request)" />
                                <div>
                                    <div class="font-medium text-gray-900">{{ request.student }}</div>
                                    <div class="text-xs text-gray-500">ID: {{ request.student_id }}</div>
                                </div>
                            </td>
                            <td class="py-4 px-6">{{ request.leave_type }}</td>
                            <td class="py-4 px-6">{{ formatDuration(request.duration) }}</td>
                            <td class="py-4 px-6">
                                <span class="px-3 py-1 rounded-full text-xs font-semibold" :class="{
                                    'bg-yellow-100 text-yellow-700': request.status === 'Pending',
                                    'bg-green-100 text-green-700': request.status === 'Approved',
                                    'bg-red-100 text-red-700': request.status === 'Rejected'
                                }">
                                    {{ request.status }}
                                </span>
                            </td>
                            <td class="py-4 px-6">{{ formatDate(request.submitted) }}</td>
                            <td class="py-4 px-6 space-x-3">
                                <button @click="viewDetail(request.id)"
                                    class="text-blue-600 hover:underline font-semibold">View</button>
                                <button v-if="request.status === 'Pending'" @click="approve(request.id)"
                                    class="text-green-600 hover:underline font-semibold">Approve</button>
                                <button v-if="request.status === 'Pending'" @click="reject(request.id)"
                                    class="text-red-600 hover:underline font-semibold">Reject</button>
                            </td>
                        </tr>
                    </tbody>
                </table>

                <!-- Pagination -->
                <div class="flex justify-center items-center space-x-4 py-4">
                    <button :disabled="currentPage === 1" @click="currentPage--"
                        class="px-3 py-1 rounded bg-gray-200 hover:bg-gray-300 ">
                        Prev
                    </button>
                    <div>Page {{ currentPage }} of {{ totalPages }}</div>
                    <button :disabled="currentPage === totalPages" @click="currentPage++"
                        class="px-3 py-1 rounded bg-gray-200 hover:bg-gray-300 disabled:opacity-50">
                        Next
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Detail Modal -->
    <div
      v-if="showDetail"
      class="fixed inset-0 z-50 bg-black bg-opacity-60 flex items-center justify-center p-4 backdrop-blur-sm"
    >
      <div class="bg-white rounded-2xl w-full max-w-5xl max-h-[90vh] overflow-hidden shadow-2xl">
        <!-- Modal Header -->
        <div class="bg-gradient-to-r from-pink-600 to-blue-500 px-3 py-2 text-white relative">
          <button
            @click="showDetail = false"
            class="absolute top-4 right-6 text-white hover:text-gray-200 transition-colors"
          >
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
          
          <div class="flex items-center space-x-4">
            <img
              v-if="getDetailImageUrl()"
              :src="getDetailImageUrl()"
              :alt="detail.student"
              class="w-16 h-16 rounded-full object-cover border-4 border-white shadow-lg"
              @error="handleDetailImageError"
            />
            <div class="w-16 h-16 rounded-full bg-white bg-opacity-20 flex items-center justify-center text-2xl font-bold" v-else>
              {{ detail.student ? detail.student.charAt(0).toUpperCase() : '?' }}
            </div>
            
            <div>
              <h2 class="text-2xl font-bold">{{ detail.student }}</h2>
              <p class="text-blue-100 text-lg">{{ detail.leave_type }} Request</p>
              <div class="flex items-center mt-2">
                <span
                  :class="{
                    'bg-yellow-500': detail.status === 'Pending',
                    'bg-green-500': detail.status === 'Approved',
                    'bg-red-500': detail.status === 'Rejected'
                  }"
                  class="px-3 py-1 rounded-full text-xs font-semibold text-white uppercase tracking-wide"
                >
                  {{ detail.status }}
                </span>
              </div>
            </div>
          </div>
        </div>

        <!-- Modal Content -->
        <div class="overflow-y-auto max-h-[calc(90vh-140px)]">
          <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 p-8">
            
            <!-- Left Column - Request Details -->
            <div class="space-y-6">
              <!-- Leave Duration -->
              <div class="bg-gray-50 rounded-xl p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                  <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                  </svg>
                  Leave Duration
                </h3>
                <div class="grid grid-cols-2 gap-4">
                  <div>
                    <p class="text-sm text-gray-500 font-medium">From Date</p>
                    <p class="text-gray-900 font-semibold">{{ formatDate(detail.from_date) }}</p>
                  </div>
                  <div>
                    <p class="text-sm text-gray-500 font-medium">To Date</p>
                    <p class="text-gray-900 font-semibold">{{ formatDate(detail.to_date) }}</p>
                  </div>
                </div>
              </div>

              <!-- Reason -->
              <div class="bg-gray-50 rounded-xl p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-3 flex items-center">
                  <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                  </svg>
                  Reason for Leave
                </h3>
                <p class="text-gray-700 leading-relaxed">{{ detail.reason || 'No reason provided' }}</p>
              </div>

              <!-- Contact Information -->
              <div class="bg-gray-50 rounded-xl p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-3 flex items-center">
                  <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                  </svg>
                  Contact Information
                </h3>
                <p class="text-gray-700">{{ detail.contact_info || 'No contact information provided' }}</p>
              </div>

              <!-- Status Information -->
              <div class="bg-gray-50 rounded-xl p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-3 flex items-center">
                  <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                  </svg>
                  Status Information
                </h3>
                <div class="space-y-2">
                  <p class="text-gray-700">
                    <span class="font-medium">Current Status:</span> 
                    <span
                      :class="{
                        'text-yellow-700': detail.status === 'Pending',
                        'text-green-700': detail.status === 'Approved',
                        'text-red-700': detail.status === 'Rejected'
                      }"
                      class="font-semibold capitalize"
                    >
                      {{ detail.status }}
                    </span>
                  </p>
                  <p v-if="detail.approved_by" class="text-gray-700">
                    <span class="font-medium">Approved by:</span> {{ detail.approved_by }}
                  </p>
                  <div v-if="detail.rejection_reason" class="mt-3 p-3 bg-red-50 border border-red-200 rounded-lg">
                    <p class="font-medium text-red-800 mb-1">Rejection Reason:</p>
                    <p class="text-red-700 text-sm">{{ detail.rejection_reason }}</p>
                  </div>
                </div>
              </div>
            </div>

            <!-- Right Column - Supporting Documents -->
            <div class="space-y-6">
              <div class="bg-gray-50 rounded-xl p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                  <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                  </svg>
                  Supporting Documents
                </h3>
                
                <div v-if="detail.supporting_documents" class="space-y-4">
                  <!-- Document Preview -->
                  <div class="border-2 border-dashed border-gray-300 rounded-lg p-4 bg-white">
                    <div v-if="isImageFile(detail.supporting_documents)" class="text-center">
                      <img 
                        :src="getDocumentUrl(detail.supporting_documents)" 
                        :alt="'Supporting document'"
                        class="max-w-full max-h-96 mx-auto rounded-lg shadow-md object-contain"
                        @error="handleDocumentError"
                      />
                    </div>
                    
                    <div v-else-if="isPdfFile(detail.supporting_documents)" class="text-center">
                      <iframe 
                        :src="getDocumentUrl(detail.supporting_documents)"
                        class="w-full h-96 rounded-lg border"
                        frameborder="0"
                        @error="handleDocumentError"
                      ></iframe>
                    </div>
                    
                    <div v-else class="text-center py-8">
                      <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                      </svg>
                      <p class="text-gray-600 mb-2">Document Preview Not Available</p>
                      <p class="text-sm text-gray-500">{{ getFileName(detail.supporting_documents) }}</p>
                    </div>
                  </div>
                  
                  <!-- Document Actions -->
                  <div class="flex flex-wrap gap-2">
                    <a
                      :href="getDocumentUrl(detail.supporting_documents)"
                      :download="getFileName(detail.supporting_documents)"
                      class="inline-flex items-center px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors text-sm font-medium"
                    >
                      <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                      </svg>
                      Download
                    </a>
                  </div>
                </div>
                
                <div v-else class="text-center py-12">
                  <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                  </svg>
                  <p class="text-gray-500 text-lg">No Supporting Documents</p>
                  <p class="text-gray-400 text-sm">No documents were submitted with this request</p>
                </div>
              </div>
            </div>
          </div>

          <!-- Action Buttons for Detail Modal -->
          <div v-if="detail.status === 'Pending'" class="border-t border-gray-200 px-8 py-6 bg-gray-50">
            <div class="flex justify-end space-x-4">
              <button
                @click="rejectFromDetail(detail.id)"
                class="px-6 py-3 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors font-medium flex items-center"
              >
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
                Reject Request
              </button>
              <button
                @click="approve(detail.id)"
                class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium flex items-center"
              >
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                Approve Request
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Rejection Reason Modal -->
    <div
      v-if="showRejectForm" 
      class="fixed inset-0 z-[60] bg-black bg-opacity-70 flex items-center justify-center p-4 backdrop-blur-sm"
    >
      <div class="bg-white rounded-xl w-full max-w-md shadow-2xl overflow-hidden transform transition-all duration-300 scale-100">
        <div class="bg-red-600 px-6 py-4 text-white">
          <div class="flex items-center justify-between">
            <div>
              <h3 class="text-lg font-semibold">Reject Leave Request</h3>
              <p class="text-sm text-red-100">Please provide a reason for rejection</p>
            </div>
            <button 
              @click="cancelRejection"
              class="text-red-100 hover:text-white transition-colors"
            >
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
              </svg>
            </button>
          </div>
        </div>
        
        <form @submit.prevent="submitRejection" class="p-6">
          <div class="mb-6">
            <label for="rejection-reason" class="block text-sm font-medium text-gray-700 mb-2">
              Reason for Rejection *
            </label>
            <textarea 
              id="rejection-reason" 
              v-model="rejectionReason" 
              rows="4" 
              class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 resize-none"
              placeholder="Please explain why this leave request is being rejected..."
              required
              :disabled="isSubmitting"
            ></textarea>
            <p v-if="rejectionError" class="mt-2 text-sm text-red-600 flex items-center">
              <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
              {{ rejectionError }}
            </p>
          </div>
          
          <div class="flex justify-end space-x-3">
            <button 
              type="button" 
              @click="cancelRejection" 
              class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300 transition-colors font-medium"
              :disabled="isSubmitting"
            >
              Cancel
            </button>
            <button 
              type="submit" 
              class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors flex items-center font-medium"
              :disabled="isSubmitting || !rejectionReason.trim()"
            >
              <svg v-if="isSubmitting" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              <svg v-else class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
              </svg>
              {{ isSubmitting ? 'Rejecting...' : 'Confirm Rejection' }}
            </button>
          </div>
        </form>
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
const user = ref(null)
const isLoading = ref(false)
const apiError = ref(null)
const detailImageError = ref(false)
// vue:src\views\educator\EducatorDashboard.vue
// ... existing code ...

const showRejectForm = ref(false)
const rejectionReason = ref('')
const rejectionError = ref('')
const pendingRejectionId = ref(null)
const isSubmitting = ref(false)

const getDocumentUrl = (path) => {
  if (!path) return null;
  return `http://127.0.0.1:8000/storage/${path}`;
};

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
            headers: { Authorization: `Bearer ${localStorage.getItem('authToken')}` }
        })

        console.log('=== DEBUGGING LEAVE REQUESTS RESPONSE ===')
        console.log('Full response:', res.data)
        
        if (res.data && res.data.length > 0) {
            console.log('First leave request:', res.data[0])
            console.log('Available fields in first request:', Object.keys(res.data[0]))
            
            // Check for different possible image field names
            const firstRequest = res.data[0]
            console.log('Checking image fields:')
            console.log('  - profile_image:', firstRequest.profile_image)
            console.log('  - img:', firstRequest.img) 
            console.log('  - img_url:', firstRequest.img_url)
            console.log('  - student_img:', firstRequest.student_img)
            console.log('  - student.img:', firstRequest.student?.img)
            console.log('  - student.profile_image:', firstRequest.student?.profile_image)
            console.log('  - student object:', firstRequest.student)
        }
        
        leaveRequests.value = res.data

    } catch (err) {
        console.error('Error loading requests:', err)
    }
}

const viewDetail = async (id) => {
    try {
        const res = await axios.get(`http://127.0.0.1:8000/api/educator/leave-request/${id}`, {
            headers: {
                Authorization: `Bearer ${localStorage.getItem('authToken')}`,
            },
        });

        const request = res.data.leave_request || {};

        detail.value = {
            ...request,
            profile_image: request.profile_image || request.student_img || request.img || null,
            document_url: request.document_url ?? null,
        };

        console.log('Detail loaded:', detail.value);

        detailImageError.value = false; // Reset image error
        showDetail.value = true;
    } catch (err) {
        console.error('Error loading request detail:', err);
    }
};

const approve = async (id) => {
    try {
        await axios.post(`http://127.0.0.1:8000/api/educator/leave-request/${id}/approve`, {}, {
            headers: { Authorization: `Bearer ${localStorage.getItem('authToken')}` }
        })
        
        // Show success notification
        const successNotification = document.createElement('div')
        successNotification.className = 'fixed top-4 right-4 bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded shadow-md z-50'
        successNotification.innerHTML = `
            <div class="flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                <p>Leave request approved successfully</p>
            </div>
        `
        document.body.appendChild(successNotification)
        
        // Remove notification after 3 seconds
        setTimeout(() => {
            document.body.removeChild(successNotification)
        }, 3000)
        
        showDetail.value = false
        fetchLeaveRequests()
    } catch (error) {
        console.error('Error approving leave request:', error)
        alert('Failed to approve leave request')
    }
}

const reject = (id) => {
    console.log('Reject button clicked for ID:', id)
    pendingRejectionId.value = id
    rejectionReason.value = ''
    rejectionError.value = ''
    showRejectForm.value = true
    console.log('showRejectForm set to:', showRejectForm.value)
}

const rejectFromDetail = (id) => {
    console.log('Reject from detail clicked for ID:', id)
    reject(id)
}

const cancelRejection = () => {
    console.log('Cancel rejection clicked')
    showRejectForm.value = false
    pendingRejectionId.value = null
    rejectionReason.value = ''
    rejectionError.value = ''
}

const submitRejection = async () => {
    if (!rejectionReason.value.trim()) {
        rejectionError.value = 'Rejection reason is required'
        return
    }
    
    console.log('Submitting rejection for ID:', pendingRejectionId.value)
    console.log('Rejection reason:', rejectionReason.value)
    
    isSubmitting.value = true
    rejectionError.value = ''
    
    try {
        await axios.post(`http://127.0.0.1:8000/api/educator/leave-request/${pendingRejectionId.value}/reject`, {
            rejection_reason: rejectionReason.value.trim()
        }, {
            headers: { Authorization: `Bearer ${localStorage.getItem('authToken')}` }
        })
        
        console.log('Rejection submitted successfully')
        
        showRejectForm.value = false
        showDetail.value = false
        pendingRejectionId.value = null
        rejectionReason.value = ''
        
        // Show success notification
        const successNotification = document.createElement('div')
        successNotification.className = 'fixed top-4 right-4 bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded shadow-md z-50'
        successNotification.innerHTML = `
            <div class="flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <p>Leave request rejected successfully</p>
            </div>
        `
        document.body.appendChild(successNotification)
        
        // Remove notification after 3 seconds
        setTimeout(() => {
            document.body.removeChild(successNotification)
        }, 3000)
        
        // Refresh the leave requests
        fetchLeaveRequests()
    } catch (error) {
        console.error('Error rejecting leave request:', error)
        rejectionError.value = error.response?.data?.message || 'Failed to reject leave request'
    } finally {
        isSubmitting.value = false
    }
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

// Updated function for detail modal image
const getDetailImageUrl = () => {
    if (!detail.value || !detail.value.profile_image || detailImageError.value) {
        console.log('No detail student profile image data, using default avatar')
        const initial = detail.value?.student ? detail.value.student.charAt(0).toUpperCase() : '?'
        return `data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNDAiIGhlaWdodD0iNDAiIHZpZXdCb3g9IjAgMCA0MCA0MCIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPGNpcmNsZSBjeD0iMjAiIGN5PSIyMCIgcj0iMjAiIGZpbGw9IiM2QjcyODAiLz4KPHRleHQgeD0iMjAiIHk9IjI2IiBmb250LWZhbWlseT0iQXJpYWwiIGZvbnQtc2l6ZT0iMTQiIGZpbGw9IndoaXRlIiB0ZXh0LWFuY2hvcj0ibWlkZGxlIj4ke2luaXRpYWx9PC90ZXh0Pjwvc3ZnPg==`
    }

    console.log('Using detail student profile image:', detail.value.profile_image)

    // The backend already returns a full URL in profile_image field, so we can use it directly
    return detail.value.profile_image
}

// Handle image errors for detail modal
const handleDetailImageError = () => {
    console.log('Detail image failed to load, using default avatar')
    detailImageError.value = true
}

const hasToken = computed(() => !!localStorage.getItem('authToken'))

const displayName = computed(() => {
    if (isLoading.value) return 'Loading...'
    if (apiError.value) return 'Error'
    if (!user.value) return 'Guest'

    return user.value.name ||
        user.value.full_name ||
        user.value.first_name ||
        (user.value.email ? user.value.email.split('@')[0] : null) ||
        'User'
})

const getUserInitials = () => {
    if (!user.value) return 'G'

    const name = user.value.name ||
        user.value.full_name ||
        user.value.first_name ||
        user.value.email ||
        'Guest'

    if (name.includes('@')) {
        return name.charAt(0).toUpperCase()
    }

    const words = name.split(' ')
    if (words.length >= 2) {
        return (words[0].charAt(0) + words[1].charAt(0)).toUpperCase()
    }

    return name.charAt(0).toUpperCase()
}

// Function to get profile image URL from leave request data
const getProfileImageUrl = (request) => {
    console.log(`=== DEBUG: Getting image for student ${request.student_id} ===`)
    console.log('Request object:', request)

    // Try multiple possible field names for the image
    let imageField   
    
    // Check different possible locations for the image data
    if (request.profile_image) {
        imageField = request.profile_image
        console.log('📷 Found image in request.profile_image:', imageField)
    } else if (request.img_url) {
        imageField = request.img_url
        console.log('📷 Found image in request.img_url:', imageField)
    } else if (request.img) {
        imageField = request.img
        console.log('📷 Found image in request.img:', imageField)
    } else if (request.student_img) {
        imageField = request.student_img
        console.log('📷 Found image in request.student_img:', imageField)
    } else if (request.student?.img) {
        imageField = request.student.img
        console.log('📷 Found image in request.student.img:', imageField)
    } else if (request.student?.profile_image) {
        imageField = request.student.profile_image
        console.log('📷 Found image in request.student.profile_image:', imageField)
    } else if (request.student?.img_url) {
        imageField = request.student.img_url
        console.log('📷 Found image in request.student.img_url:', imageField)
    }

    console.log('Available request fields:', Object.keys(request))
    console.log('Student object:', request.student)

    // If no image field found, return placeholder with student initial
    if (!imageField) {
        const initial = request.student ? request.student.charAt(0).toUpperCase() : '?'
        console.log(`❌ No img field found for student ${request.student_id}, returning placeholder: ${initial}`)
        return `data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNDAiIGhlaWdodD0iNDAiIHZpZXdCb3g9IjAgMCA0MCA0MCIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPGNpcmNsZSBjeD0iMjAiIGN5PSIyMCIgcj0iMjAiIGZpbGw9IiM2QjcyODAiLz4KPHRleHQgeD0iMjAiIHk9IjI2IiBmb250LWZhbWlseT0iQXJpYWwiIGZvbnQtc2l6ZT0iMTQiIGZpbGw9IndoaXRlIiB0ZXh0LWFuY2hvcj0ibWlkZGxlIj4ke2luaXRpYWx9PC90ZXh0Pjwvc3ZnPg==`
    }

    // Check if it's already a full URL or needs to be constructed
    let finalUrl = imageField
    if (imageField && !imageField.startsWith('http://') && !imageField.startsWith('https://')) {
        if (imageField.startsWith('/')) {
            finalUrl = `http://127.0.0.1:8000${imageField}`
        } else {
            finalUrl = `http://127.0.0.1:8000/storage/${imageField}`
        }
    }

    console.log(`✅ Final image URL for student ${request.student_id}:`, finalUrl)
    return finalUrl
}

const handleImageError = (event, request) => {
    console.log(`Profile image failed to load for student ${request.student}, falling back to initials`)
    const initial = request.student ? request.student.charAt(0).toUpperCase() : '?'
    event.target.src = `data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNDAiIGhlaWdodD0iNDAiIHZpZXdCb3g9IjAgMCA0MCA0MCIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPGNpcmNsZSBjeD0iMjAiIGN5PSIyMCIgcj0iMjAiIGZpbGw9IiM2QjcyODAiLz4KPHRleHQgeD0iMjAiIHk9IjI2IiBmb250LWZhbWlseT0iQXJpYWwiIGZvbnQtc2l6ZT0iMTQiIGZpbGw9IndoaXRlIiB0ZXh0LWFuY2hvcj0ibWlkZGxlIj4ke2luaXRpYWx9PC90ZXh0Pjwvc3ZnPg==`
}

const fetchUser = async () => {
    const token = localStorage.getItem('authToken')

    if (!token) {
        console.log('No auth token found')
        return
    }

    isLoading.value = true
    apiError.value = null

    try {
        const response = await axios.get('http://127.0.0.1:8000/api/user', {
            headers: {
                'Authorization': `Bearer ${token}`,
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        })

        user.value = response.data
        localStorage.setItem('user_data', JSON.stringify(response.data))

        console.log('User loaded successfully:', user.value)
        console.log('Image from img column:', user.value.img)

    } catch (error) {
        console.error('Error fetching user:', error)
        apiError.value = error.response?.data?.message || error.message

        if (error.response?.status === 401) {
            localStorage.removeItem('authToken')
            localStorage.removeItem('user_data')
            console.log('User unauthorized, token removed')
        }
    } finally {
        isLoading.value = false
    }
}

const isImageFile = (filename) => {
  if (!filename) return false
  const imageExtensions = ['.jpg', '.jpeg', '.png', '.gif', '.bmp', '.webp', '.svg']
  return imageExtensions.some(ext => filename.toLowerCase().includes(ext))
}

const isPdfFile = (filename) => {
  if (!filename) return false
  return filename.toLowerCase().includes('.pdf')
}

const getFileName = (path) => {
  if (!path) return 'Unknown File'
  return path.split('/').pop() || path
}

const handleDocumentError = (event) => {
  console.log('Document failed to load:', event)
  // You could add additional error handling here
}

onMounted(fetchLeaveRequests)
</script>

<style scoped>
/* Tailwind CSS is used */
</style>>