<template>
  <div class="min-h-screen bg-gray-50 text-gray-800">
    <div class="px-6 py-6">
      <!-- Success Alert -->
      <div v-if="successMessage"
        class="fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50 flex items-center">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        {{ successMessage }}
      </div>

      <!-- Header -->
      <div class="flex justify-between items-center mb-6 px-[30px]">
        <div>
          <h1 class="text-2xl font-bold text-blue-600">Welcome back {{ displayName }}</h1>
          <p class="text-sm text-gray-500">
            Manage your leave requests and track their status
          </p>
        </div>
        <div class="flex items-center space-x-4">
          <!-- 📅 Calendar Button -->
          <router-link to="/calendar"
            class="relative p-2 text-gray-600 hover:text-gray-800 hover:bg-gray-100 rounded-full transition-colors">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
            </svg>
          </router-link>
          <!-- Calendar Component -->
          <Calendar v-if="showCalendar" />

          <!-- Notification Icon -->
          <div class="relative">
            <button @click="toggleNotificationSummary"
              class="relative p-2 text-gray-600 hover:text-gray-800 hover:bg-gray-100 rounded-full transition-colors">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M15 17h5l-5-5V9.09c0-2.36-1.64-4.36-4-4.36S7 6.73 7 9.09V12l-5 5h5m8 0v1a3 3 0 11-6 0v-1m6 0H9" />
              </svg>

              <!-- Notification Badge -->
              <span v-if="unreadNotificationCount > 0"
                class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center font-medium">
                {{ unreadNotificationCount > 9 ? "9+" : unreadNotificationCount }}
              </span>
            </button>

            <!-- Notification Summary Card -->
            <div v-if="showNotificationSummary"
              class="absolute right-0 top-12 w-96 bg-white rounded-lg shadow-lg border z-50">
              <div class="p-4 border-b">
                <div class="flex justify-between items-center">
                  <h3 class="text-lg font-semibold text-gray-800">
                    Notification Summary
                  </h3>
                  <button @click="showNotificationSummary = false" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                  </button>
                </div>
              </div>

              <div class="p-4">
                <div class="p-6">
                  <!-- Recent Notifications -->
                  <div class="space-y-3">
                    <h4 class="text-sm font-medium text-gray-700 mb-2">
                      Recent Updates
                    </h4>

                    <div v-for="notification in recentNotifications" :key="notification.id"
                      class="flex items-start space-x-3 p-2 rounded-lg cursor-pointer hover:bg-gray-50 transition-colors"
                      :class="{ 'bg-blue-50': !notification.read }" @click="handleNotificationClick(notification)">
                      <svg v-if="notification.type === 'leave_approved'"
                        class="w-4 h-4 text-green-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                      </svg>
                      <svg v-else-if="notification.type === 'leave_rejected'"
                        class="w-4 h-4 text-red-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M6 18L18 6M6 6l12 12" />
                      </svg>

                      <div class="flex-1 min-w-0">
                        <p class="text-sm text-gray-800 truncate font-semibold">
                          {{ notification.message }}
                        </p>
                        <p class="text-sm text-gray-500 font-medium">
                          {{ formatDate(notification.created_at) }}
                        </p>
                      </div>

                      <span v-if="!notification.read"
                        class="w-2 h-2 bg-blue-500 rounded-full flex-shrink-0 mt-1"></span>
                    </div>

                    <div v-if="recentNotifications.length === 0" class="text-sm text-gray-500 text-center py-2">
                      No recent notifications
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Notification Detail Modal -->
          <div v-if="showNotificationDetail"
            class="fixed inset-0 z-50 bg-black bg-opacity-60 flex items-center justify-center p-4 backdrop-blur-sm">
            <div class="bg-white rounded-2xl w-full max-w-md shadow-2xl overflow-hidden">
              <!-- Modal Header -->
              <div class="bg-gradient-to-r from-blue-600 to-purple-600 px-6 py-4 text-white">
                <div class="flex justify-between items-center">
                  <h3 class="text-lg font-semibold">Notification Details</h3>
                  <button @click="closeNotificationDetail" class="text-white hover:text-gray-200 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                  </button>
                </div>
              </div>

              <!-- Modal Content -->
              <div class="p-6" v-if="selectedNotification">
                <div class="flex items-start space-x-3 mb-4">
                  <svg v-if="selectedNotification.type === 'leave_approved'"
                    class="w-8 h-8 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                  </svg>
                  <svg v-else-if="selectedNotification.type === 'leave_rejected'"
                    class="w-8 h-8 text-red-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                  </svg>
                  <svg v-else-if="selectedNotification.type === 'leave_pending'"
                    class="w-8 h-8 text-yellow-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                  </svg>

                  <div class="flex-1">
                    <div class="text-lg font-semibold text-gray-800 mb-2">
                      {{
                        selectedNotification.type === "leave_approved"
                          ? "Leave Request Approved"
                          : selectedNotification.type === "leave_rejected"
                            ? "Leave Request Rejected"
                            : "Leave Request Pending"
                      }}
                    </div>
                    <div class="text-sm text-gray-600 mb-3">
                      {{ formatDate(selectedNotification.created_at) }}
                    </div>
                  </div>
                </div>

                <div class="bg-gray-50 rounded-lg p-4 mb-4">
                  <h4 class="font-semibold text-gray-800 mb-2">Message:</h4>
                  <p class="text-gray-700 leading-relaxed">
                    {{ selectedNotification.message }}
                  </p>
                </div>

                <div v-if="selectedNotification.data || parseNotificationDetails(selectedNotification.message)"
                  class="bg-gray-50 rounded-lg p-4 mb-4">
                  <h4 class="font-semibold text-gray-800 mb-2">
                    Additional Details:
                  </h4>
                  <div class="text-sm text-gray-600">
                    <p v-if="parseNotificationDetails(selectedNotification.message).leaveType">
                      <strong>Leave Type:</strong>
                      {{ parseNotificationDetails(selectedNotification.message).leaveType }}
                    </p>
                    <p v-if="parseNotificationDetails(selectedNotification.message).duration">
                      <strong>Duration:</strong>
                      {{ parseNotificationDetails(selectedNotification.message).duration }}
                    </p>
                    <p v-if="parseNotificationDetails(selectedNotification.message).actionBy">
                      <strong>{{ selectedNotification.type === 'leave_approved' ? 'Approved by' : 'Rejected by'
                      }}:</strong>
                      {{ parseNotificationDetails(selectedNotification.message).actionBy }}
                    </p>
                    <p v-if="parseNotificationDetails(selectedNotification.message).reason">
                      <strong>Rejection Reason:</strong>
                      {{ parseNotificationDetails(selectedNotification.message).reason }}
                    </p>
                  </div>
                </div>

                <div class="flex justify-end space-x-3">
                  <button @click="closeNotificationDetail"
                    class="px-4 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300 transition-colors">
                    Close
                  </button>
                </div>
              </div>
            </div>
          </div>

          <button
            class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm font-medium flex items-center space-x-1"
            @click="$router.push('/request-leave')">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            <span>New Leave Request</span>
          </button>
        </div>
      </div>

      <!-- Current Status Display -->
      <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4 mb-8 px-[30px]">
        <div class="bg-white p-4 rounded-lg shadow border flex items-center space-x-3">
          <div class="text-yellow-500 text-2xl p-2 bg-yellow-100 rounded-full">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
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
                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
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
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
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
                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
          </div>
          <div>
            <div class="text-sm text-gray-500 font-semibold">Total</div>
            <div class="text-2xl font-bold">{{ leaveRequests.length }}</div>
          </div>
        </div>
      </div>
      <div class="border-b px-6 py-4 bg-to-r from-blue-50 to-indigo-50">
        <!-- Notifications Section -->
        <div class="bg-gray rounded-lg shadow border mb-6" ref="notificationsSection">
          <!-- Wrap buttons and status in flex justify-between -->
          <div class="flex items-center justify-between mb-6 px-[30px] pt-[20px]">
            <!-- Buttons on left -->
            <div class="flex items-center space-x-4">
              <!-- All Requests Button -->
              <button @click="setStatusFilter('all')" :class="[
                'flex items-center space-x-2 px-4 py-2 rounded-md transition',
                statusFilter === 'all'
                  ? 'bg-blue-100 text-blue-600'
                  : 'text-gray-700 hover:bg-blue-100 hover:text-blue-600'
              ]">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6l4 2" />
                </svg>
                <span>All Requests</span>
              </button>


              <!-- Pending Button -->
              <button @click="setStatusFilter('pending')" :class="[
                'flex items-center space-x-2 px-4 py-2 rounded-md transition',
                statusFilter === 'pending'
                  ? 'bg-yellow-100 text-yellow-600'
                  : 'text-gray-700 hover:bg-yellow-100 hover:text-yellow-600'
              ]">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6l4 2" />
                </svg>
                <span>Pending</span>
              </button>

              <!-- Approve Button -->
              <button @click="setStatusFilter('approved')" :class="[
                'flex items-center space-x-2 px-4 py-2 rounded-md transition',
                statusFilter === 'approved'
                  ? 'bg-green-100 text-green-600'
                  : 'text-gray-700 hover:bg-green-100 hover:text-green-600'
              ]">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                </svg>
                <span>Approve</span>
              </button>

              <!-- Reject Button -->
              <button @click="setStatusFilter('rejected')" :class="[
                'flex items-center space-x-2 px-4 py-2 rounded-md transition',
                statusFilter === 'rejected'
                  ? 'bg-red-100 text-red-600'
                  : 'text-gray-700 hover:bg-red-100 hover:text-red-600'
              ]">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
                <span>Reject</span>
              </button>
            </div>
            <!-- Current Status on right -->
            <div class="ml-[30px]">
              <h2 class="text-lg font-semibold">
                Current Status:
                <span class="font-bold">{{ currentStatus }}</span>
              </h2>
            </div>
          </div>
        </div>
        <div class="max-h-96 overflow-y-auto">
          <div v-for="notification in pagedFilteredNotifications" :key="notification.id"
            class="border-l-4 hover:bg-gray-50 transition-all duration-200 cursor-pointer" :class="{
              'border-l-green-400 bg-green-50':
                notification.type === 'leave_approved' && !notification.read,
              'border-l-red-400 bg-red-50':
                notification.type === 'leave_rejected' && !notification.read,
              'border-l-yellow-400 bg-yellow-50':
                notification.type === 'leave_pending',
              'border-l-gray-300 bg-white': notification.read,
              'border-l-green-300 bg-white':
                notification.type === 'leave_approved' && notification.read,
              'border-l-red-300 bg-white':
                notification.type === 'leave_rejected' && notification.read,
            }" @click="handleNotificationClick(notification)">
            <div class="p-6">
              <!-- Notification Header -->
              <div class="flex items-start justify-between mb-3">
                <div class="flex items-center space-x-3">
                  <!-- Status Icon -->
                  <div class="flex-shrink-0">
                    <div v-if="notification.type === 'leave_approved'"
                      class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center">
                      <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                      </svg>
                    </div>
                    <div v-else-if="notification.type === 'leave_rejected'"
                      class="w-10 h-10 bg-red-100 rounded-full flex items-center justify-center">
                      <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M6 18L18 6M6 6l12 12" />
                      </svg>
                    </div>
                    <div v-else-if="notification.type === 'leave_pending'"
                      class="w-10 h-10 bg-yellow-100 rounded-full flex items-center justify-center">
                      <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                      </svg>
                    </div>
                  </div>

                  <!-- Status Text -->
                  <div class="flex-1">
                    <div class="flex items-center space-x-2">
                      <span class="font-semibold text-gray-900" :class="{
                        'text-green-700':
                          notification.type === 'leave_approved',
                        'text-red-700':
                          notification.type === 'leave_rejected',
                        'text-yellow-700':
                          notification.type === 'leave_pending',
                      }">
                        {{
                          notification.type === "leave_approved"
                            ? "Leave Request Approved"
                            : notification.type === "leave_rejected"
                              ? "Leave Request Rejected"
                              : notification.type === "leave_pending"
                                ? "Leave Request Pending"
                                : "Unknown Status"
                        }}
                      </span>
                      <span v-if="!notification.read"
                        class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                        New
                      </span>
                    </div>
                    <div class="text-xs text-gray-500 mt-1 flex items-center space-x-1">
                      <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                      </svg>
                      <span>{{ formatDate(notification.created_at) }}</span>
                    </div>
                  </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex items-center space-x-2">
                  <button v-if="!notification.read" @click.stop="markNotificationAsRead(notification.id)"
                    class="px-3 py-1 text-xs bg-blue-100 text-blue-700 rounded-full hover:bg-blue-200 transition-colors font-medium">
                    Mark Read
                  </button>
                  <button v-if="notification.type === 'leave_pending'" @click.stop="cancelLeaveRequest(notification.id)"
                    class="px-3 py-1 text-xs bg-red-100 text-red-700 rounded-full hover:bg-red-200 transition-colors font-medium">
                    Cancel Request
                  </button>
                </div>
              </div>

              <!-- Parsed Notification Content -->
              <div class="space-y-3">
                <div class="bg-gray-50 rounded-lg p-4">
                  <div class="grid grid-cols-1 gap-3 text-sm">
                    <!-- Leave Type and Dates (parsed from message) -->
                    <div v-if="
                      parseNotificationDetails(notification.message).leaveType
                    " class="flex flex-wrap items-center gap-4">
                      <div class="flex items-center space-x-2">
                        <span class="font-medium text-gray-600">Leave Type:</span>
                        <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded text-xs font-medium">
                          {{
                            parseNotificationDetails(notification.message)
                              .leaveType
                          }}
                        </span>
                      </div>
                      <div v-if="
                        parseNotificationDetails(notification.message)
                          .duration
                      " class="flex items-center space-x-2">
                        <span class="font-medium text-gray-600">Duration:</span>
                        <span class="text-gray-800 font-medium">
                          {{
                            parseNotificationDetails(notification.message)
                              .duration
                          }}
                        </span>
                      </div>
                    </div>

                    <!-- Approver/Rejector -->
                    <div v-if="
                      parseNotificationDetails(notification.message).actionBy
                    " class="flex items-center space-x-2">
                      <span class="font-medium text-gray-600">
                        {{
                          notification.type === "leave_approved"
                            ? "Approved by:"
                            : "Rejected by:"
                        }}
                      </span>
                      <span class="text-gray-800 font-medium">
                        {{
                          parseNotificationDetails(notification.message)
                            .actionBy
                        }}
                      </span>
                    </div>

                    <!-- Rejection Reason -->
                    <div v-if="
                      notification.type === 'leave_rejected' &&
                      parseNotificationDetails(notification.message).reason
                    " class="mt-3">
                      <div class="flex items-start space-x-2">
                        <svg class="w-4 h-4 text-red-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor"
                          viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
                        </svg>
                        <div>
                          <span class="font-medium text-red-800 text-xs uppercase tracking-wide">Rejection
                            Reason:</span>
                          <p class="text-red-700 text-sm mt-1 leading-relaxed">
                            {{
                              parseNotificationDetails(notification.message)
                                .reason
                            }}
                          </p>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Empty State -->
          <div v-if="pagedFilteredNotifications.length === 0" class="px-6 py-12 text-center">
            <svg class="w-12 h-12 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M15 17h5l-5-5V9.09c0-2.36-1.64-4.36-4-4.36S7 6.73 7 9.09V12l-5 5h5m8 0v1a3 3 0 11-6 0v-1m6 0H9" />
            </svg>
            <h3 class="text-lg font-medium text-gray-900 mb-2">
              No notifications yet
            </h3>
            <p class="text-sm text-gray-500">
              You'll see updates about your leave requests here
            </p>
          </div>
        </div>

        <!-- Pagination Controls -->
        <div class="flex justify-center items-center space-x-4 py-4">
          <button class="px-3 py-1 border rounded disabled:opacity-50" :disabled="currentPage === 1"
            @click="goToPage(currentPage - 1)">
            Prev
          </button>

          <span>Page {{ currentPage }} of {{ totalPages }}</span>

          <button class="px-3 py-1 border rounded disabled:opacity-50" :disabled="currentPage === totalPages"
            @click="goToPage(currentPage + 1)">
            Next
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from "vue";
import axios from "axios";
import Calendar from "@/views/student/Calendar.vue";


