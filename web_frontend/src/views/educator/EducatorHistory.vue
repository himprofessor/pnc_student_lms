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
                <span v-else-if="request.leave_type === 'Vacation Leave'" class="text-lg">🏖️</span>
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
              Approved
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

    <!-- SINGLE Detail Modal (removed duplicate) -->
    <div v-if="showDetail"
      class="fixed inset-0 z-50 bg-black bg-opacity-60 flex items-center justify-center p-4 backdrop-blur-sm">
      <div class="bg-white rounded-2xl w-full max-w-5xl max-h-[90vh] overflow-hidden shadow-2xl">
        <!-- Modal Header -->
        <div class="bg-gradient-to-r from-pink-600 to-blue-500 px-6 py-4 text-white relative">
          <button @click="closeDetailModal"
            class="absolute top-4 right-6 text-white hover:text-gray-200 transition-colors">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>

          <div class="flex items-center space-x-4">
            <img v-if="getDetailImageUrl() && !detailImageError" :src="getDetailImageUrl()" :alt="detail.student"
              class="w-16 h-16 rounded-full object-cover border-4 border-white shadow-lg"
              @error="handleDetailImageError" />
            <div
              class="w-16 h-16 rounded-full bg-white bg-opacity-20 flex items-center justify-center text-2xl font-bold"
              v-else>
              {{ detail.student ? detail.student.charAt(0).toUpperCase() : "?" }}
            </div>

            <div>
              <h2 class="text-2xl font-bold">{{ detail.student || 'Unknown Student' }}</h2>
              <p class="text-blue-100 text-lg">{{ detail.leave_type || 'Leave Request' }}</p>
              <div class="flex items-center mt-2">
                <span :class="{
                  'bg-yellow-500': detail.status === 'Pending',
                  'bg-green-500': detail.status === 'Approved',
                  'bg-red-500': detail.status === 'Rejected',
                }" class="px-3 py-1 rounded-full text-xs font-semibold text-white uppercase tracking-wide">
                  {{ detail.status || 'Unknown' }}
                </span>
              </div>
            </div>
          </div>
        </div>

        <!-- Modal Content -->
        <div class="overflow-y-auto max-h-[calc(90vh-140px)]">
          <div v-if="isLoadingDetail" class="flex items-center justify-center py-12">
            <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600"></div>
            <span class="ml-3 text-gray-600">Loading details...</span>
          </div>

          <div v-else class="grid grid-cols-1 lg:grid-cols-2 gap-8 p-8">
            <!-- Left Column - Request Details -->
            <div class="space-y-6">
              <!-- Leave Duration -->
              <div class="bg-gray-50 rounded-xl p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                  <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                  </svg>
                  Leave Duration
                </h3>
                <div class="grid grid-cols-2 gap-4">
                  <div>
                    <p class="text-sm text-gray-500 font-medium">From Date</p>
                    <p class="text-gray-900 font-semibold">
                      {{ detail.from_date ? formatDate(detail.from_date) : 'Not specified' }}
                    </p>
                  </div>
                  <div>
                    <p class="text-sm text-gray-500 font-medium">To Date</p>
                    <p class="text-gray-900 font-semibold">
                      {{ detail.to_date ? formatDate(detail.to_date) : 'Not specified' }}
                    </p>
                  </div>
                </div>
              </div>

              <!-- Reason -->
              <div class="bg-gray-50 rounded-xl p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-3 flex items-center">
                  <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                  </svg>
                  Reason for Leave
                </h3>
                <p class="text-gray-700 leading-relaxed">
                  {{ detail.reason || "No reason provided" }}
                </p>
              </div>

              <!-- Contact Information -->
              <div class="bg-gray-50 rounded-xl p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-3 flex items-center">
                  <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                  </svg>
                  Contact Information
                </h3>
                <p class="text-gray-700">
                  {{ detail.contact_info || "No contact information provided" }}
                </p>
              </div>

              <!-- Status Information -->
              <div class="bg-gray-50 rounded-xl p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-3 flex items-center">
                  <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                  </svg>
                  Status Information
                </h3>
                <div class="space-y-2">
                  <p class="text-gray-700">
                    <span class="font-medium">Current Status:</span>
                    <span :class="{
                      'text-yellow-700': detail.status === 'Pending',
                      'text-green-700': detail.status === 'Approved',
                      'text-red-700': detail.status === 'Rejected',
                    }" class="font-semibold capitalize ml-2">
                      {{ detail.status || 'Unknown' }}
                    </span>
                  </p>

                  <!-- Display Rejection Reason -->
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
                  <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">```vue:web_frontend\src\views\educator\EducatorHistory.vue
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                  </svg>
                  Supporting Documents
                </h3>

                <div v-if="detail.supporting_documents && detail.supporting_documents.trim()" class="space-y-4">
                  <!-- Document Preview -->
                  <div class="border-2 border-dashed border-gray-300 rounded-lg p-4 bg-white">
                    <!-- Loading State -->
                    <div v-if="documentLoading" class="text-center py-8">
                      <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600 mx-auto mb-2"></div>
                      <p class="text-gray-600 text-sm">Loading document...</p>
                    </div>

                    <!-- Image Preview -->
                    <div v-else-if="isImageFile(detail.supporting_documents)" class="text-center">
                      <img :src="getDocumentUrl(detail.supporting_documents)"
                        :alt="getFileName(detail.supporting_documents)"
                        class="max-w-full max-h-96 mx-auto rounded-lg shadow-md object-contain"
                        @load="documentLoading = false" @error="handleDocumentError" />
                    </div>

                    <!-- PDF Preview -->
                    <div v-else-if="isPdfFile(detail.supporting_documents)" class="text-center">
                      <iframe :src="getDocumentUrl(detail.supporting_documents)" class="w-full h-96 rounded-lg border"
                        frameborder="0" @load="documentLoading = false" @error="handleDocumentError">
                      </iframe>
                    </div>

                    <!-- Other File Types -->
                    <div v-else class="text-center py-8">
                      <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                      </svg>
                      <p class="text-gray-600 mb-2">Document Preview Not Available</p>
                      <p class="text-sm text-gray-500">{{ getFileName(detail.supporting_documents) }}</p>
                    </div>

                    <!-- Document Error State -->
                    <div v-if="documentError" class="text-center py-8">
                      <svg class="w-16 h-16 mx-auto text-red-400 mb-4" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
                      </svg>
                      <p class="text-red-600 mb-2">Failed to Load Document</p>
                      <p class="text-sm text-gray-500">{{ getFileName(detail.supporting_documents) }}</p>
                    </div>
                  </div>

                  <!-- Document Actions -->
                  <div class="flex flex-wrap gap-2">
                    <button @click="openDocumentInNewTab"
                      class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors text-sm font-medium">
                      <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                      </svg>
                      Open New Tab
                    </button>
                  </div>
                </div>

                <!-- No Documents State -->
                <div v-else class="text-center py-12">
                  <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                  </svg>
                  <p class="text-gray-500 text-lg">No Supporting Documents</p>
                  <p class="text-gray-400 text-sm">No documents were submitted with this request</p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Reject Dialog Modal -->
    <div v-if="showRejectDialog" class="fixed inset-0 z-60 bg-black bg-opacity-60 flex items-center justify-center p-4">
      <div class="bg-white rounded-lg w-full max-w-md">
        <div class="p-6">
          <h3 class="text-lg font-semibold text-gray-900 mb-4">Reject Leave Request</h3>
          <p class="text-gray-600 mb-4">Please provide a reason for rejecting this request:</p>
          <textarea v-model="rejectionReason"
            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-red-500 focus:border-red-500"
            rows="4" placeholder="Enter rejection reason..."></textarea>
          <div class="flex justify-end gap-3 mt-6">
            <button @click="closeRejectDialog" class="px-4 py-2 text-gray-600 hover:text-gray-800 transition-colors">
              Cancel
            </button>
            <button @click="confirmReject"
              class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
              Reject Request
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import axios from 'axios'

