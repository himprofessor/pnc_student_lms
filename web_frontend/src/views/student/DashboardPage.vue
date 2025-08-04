<template>

    <div class="px-6 py-6">
      <!-- Header -->
      <div class="flex justify-between items-center mb-6 px-[30px]">
        <div>
          <h1 class="text-2xl font-bold">Welcome back {{ displayName }}</h1>
          <p class="text-sm text-gray-500">
            Manage your leave requests and track their status
          </p>
        </div>
        <div class="flex items-center space-x-4">
          <!-- Notification Icon -->
          <div class="relative">
            <button
              @click="toggleNotificationSummary"
              class="relative p-2 text-gray-600 hover:text-gray-800 hover:bg-gray-100 rounded-full transition-colors"
            >
              <svg
                class="w-6 h-6"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M15 17h5l-5-5V9.09c0-2.36-1.64-4.36-4-4.36S7 6.73 7 9.09V12l-5 5h5m8 0v1a3 3 0 11-6 0v-1m6 0H9"
                />
              </svg>

              <!-- Notification Badge -->
              <span
                v-if="unreadNotificationCount > 0"
                class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center font-medium"
              >
                {{ unreadNotificationCount > 9 ? "9+" : unreadNotificationCount }}
              </span>
            </button>

            <!-- Notification Summary Card -->
            <div
              v-if="showNotificationSummary"
              class="absolute right-0 top-12 w-96 bg-white rounded-lg shadow-lg border z-50"
            >
              <div class="p-4 border-b">
                <div class="flex justify-between items-center">
                  <h3 class="text-lg font-semibold text-gray-800">
                    Notification Summary
                  </h3>
                  <button
                    @click="showNotificationSummary = false"
                    class="text-gray-400 hover:text-gray-600"
                  >
                    <svg
                      class="w-5 h-5"
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

                    <div
                      v-for="notification in recentNotifications"
                      :key="notification.id"
                      class="flex items-start space-x-3 p-2 rounded-lg cursor-pointer hover:bg-gray-50 transition-colors"
                      :class="{ 'bg-blue-50': !notification.read }"
                      @click="handleNotificationClick(notification)"
                    >
                      <svg
                        v-if="notification.type === 'leave_approved'"
                        class="w-4 h-4 text-green-500 mt-0.5 flex-shrink-0"
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
                        v-else-if="notification.type === 'leave_rejected'"
                        class="w-4 h-4 text-red-500 mt-0.5 flex-shrink-0"
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

                      <div class="flex-1 min-w-0">
                        <p
                          class="text-sm text-gray-800 truncate font-semibold"
                        >
                          {{ notification.message }}
                        </p>
                        <p class="text-sm text-gray-500 font-medium">
                          {{ formatDate(notification.created_at) }}
                        </p>
                      </div>

                      <span
                        v-if="!notification.read"
                        class="w-2 h-2 bg-blue-500 rounded-full flex-shrink-0 mt-1"
                      ></span>
                    </div>

                    <div
                      v-if="recentNotifications.length === 0"
                      class="text-sm text-gray-500 text-center py-2"
                    >
                      No recent notifications
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Notification Detail Modal -->
          <div
            v-if="showNotificationDetail"
            class="fixed inset-0 z-50 bg-black bg-opacity-60 flex items-center justify-center p-4 backdrop-blur-sm"
          >
            <div
              class="bg-white rounded-2xl w-full max-w-md shadow-2xl overflow-hidden"
            >
              <!-- Modal Header -->
              <div
                class="bg-gradient-to-r from-blue-600 to-purple-600 px-6 py-4 text-white"
              >
                <div class="flex justify-between items-center">
                  <h3 class="text-lg font-semibold">Notification Details</h3>
                  <button
                    @click="closeNotificationDetail"
                    class="text-white hover:text-gray-200 transition-colors"
                  >
                    <svg
                      class="w-5 h-5"
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
                  </button>
                </div>
              </div>

              <!-- Modal Content -->
              <div class="p-6" v-if="selectedNotification">
                <div class="flex items-start space-x-3 mb-4">
                  <svg
                    v-if="selectedNotification.type === 'leave_approved'"
                    class="w-8 h-8 text-green-500 flex-shrink-0"
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
                    v-else-if="selectedNotification.type === 'leave_rejected'"
                    class="w-8 h-8 text-red-500 flex-shrink-0"
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
                  <svg
                    v-else-if="selectedNotification.type === 'leave_pending'"
                    class="w-8 h-8 text-yellow-500 flex-shrink-0"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                  >
                    <path
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2"
                      d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"
                    />
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

                <div
                  v-if="selectedNotification.data || parseNotificationDetails(selectedNotification.message)"
                  class="bg-gray-50 rounded-lg p-4 mb-4"
                >
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
                      <strong>{{ selectedNotification.type === 'leave_approved' ? 'Approved by' : 'Rejected by' }}:</strong>
                      {{ parseNotificationDetails(selectedNotification.message).actionBy }}
                    </p>
                    <p v-if="parseNotificationDetails(selectedNotification.message).reason">
                      <strong>Rejection Reason:</strong>
                      {{ parseNotificationDetails(selectedNotification.message).reason }}
                    </p>
                  </div>
                </div>

                <div class="flex justify-end space-x-3">
                  <button
                    @click="closeNotificationDetail"
                    class="px-4 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300 transition-colors"
                  >
                    Close
                  </button>
                </div>
              </div>
            </div>
          </div>

          <button
            class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm font-medium flex items-center space-x-1"
            @click="$router.push('/request-leave')"
          >
            <svg
              class="w-4 h-4"
              fill="none"
              stroke="currentColor"
              viewBox="0 0 24 24"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M12 4v16m8-8H4"
              />
            </svg>
            <span>New Leave Request</span>
          </button>
        </div>
      </div>

      <!-- Current Status Display -->
      <div
        class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4 mb-8 px-[30px]"
      >
        <div
          class="bg-white p-4 rounded-lg shadow border flex items-center space-x-3"
        >
          <div class="text-yellow-500 text-2xl p-2 bg-yellow-100 rounded-full">
            <svg
              class="w-6 h-6"
              fill="none"
              stroke="currentColor"
              viewBox="0 0 24 24"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"
              />
            </svg>
          </div>
          <div>
            <div class="text-sm text-gray-500 font-semibold">Pending</div>
            <div class="text-2xl font-bold">{{ pendingCount }}</div>
          </div>
        </div>
        <div
          class="bg-white p-4 rounded-lg shadow border flex items-center space-x-3"
        >
          <div class="text-green-500 text-2xl p-2 bg-green-100 rounded-full">
            <svg
              class="w-6 h-6"
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
          </div>
          <div>
            <div class="text-sm text-gray-500 font-semibold">Approved</div>
            <div class="text-2xl font-bold">{{ approvedCount }}</div>
          </div>
        </div>
        <div
          class="bg-white p-4 rounded-lg shadow border flex items-center space-x-3"
        >
          <div class="text-red-500 text-2xl p-2 bg-red-100 rounded-full">
            <svg
              class="w-6 h-6"
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
          </div>
          <div>
            <div class="text-sm text-gray-500 font-semibold">Rejected</div>
            <div class="text-2xl font-bold">{{ rejectedCount }}</div>
          </div>
        </div>
        <div
          class="bg-white p-4 rounded-lg shadow border flex items-center space-x-3"
        >
          <div class="text-blue-500 text-2xl p-2 bg-blue-100 rounded-full">
            <svg
              class="w-6 h-6"
              fill="none"
              stroke="currentColor"
              viewBox="0 0 24 24"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"
              />
            </svg>
          </div>
          <div>
            <div class="text-sm text-gray-500 font-semibold">Total</div>
            <div class="text-2xl font-bold">{{ leaveRequests.length }}</div>
          </div>
        </div>
      </div>
    </div>
<notification/>
</template>
<script setup>
import notification from "./notification.vue";  
import { ref, computed, onMounted, onUnmounted } from "vue";
import axios from "axios";

const user = ref({});
const notifications = ref([]);
const leaveRequests = ref([]);
const pendingCount = ref(0);
const approvedCount = ref(0);
const rejectedCount = ref(0);
const currentPage = ref(1);
const pageSize = 5;

// Reactive variables for current status and filter

const statusFilter = ref("all");

// Reactive variables for notification summary
const showNotificationSummary = ref(false);
const notificationsSection = ref(null);

// Reactive variables for notification detail modal
const showNotificationDetail = ref(false);
const selectedNotification = ref(null);

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