const user = ref({});
const notifications = ref([]);
const leaveRequests = ref([]);
const pendingCount = ref(0);
const approvedCount = ref(0);
const rejectedCount = ref(0);
const currentPage = ref(1);
const pageSize = 5;
const successMessage = ref(null);

// Reactive variables for current status and filter
const currentStatus = ref("All Requests");
const statusFilter = ref("all");

// Reactive variables for notification summary
const showNotificationSummary = ref(false);
const notificationsSection = ref(null);

// Reactive variables for notification detail modal
const showNotificationDetail = ref(false);
const selectedNotification = ref(null);

const showCalendar = ref(false)

const navigateToCalendar = () => {
  router.push('/calendar')
}

const displayName = computed(() => {
  if (!user.value) return "Guest";
  return (
    user.value.name ||
    user.value.full_name ||
    user.value.first_name ||
    (user.value.email ? user.value.email.split("@")[0] : null) ||
    "User"
  );
});

const totalPages = computed(() =>
  Math.ceil(filteredNotifications.value.length / pageSize)
);

const pagedFilteredNotifications = computed(() => {
  const start = (currentPage.value - 1) * pageSize;
  const end = start + pageSize;
  return filteredNotifications.value.slice(start, end);
});

// Computed properties for notification summary
const unreadNotificationCount = computed(() => {
  return notifications.value.filter((n) => !n.read).length;
});