// Reactive variables
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
const detailImageError = ref(false)
const isLoadingDetail = ref(false)
const documentLoading = ref(false)
const documentError = ref(false)
const showRejectDialog = ref(false)
const rejectionReason = ref('')
const requestToReject = ref(null)

const leaveRequests = ref([])

// Utility functions
const formatDate = (dateStr) => {
  if (!dateStr) return 'Not specified'
  try {
    const options = { year: 'numeric', month: 'long', day: 'numeric' }
    return new Date(dateStr).toLocaleDateString('en-US', options)
  } catch (error) {
    console.error('Date formatting error:', error)
    return 'Invalid date'
  }
}

const formatDuration = (range) => {
  if (!range) return 'Not specified'
  try {
    const [from, to] = range.split(' to ')
    return `${formatDate(from)} to ${formatDate(to)}`
  } catch (error) {
    console.error('Duration formatting error:', error)
    return range
  }
}

// Enhanced document handling functions
const getDocumentUrl = (path) => {
  if (!path || typeof path !== 'string') return null

  // Handle different path formats
  if (path.startsWith('http://') || path.startsWith('https://')) {
    return path
  }

  // Remove leading slash if present
  const cleanPath = path.startsWith('/') ? path.substring(1) : path

  // Construct full URL
  return `http://127.0.0.1:8000/storage/${cleanPath}`
}

const isImageFile = (filename) => {
  if (!filename || typeof filename !== 'string') return false
  const imageExtensions = ['.jpg', '.jpeg', '.png', '.gif', '.bmp', '.webp', '.svg']
  const lowerFilename = filename.toLowerCase()
  return imageExtensions.some(ext => lowerFilename.endsWith(ext))
}

const isPdfFile = (filename) => {
  if (!filename || typeof filename !== 'string') return false
  return filename.toLowerCase().endsWith('.pdf')
}

const getFileName = (path) => {
  if (!path || typeof path !== 'string') return 'Unknown File'
  return path.split('/').pop() || path
}

const handleDocumentError = () => {
  console.error('Document failed to load')
  documentError.value = true
  documentLoading.value = false
}

