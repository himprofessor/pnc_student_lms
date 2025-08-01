<template>
    <div class="min-h-screen bg-gray-50 text-gray-800">
      <div class="px-6 py-6">
        <!-- Header -->
        <div class="flex justify-between items-center mb-6">
          <div>
            <h1 class="text-2xl font-bold">Welcome back {{ displayName }}</h1>
            <p class="text-sm text-gray-500">Manage your leave requests and track their status</p>
          </div>
          <div class="flex items-center space-x-4">
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
                  {{ unreadNotificationCount > 9 ? '9+' : unreadNotificationCount }}
                </span>
              </button>
  
              <!-- Notification Summary Card -->
              <div v-if="showNotificationSummary"
                class="absolute right-0 top-12 w-96 bg-white rounded-lg shadow-lg border z-50">
                <div class="p-6 border-b">
                  <div class="flex justify-between items-center">
                    <h3 class="text-lg font-semibold text-gray-800">Notification Summary</h3>
                    <button @click="showNotificationSummary = false" class="text-gray-400 hover:text-gray-600">
                      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M6 18L18 6M6 6l12 12" />
                      </svg>
                    </button>
                  </div>
                </div>
  
                <div class="p-6">
                  <!-- Recent Notifications -->
                  <div class="space-y-3">
                    <h4 class="text-sm font-medium text-gray-700 mb-2">Recent Updates</h4>
  
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
                        <p class="text-sm text-gray-800 truncate font-semibold">{{
                          notification.message }}</p>
                        <p class="text-sm text-gray-500 font-medium">{{
                          formatDate(notification.created_at) }}</p>
                      </div>
  
                      <span v-if="!notification.read"
                        class="w-2 h-2 bg-blue-500 rounded-full flex-shrink-0 mt-1"></span>
                    </div>
  
                    <div v-if="recentNotifications.length === 0" class="text-sm text-gray-500 text-center py-2">
                      No recent notifications
                    </div>
                  </div>
  
                  <!-- View All Link -->
                  <div class="mt-4 pt-3 border-t">
                    <button @click="scrollToNotifications"
                      class="w-full text-sm text-blue-600 hover:text-blue-800 font-medium">
                      View All Notifications
                    </button>
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
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M6 18L18 6M6 6l12 12" />
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
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M6 18L18 6M6 6l12 12" />
                    </svg>
  
                    <div class="flex-1">
                      <div class="text-lg font-semibold text-gray-800 mb-2">
                        {{ selectedNotification.type === 'leave_approved' ? 'Leave Request Approved'
                        : 'Leave Request Rejected' }}
                      </div>
                      <div class="text-sm text-gray-600 mb-3">
                        {{ formatDate(selectedNotification.created_at) }}
                      </div>
                    </div>
                  </div>
  
                  <div class="bg-gray-50 rounded-lg p-4 mb-4">
                    <h4 class="font-semibold text-gray-800 mb-2">Message:</h4>
                    <p class="text-gray-700 leading-relaxed">{{ selectedNotification.message }}</p>
                  </div>
  
                  <div v-if="selectedNotification.data" class="bg-gray-50 rounded-lg p-4 mb-4">
                    <h4 class="font-semibold text-gray-800 mb-2">Additional Details:</h4>
                    <div class="text-sm text-gray-600">
                      <p v-if="selectedNotification.data.leave_type">
                        <strong>Leave Type:</strong> {{ selectedNotification.data.leave_type }}
                      </p>
                      <p v-if="selectedNotification.data.from_date && selectedNotification.data.to_date">
                        <strong>Duration:</strong> {{
                        formatDate(selectedNotification.data.from_date) }} - {{
                        formatDate(selectedNotification.data.to_date) }}
                      </p>
                      <p v-if="selectedNotification.data.approved_by">
                        <strong>Approved by:</strong> {{ selectedNotification.data.approved_by }}
                      </p>
                      <p v-if="selectedNotification.data.rejection_reason">
                        <strong>Rejection Reason:</strong> {{
                        selectedNotification.data.rejection_reason }}
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
  
        <!-- Summary Cards section -->
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4 mb-8">
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
                  d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
              </svg>
            </div>
            <div>
              <div class="text-sm text-gray-500 font-semibold">Total</div>
              <div class="text-2xl font-bold">{{ allDisplayableNotifications.length }}</div>
            </div>
          </div>
        </div>
  
        <!-- Notifications Section -->
        <div class="bg-white rounded-lg shadow border mb-6" ref="notificationsSection">
          <div class="border-b px-6 py-4">
            <h2 class="text-lg font-semibold">Notifications</h2>
            <p class="text-sm text-gray-500">Your latest notifications</p>
            <!-- Notification Filter Buttons -->
            <div class="flex space-x-2 mt-3">
              <button
                @click="currentNotificationFilter = 'All'; currentPage = 1;"
                :class="{ 'bg-blue-600 text-white': currentNotificationFilter === 'All', 'bg-gray-200 text-gray-800': currentNotificationFilter !== 'All' }"
                class="px-3 py-1 rounded-md text-xs font-medium"
              >
                All
              </button>
              <button
                @click="currentNotificationFilter = 'Approved'; currentPage = 1;"
                :class="{ 'bg-green-600 text-white': currentNotificationFilter === 'Approved', 'bg-gray-200 text-gray-800': currentNotificationFilter !== 'Approved' }"
                class="px-3 py-1 rounded-md text-xs font-medium"
              >
                Approved
              </button>
              <button
                @click="currentNotificationFilter = 'Rejected'; currentPage = 1;"
                :class="{ 'bg-red-600 text-white': currentNotificationFilter === 'Rejected', 'bg-gray-200 text-gray-800': currentNotificationFilter !== 'Rejected' }"
                class="px-3 py-1 rounded-md text-xs font-medium"
              >
                Rejected
              </button>
              <button
                @click="currentNotificationFilter = 'Pending'; currentPage = 1;"
                :class="{ 'bg-orange-600 text-white': currentNotificationFilter === 'Pending', 'bg-gray-200 text-gray-800': currentNotificationFilter !== 'Pending' }"
                class="px-3 py-1 rounded-md text-xs font-medium"
              >
                Pending
              </button>
            </div>
          </div>
          <div class="divide-y">
            <div
              v-for="notification in paginatedDisplayableNotifications"
              :key="notification.id"
              class="flex justify-between items-center px-6 py-4"
              :class="{ 'bg-blue-50': !notification.read }"
            >
              <div class="flex items-center space-x-3">
                <svg
                  v-if="notification.type === 'leave_approved'"
                  class="w-6 h-6 text-green-500"
                  fill="none"
                  stroke="currentColor"
                  viewBox="0 0 24 24"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"
                  />
                </svg>
                <svg
                  v-if="notification.type === 'leave_rejected'"
                  class="w-6 h-6 text-red-500"
                  fill="none"
                  stroke="currentColor"
                  viewBox="0 0 24 24"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M6 18L18 6M6 6l12 12"
                  />
                </svg>
                <!-- SVG for Pending Notifications -->
                <svg
                  v-if="notification.type === 'leave_pending'"
                  class="w-6 h-6 text-orange-500"
                  fill="none"
                  stroke="currentColor"
                  viewBox="0 0 24 24"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M8.228 9.247a4.5 4.5 0 00.952 1.789l1.644 1.644a4.5 4.5 0 001.789.952M12 16V8m-4 4h8m-4 4v4m0-4h4m-4-4H8m4 0h4m-4-4V4m0 4h4"
                  />
                </svg>
                <div>
                  <div class="text-sm">{{ notification.message }}</div>
                  <div class="text-xs text-gray-500">{{ formatDate(notification.created_at) }}</div>
                </div>
              </div>
              <button
                v-if="!notification.read && !notification.isLeaveRequest"
                @click="markNotificationAsRead(notification.id)"
                class="text-blue-600 text-xs underline"
              >
                Mark as Read
              </button>
            </div>
            <div v-if="filteredNotifications.length === 0" class="px-6 py-4 text-sm text-gray-500">
              No notifications found for this status.
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
  import { ref, computed, onMounted, onUnmounted, watch } from "vue";
  import axios from "axios";
  import Swal from "sweetalert2";
  
  const user = ref({});
  const notifications = ref([]);
  const leaveRequests = ref([]);
  const leaveTypes = ref([]);
  // Removed pendingCount, approvedCount, rejectedCount as refs, they will be computed
  const currentPage = ref(1);
  const pageSize = 5;
  
  const showNotificationSummary = ref(false);
  const notificationsSection = ref(null);
  const showNotificationDetail = ref(false);
  const selectedNotification = ref(null);
  const currentNotificationFilter = ref('All');
  const pollingInterval = ref(null);
  
  const displayName = computed(() => {
      if (!user.value) return 'Guest';
      return user.value.name ||
          user.value.full_name ||
          user.value.first_name ||
          (user.value.email ? user.value.split('@')[0] : null) ||
          'User';
  });
  
  const pendingLeaveNotifications = computed(() => {
    return leaveRequests.value
      .filter(lr => lr.status === 'pending')
      .map(lr => {
        const leaveTypeName = lr.leave_type || 'Unknown Leave Type';
        return {
          id: `lr-${lr.id}`,
          message: `Your ${leaveTypeName} request for '${lr.reason}' from ${formatDate(lr.from_date)} to ${formatDate(lr.to_date)} is pending.`,
          type: 'leave_pending',
          read: false,
          created_at: lr.created_at,
          isLeaveRequest: true
        };
      });
  });
  
  const allDisplayableNotifications = computed(() => {
    return [...notifications.value, ...pendingLeaveNotifications.value]
      .sort((a, b) => new Date(b.created_at) - new Date(a.created_at));
  });
  
  const filteredNotifications = computed(() => {
    let filtered = allDisplayableNotifications.value;
  
    if (currentNotificationFilter.value === 'Approved') {
      filtered = filtered.filter(n => n.type === 'leave_approved');
    } else if (currentNotificationFilter.value === 'Rejected') {
      filtered = filtered.filter(n => n.type === 'leave_rejected');
    } else if (currentNotificationFilter.value === 'Pending') {
      filtered = filtered.filter(n => n.type === 'leave_pending');
    } 
    return filtered;
  });
  
  // --- Start Header Counts and Recent Notifications Updates ---
  
  // Now computed properties, deriving from allDisplayableNotifications
  const pendingCount = computed(() => {
    return allDisplayableNotifications.value.filter(n => n.type === 'leave_pending').length;
  });
  
  const approvedCount = computed(() => {
    return allDisplayableNotifications.value.filter(n => n.type === 'leave_approved').length;
  });
  
  const rejectedCount = computed(() => {
    return allDisplayableNotifications.value.filter(n => n.type === 'leave_rejected').length;
  });
  
  // unreadNotificationCount now considers all displayable notifications
  const unreadNotificationCount = computed(() => {
      return allDisplayableNotifications.value.filter(n => !n.read).length;
  });
  
  // recentNotifications now considers all displayable notifications
  const recentNotifications = computed(() => {
      return allDisplayableNotifications.value
          .sort((a, b) => new Date(b.created_at) - new Date(a.created_at))
          .slice(0, 3);
  });
  
  // --- End Header Counts and Recent Notifications Updates ---
  
  
  const totalPages = computed(() =>
      Math.ceil(filteredNotifications.value.length / pageSize)
  );
  
  const paginatedDisplayableNotifications = computed(() => {
      const start = (currentPage.value - 1) * pageSize;
      const end = start + pageSize;
      return filteredNotifications.value.slice(start, end);
  });
  
  watch(filteredNotifications, () => {
    if (currentPage.value > totalPages.value && totalPages.value > 0) {
      currentPage.value = totalPages.value;
    } else if (totalPages.value === 0) {
      currentPage.value = 1;
    }
  });
  
  const toggleNotificationSummary = () => {
      showNotificationSummary.value = !showNotificationSummary.value;
  };
  
  const scrollToNotifications = () => {
      showNotificationSummary.value = false;
      if (notificationsSection.value) {
          notificationsSection.value.scrollIntoView({
              behavior: 'smooth',
              block: 'start'
          });
      }
  };
  
  const handleNotificationClick = async (notification) => {
    selectedNotification.value = notification;
    showNotificationDetail.value = true;
    showNotificationSummary.value = false;

    if (!notification.read) { // If it's currently unread
        if (notification.isLeaveRequest) {
            // For transformed leave requests, mark as read locally
            readLeaveNotificationIds.value.add(notification.id);
        } else {
            // For actual backend notifications, call the API
            await markNotificationAsRead(notification.id);
        }
    }
};
  
  const closeNotificationDetail = () => {
      showNotificationDetail.value = false;
      selectedNotification.value = null;
  };
  
  const handleClickOutside = (event) => {
      if (showNotificationSummary.value && !event.target.closest('.relative')) {
          showNotificationSummary.value = false;
      }
  
      if (showNotificationDetail.value && !event.target.closest('.rounded-2xl')) {
          showNotificationDetail.value = false;
          selectedNotification.value = null;
      }
  };
  
  const fetchUser = async () => {
      const stored = localStorage.getItem('user_data');
      if (stored) user.value = JSON.parse(stored);
  
      try {
          const token = localStorage.getItem('authToken');
          if (token) {
              const response = await axios.get('http://127.0.0.1:8000/api/user', {
                  headers: { 'Authorization': `Bearer ${token}` }
              });
              user.value = response.data;
              localStorage.setItem('user_data', JSON.stringify(user.value));
          }
      } catch (error) {
          console.error('Failed to fetch user:', error);
      }
  };
  
  const fetchNotifications = async () => {
      try {
          const token = localStorage.getItem('authToken');
          const response = await axios.get('http://127.0.0.1:8000/api/notifications', {
              headers: { Authorization: `Bearer ${token}` }
          });
          notifications.value = response.data;
      } catch (error) {
          console.error('Failed to fetch notifications:', error);
      }
  };
  
  const markNotificationAsRead = async (id) => {
      try {
          const token = localStorage.getItem('authToken');
          await axios.post(`http://127.0.0.1:8000/api/notifications/${id}/read`, {}, {
              headers: { Authorization: `Bearer ${token}` }
          });
          notifications.value = notifications.value.map(n =>
              n.id === id ? { ...n, read: true } : n
          );
      } catch (error) {
          console.error('Failed to mark notification as read:', error);
      }
  };
  
  const fetchLeaveTypes = async () => {
      try {
          const token = localStorage.getItem('authToken');
          const response = await axios.get('http://127.0.0.1:8000/api/leave-types', {
              headers: { Authorization: `Bearer ${token}` }
          });
          leaveTypes.value = response.data.data;
      } catch (error) {
          console.error('Failed to fetch leave types:', error);
      }
  };
  
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
  
          leaveRequests.value = response.data.leaves.map(leave => ({
              ...leave,
              approved_by: leave.approved_by || 'N/A',
          }));
      } catch (error) {
          console.error("Failed to fetch leave requests:", error);
      }
  };
  
  function formatDate(dateStr) {
      if (!dateStr) return "";
      const date = new Date(dateStr);
      return date.toLocaleDateString("en-US", {
          year: "numeric",
          month: "long",
          day: "numeric",
      });
  }
  
  function goToPage(page) {
      if (page >= 1 && page <= totalPages.value) {
          currentPage.value = page;
      }
  }
  
  const cancelLeaveRequest = async (id) => {
      const result = await Swal.fire({
          title: 'Confirm Cancellation',
          text: 'Are you sure you want to cancel this leave request?',
          icon: 'warning',
          showCancelButton: true,
          confirmButtonText: 'Yes, Cancel It',
          cancelButtonText: 'No, Keep It',
          customClass: {
              confirmButton: 'bg-red-400 hover:bg-red-400 text-white text-sm py-2 rounded mr-2',
              cancelButton: 'bg-blue-400 hover:bg-blue-400 text-white text-sm py-2 rounded',
          },
          background: '#fff',
          backdrop: 'rgba(0,0,0,0.4)',
      });
      if (!result.isConfirmed) return;
      try {
          const token = localStorage.getItem('authToken');
          await axios.delete(`http://127.0.0.1:8000/api/student/leave-request/${id}`, {
              headers: { Authorization: `Bearer ${token}` },
          });
          await Swal.fire({
              icon: 'success',
              title: 'Leave Cancelled',
              text: 'Your leave request has been cancelled.',
              iconColor: '#16a34a',
              confirmButtonText: 'OK',
              customClass: { confirmButton: 'bg-green-400 hover:bg-green-400 text-white text-sm py-2 rounded' },
              background: '#fff',
          });
          await fetchLeaveRequests();
      } catch (err) {
          await Swal.fire({
              icon: 'error',
              title: 'Error',
              text: `Failed to cancel leave: ${err.response?.data?.message || err.message}`,
              iconColor: '#dc2626',
              confirmButtonText: 'OK',
              customClass: { confirmButton: 'bg-red-400 hover:bg-red-400 text-white text-sm py-2 rounded' },
              background: '#fff',
          });
          if (err.response?.status === 401) router.push('/login');
          console.error('Cancel leave error:', err);
      }
  };
  
  onMounted(() => {
      fetchUser();
      fetchNotifications();
      fetchLeaveRequests();
      fetchLeaveTypes();
  });
  
  onUnmounted(() => {
      document.removeEventListener('click', handleClickOutside);
      if (pollingInterval.value) {
          clearInterval(pollingInterval.value);
      }
  });
  </script>
  