const recentNotifications = computed(() => {
  return notifications.value
    .sort((a, b) => new Date(b.created_at) - new Date(a.created_at))
    .slice(0, 3);
});

// Modified computed property to include pending requests in "all"
const filteredNotifications = computed(() => {
  // Create pseudo-notifications for pending leave requests
  const pendingNotifications = leaveRequests.value
    .filter((r) => r.status === "pending")
    .map((r) => ({
      id: `leave-${r.id}`,
      type: "leave_pending",
      message: `Leave request for ${r.leave_type} from ${r.from_date} to ${r.to_date} is awaiting approval.`,
      created_at: r.created_at,
      read: true, // Nothing to mark as read for pending requests
    }));

  switch (statusFilter.value) {
    case "approved":
      return notifications.value.filter((n) => n.type === "leave_approved");
    case "rejected":
      return notifications.value.filter((n) => n.type === "leave_rejected");
    case "pending":
      return pendingNotifications;
    default: // "all"
      // Combine actual notifications with pending pseudo-notifications
      return [
        ...notifications.value,
        ...pendingNotifications,
      ].sort((a, b) => new Date(b.created_at) - new Date(a.created_at)); // Sort by date, newest first
  }
});

// Set status filter and update current status display
const setStatusFilter = (status) => {
  statusFilter.value = status;
  currentPage.value = 1; // Reset to first page when filter changes
  currentStatus.value =
    status === "all"
      ? "All Requests"
      : status.charAt(0).toUpperCase() + status.slice(1);
};