const openDocumentInNewTab = () => {
  if (detail.value.supporting_documents) {
    const url = getDocumentUrl(detail.value.supporting_documents)
    if (url) {
      window.open(url, '_blank')
    }
  }
}

// Profile image handling
const getDetailImageUrl = () => {
  if (!detail.value || detailImageError.value) return null

  const imageFields = ['profile_image', 'student_img', 'img', 'avatar']

  for (const field of imageFields) {
    if (detail.value[field]) {
      const imagePath = detail.value[field]
      if (imagePath.startsWith('http://') || imagePath.startsWith('https://')) {
        return imagePath
      }
      return `http://127.0.0.1:8000/storage/${imagePath}`
    }
  }

  return null
}

const handleDetailImageError = () => {
  console.log("Detail image failed to load, using default avatar")
  detailImageError.value = true
}

// API functions
const fetchLeaveRequests = async () => {
  try {
    const res = await axios.get('http://127.0.0.1:8000/api/educator/leave-requests', {
      headers: { Authorization: `Bearer ${localStorage.getItem('authToken')}` }
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
    isLoadingDetail.value = true
    documentLoading.value = true
    documentError.value = false
    detailImageError.value = false

    const res = await axios.get(`http://127.0.0.1:8000/api/educator/leave-request/${request.id}`, {
      headers: { Authorization: `Bearer ${localStorage.getItem('authToken')}` }
    })

    // Handle different API response structures
    const responseData = res.data
    let leaveRequest = {}

    if (responseData.leave_request) {
      leaveRequest = responseData.leave_request
    } else if (responseData.data) {
      leaveRequest = responseData.data
    } else {
      leaveRequest = responseData
    }

    detail.value = {
      id: leaveRequest.id || request.id,
      student: leaveRequest.student || leaveRequest.student_name || 'Unknown Student',
      leave_type: leaveRequest.leave_type || 'Leave Request',
      status: leaveRequest.status || 'Unknown',
      from_date: leaveRequest.from_date || leaveRequest.start_date,
      to_date: leaveRequest.to_date || leaveRequest.end_date,
      reason: leaveRequest.reason || leaveRequest.description,
      contact_info: leaveRequest.contact_info || leaveRequest.contact,
      approved_by: leaveRequest.approved_by,
      rejection_reason: leaveRequest.rejection_reason,
      supporting_documents: leaveRequest.supporting_documents || leaveRequest.documents || leaveRequest.attachment,
      profile_image: leaveRequest.profile_image || leaveRequest.student_img || leaveRequest.img || leaveRequest.avatar,
      submitted: leaveRequest.submitted || leaveRequest.created_at,
      duration: leaveRequest.duration
    }

    console.log('Detail loaded:', detail.value)
    showDetail.value = true

  } catch (err) {
    console.error('Error loading request detail:', err)
  } finally {
    isLoadingDetail.value = false
    documentLoading.value = false
  }
}

// Modal functions
const closeDetailModal = () => {
  showDetail.value = false
  detail.value = {}
  detailImageError.value = false
  documentError.value = false
  documentLoading.value = false
}

const openRejectDialog = (requestId) => {
  requestToReject.value = requestId
  rejectionReason.value = ''
  showRejectDialog.value = true
}

const closeRejectDialog = () => {
  showRejectDialog.value = false
  requestToReject.value = null
  rejectionReason.value = ''
}

const confirmReject = async () => {
  if (!rejectionReason.value.trim()) {
    alert('Please provide a reason for rejection')
    return
  }

  try {
    await axios.patch(`http://127.0.0.1:8000/api/educator/leave-request/${requestToReject.value}`, {
      status: 'Rejected',
      rejection_reason: rejectionReason.value.trim()
    }, {
      headers: { Authorization: `Bearer ${localStorage.getItem('authToken')}` }
    })

    // Update the detail if it's the same request
    if (detail.value.id === requestToReject.value) {
      detail.value.status = 'Rejected'
      detail.value.rejection_reason = rejectionReason.value.trim()
    }

    // Refresh the list
    await fetchLeaveRequests()
    closeRejectDialog()

  } catch (err) {
    console.error('Error rejecting request:', err)
    alert('Failed to reject request. Please try again.')
  }
}

// List management functions
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
  currentPage.value = 1
}

const confirmDelete = (request) => {
  requestToDelete.value = request
  showDeleteConfirm.value = true
}

const deleteRequest = async () => {
  try {
    await axios.delete(`http://127.0.0.1:8000/api/educator/leave-request/${requestToDelete.value.id}`, {
      headers: { Authorization: `Bearer ${localStorage.getItem('authToken')}` }
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

// Computed properties
const filteredRequests = computed(() => {
  return leaveRequests.value.filter((req) => {
    const matchesSearch = req.student?.toLowerCase().includes(search.value.toLowerCase()) ?? false
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

// Pagination functions
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

// Initialize
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

/* Loading animation */
@keyframes spin {
  0% {
    transform: rotate(0deg);
  }

  100% {
    transform: rotate(360deg);
  }
}

.animate-spin {
  animation: spin 1s linear infinite;
}
</style>