// Toggle notification summary
const toggleNotificationSummary = () => {
  showNotificationSummary.value = !showNotificationSummary.value;
};

// Scroll to notifications section
const scrollToNotifications = () => {
  showNotificationSummary.value = false;
  if (notificationsSection.value) {
    notificationsSection.value.scrollIntoView({
      behavior: "smooth",
      block: "start",
    });
  }
};

// Handle notification click and open detail modal
const handleNotificationClick = async (notification) => {
  selectedNotification.value = notification;
  showNotificationDetail.value = true;
  showNotificationSummary.value = false;

  // Mark notification as read when clicked (if not already read)
  if (!notification.read && notification.type !== "leave_pending") {
    await markNotificationAsRead(notification.id);
  }
};

// Close notification detail modal
const closeNotificationDetail = () => {
  showNotificationDetail.value = false;
  selectedNotification.value = null;
};

// Parse notification message to extract structured information
const parseNotificationDetails = (message) => {
  if (!message) return {};

  const details = {};

  // Extract leave type (pattern: "for [LeaveType] from")
  const leaveTypeMatch = message.match(/for\s+(.+?)\s+from/i);
  if (leaveTypeMatch) {
    details.leaveType = leaveTypeMatch[1].trim();
  }

  // Extract duration (pattern: "from YYYY-MM-DD to YYYY-MM-DD")
  const durationMatch = message.match(
    /from\s+(\d{4}-\d{2}-\d{2})\s+to\s+(\d{4}-\d{2}-\d{2})/i
  );
  if (durationMatch) {
    const fromDate = new Date(durationMatch[1]).toLocaleDateString("en-US", {
      year: "numeric",
      month: "short",
      day: "numeric",
    });
    const toDate = new Date(durationMatch[2]).toLocaleDateString("en-US", {
      year: "numeric",
      month: "short",
      day: "numeric",
    });
    details.duration = `${fromDate} - ${toDate}`;
  }

  // Extract action by (pattern: "approved/rejected by [Name]")
  const actionByMatch = message.match(
    /(?:approved|rejected)\s+by\s+(.+?)(?:\.|$|\s+Reason:)/i
  );
  if (actionByMatch) {
    details.actionBy = actionByMatch[1].trim();
  }

  // Extract rejection reason (pattern: "Reason: [reason]")
  const reasonMatch = message.match(/Reason:\s*(.+?)$/i);
  if (reasonMatch) {
    details.reason = reasonMatch[1].trim();
  }

  return details;
};

// Close notification summary or modal when clicking outside
const handleClickOutside = (event) => {
  if (showNotificationSummary.value && !event.target.closest(".relative")) {
    showNotificationSummary.value = false;
  }
  if (showNotificationDetail.value && !event.target.closest(".rounded-2xl")) {
    showNotificationDetail.value = false;
    selectedNotification.value = null;
  }
};

// Fetch user data
const fetchUser = async () => {
  const stored = localStorage.getItem("user_data");
  if (stored) user.value = JSON.parse(stored);

  try {
    const token = localStorage.getItem("authToken");
    if (token) {
      const response = await axios.get("http://127.0.0.1:8000/api/user", {
        headers: { Authorization: `Bearer ${token}` },
      });
      user.value = response.data;
      localStorage.setItem("user_data", JSON.stringify(user.value));
    }
  } catch (error) {
    console.error("Failed to fetch user:", error);
  }
};

// Fetch notifications
const fetchNotifications = async () => {
  try {
    const token = localStorage.getItem("authToken");
    const response = await axios.get(
      "http://127.0.0.1:8000/api/notifications",
      {
        headers: { Authorization: `Bearer ${token}` },
      }
    );
    notifications.value = response.data;
  } catch (error) {
    console.error("Failed to fetch notifications:", error);
  }
};

// Mark notification as read
const markNotificationAsRead = async (id) => {
  try {
    const token = localStorage.getItem("authToken");
    await axios.post(
      `http://127.0.0.1:8000/api/notifications/${id}/read`,
      {},
      {
        headers: { Authorization: `Bearer ${token}` },
      }
    );
    notifications.value = notifications.value.map((n) =>
      n.id === id ? { ...n, read: true } : n
    );
  } catch (error) {
    console.error("Failed to mark notification as read:", error);
  }
};

// Fetch leave requests
const fetchLeaveRequests = async () => {
  try {
    const token = localStorage.getItem("authToken");
    const response = await axios.get(
      "http://127.0.0.1:8000/api/student/my-leaves",
      {
        headers: {
          Authorization: `Bearer ${token}`,
        },
      }
    );

    leaveRequests.value = response.data.leaves.map((leave) => ({
      ...leave,
      approved_by: leave.approved_by || "N/A",
    }));
    pendingCount.value = leaveRequests.value.filter(
      (r) => r.status === "pending"
    ).length;
    approvedCount.value = leaveRequests.value.filter(
      (r) => r.status === "approved"
    ).length;
    rejectedCount.value = leaveRequests.value.filter(
      (r) => r.status === "rejected"
    ).length;
  } catch (error) {
    console.error("Failed to fetch leave requests:", error);
  }
};

// Cancel leave request
const cancelLeaveRequest = async (id) => {
  const leaveId = id.startsWith("leave-") ? id.replace("leave-", "") : id;
  try {
    const token = localStorage.getItem("authToken");
    await axios.delete(
      `http://127.0.0.1:8000/api/student/leave-request/${leaveId}`,
      {
        headers: { Authorization: `Bearer ${token}` },
      }
    );
    successMessage.value = "Your leave request has been cancelled.";
    setTimeout(() => {
      successMessage.value = null;
    }, 3000); // Hide alert after 3 seconds
    await fetchLeaveRequests(); // Refresh the leave requests
    await fetchNotifications(); // Refresh notifications
  } catch (err) {
    window.alert(`Failed to cancel leave: ${err.response?.data?.message || err.message}`);
    if (err.response?.status === 401 && typeof router !== 'undefined') router.push("/login");
    console.error("Cancel leave error:", err);
  }
};

// Format date for display
function formatDate(dateStr) {
  if (!dateStr) return "";
  const date = new Date(dateStr);
  return date.toLocaleDateString("en-US", {
    year: "numeric",
    month: "long",
    day: "numeric",
  });
}

// Navigate to a specific page
function goToPage(page) {
  if (page >= 1 && page <= totalPages.value) {
    currentPage.value = page;
  }
}

// Initialize data on component mount
onMounted(() => {
  fetchUser();
  fetchNotifications();
  fetchLeaveRequests();
  document.addEventListener("click", handleClickOutside);
});

// Cleanup event listener
onUnmounted(() => {
  document.removeEventListener("click", handleClickOutside);
});
</script>