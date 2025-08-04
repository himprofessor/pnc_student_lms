<template>
    <div class="border-b bg-gradient-to-r from--50 to-indigo-50 px-[55px]">
        <!-- Notifications Section -->
        <div class="bg-gray rounded-lg shadow border mb-6" ref="notificationsSection">
            <!-- Wrap buttons and status in flex justify-between -->
            <div class="flex items-center justify-between mb-6 px-[30px] pt-[20px]">
                <!-- Buttons on left -->
                <div class="flex items-center space-x-4">
                    <button @click="setStatusFilter('all')"
                        class="flex items-center space-x-2 px-4 py-2 rounded-md text-gray-700 hover:bg-blue-100 hover:text-blue-600 transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6l4 2"></path>
                        </svg>
                        <span>All Requests</span>
                    </button>
                    <button @click="setStatusFilter('pending')"
                        class="flex items-center space-x-2 px-4 py-2 rounded-md text-gray-700 hover:bg-yellow-100 hover:text-yellow-600 transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6l4 2"></path>
                        </svg>
                        <span>Pending</span>
                    </button>
                    <button @click="setStatusFilter('approved')"
                        class="flex items-center space-x-2 px-4 py-2 rounded-md text-gray-700 hover:bg-green-100 hover:text-green-600 transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path>
                        </svg>
                        <span>Approve</span>
                    </button>
                    <button @click="setStatusFilter('rejected')"
                        class="flex items-center space-x-2 px-4 py-2 rounded-md text-gray-700 hover:bg-red-100 hover:text-red-600 transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path>
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
                                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <div v-else-if="notification.type === 'leave_rejected'"
                                    class="w-10 h-10 bg-red-100 rounded-full flex items-center justify-center">
                                    <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </div>
                                <div v-else-if="notification.type === 'leave_pending'"
                                    class="w-10 h-10 bg-yellow-100 rounded-full flex items-center justify-center">
                                    <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
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

                        <!-- Mark as Read Button -->
                        <button v-if="!notification.read" @click.stop="markNotificationAsRead(notification.id)"
                            class="px-3 py-1 text-xs bg-blue-100 text-blue-700 rounded-full hover:bg-blue-200 transition-colors font-medium">
                            Mark Read
                        </button>
                    </div>

                    <!-- Cancel Button for Pending Requests -->
                    <div v-if="notification.type === 'leave_pending'" class="mt-2">
                        <!-- <button
                @click.stop="cancelLeaveRequest(notification.id)"
                class="px-4 py-2 bg-red-500 text-white rounded-md hover:bg-red-600 transition-colors"
              >
                Cancel
              </button> -->
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
                                        <svg class="w-4 h-4 text-red-500 mt-0.5 flex-shrink-0" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
                                        </svg>
                                        <div>
                                            <span
                                                class="font-medium text-red-800 text-xs uppercase tracking-wide">Rejection
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

                <!-- Empty State -->
                <div v-if="notifications.length === 0" class="px-6 py-12 text-center">
                    <svg class="w-12 h-12 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
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
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from "vue";
import axios from "axios";
import Swal from "sweetalert2";

const user = ref({});
const notifications = ref([]);
const leaveRequests = ref([]);
const pendingCount = ref(0);
const approvedCount = ref(0);
const rejectedCount = ref(0);
const currentPage = ref(1);
const pageSize = 5;

// New reactive variable for current status
const currentStatus = ref("");
const statusFilter = ref(""); // New reactive variable for status filter

// New reactive variables for notification summary
const showNotificationSummary = ref(false);
const notificationsSection = ref(null);

// New reactive variables for notification detail modal
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

const pagedLeaveRequests = computed(() => {
    const start = (currentPage.value - 1) * pageSize;
    const end = start + pageSize;
    return leaveRequests.value.slice(start, end);
});

const pagedFilteredNotifications = computed(() => {
    const start = (currentPage.value - 1) * pageSize;
    const end = start + pageSize;
    return filteredNotifications.value.slice(start, end);
});

// New computed properties for notification summary
const unreadNotificationCount = computed(() => {
    return notifications.value.filter((n) => !n.read).length;
});

const approvedNotificationCount = computed(() => {
    return notifications.value.filter((n) => n.type === "leave_approved").length;
});

const rejectedNotificationCount = computed(() => {
    return notifications.value.filter((n) => n.type === "leave_rejected").length;
});

const recentNotifications = computed(() => {
    return notifications.value
        .sort((a, b) => new Date(b.created_at) - new Date(a.created_at))
        .slice(0, 3);
});

// New computed property for filtered notifications
const filteredNotifications = computed(() => {
    switch (statusFilter.value) {
        case "approved":
            return notifications.value.filter((n) => n.type === "leave_approved");
        case "rejected":
            return notifications.value.filter((n) => n.type === "leave_rejected");
        case "pending":
            // Create pseudo-notifications from pending leave requests
            return leaveRequests.value
                .filter((r) => r.status === "pending")
                .map((r) => ({
                    id: `leave-${r.id}`,
                    type: "leave_pending",
                    message: `Leave request for ${r.leave_type} from ${r.from_date} to ${r.to_date} is awaiting approval.`,
                    created_at: r.created_at,
                    read: true, // Nothing to mark as read for pending requests
                }));
        default:
            return notifications.value;
    }
});

// New methods for status functionality
const setStatusFilter = (status) => {
    statusFilter.value = status; // Set the status filter
    currentPage.value = 1; // Reset to first page when filter changes
    currentStatus.value = status.charAt(0).toUpperCase() + status.slice(1); // Capitalize the first letter
    currentStatus.value =
        status === "all"
            ? "All Requests"
            : status.charAt(0).toUpperCase() + status.slice(1); // Capitalize the first letter or set to 'All Requests'
};

// New methods for notification functionality
const toggleNotificationSummary = () => {
    showNotificationSummary.value = !showNotificationSummary.value;
};

const scrollToNotifications = () => {
    showNotificationSummary.value = false;
    if (notificationsSection.value) {
        notificationsSection.value.scrollIntoView({
            behavior: "smooth",
            block: "start",
        });
    }
};

// New methods for notification detail functionality
const handleNotificationClick = async (notification) => {
    selectedNotification.value = notification;
    showNotificationDetail.value = true;
    showNotificationSummary.value = false;

    // Mark notification as read when clicked (this removes the count)
    if (!notification.read) {
        await markNotificationAsRead(notification.id);
    }
};

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
// Close notification summary when clicking outside
const handleClickOutside = (event) => {
    if (showNotificationSummary.value && !event.target.closest(".relative")) {
        showNotificationSummary.value = false;
    }

    // Close notification detail modal when clicking outside
    if (showNotificationDetail.value && !event.target.closest(".rounded-2xl")) {
        showNotificationDetail.value = false;
        selectedNotification.value = null;
    }
};
// ... existing code ... (all existing functions remain unchanged)
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
onMounted(() => {
    fetchUser();
    fetchNotifications();
    fetchLeaveRequests();

    // Add click outside listener
    document.addEventListener("click", handleClickOutside);
});
// Cleanup event listener
onUnmounted(() => {
    document.removeEventListener("click", handleClickOutside);
});
const cancelLeaveRequest = async (id) => {
    const result = await Swal.fire({
        title: "Confirm Cancellation",
        text: "Are you sure you want to cancel this leave request?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Yes, Cancel It",
        cancelButtonText: "No, Keep It",
        customClass: {
            confirmButton:
                "bg-red-400 hover:bg-red-400 text-white text-sm py-2 rounded mr-2",
            cancelButton:
                "bg-blue-400 hover:bg-blue-400 text-white text-sm py-2 rounded",
        },
        background: "#fff",
        backdrop: "rgba(0,0,0,0.4)",
    });
    if (!result.isConfirmed) return;
    try {
        const token = localStorage.getItem("authToken");
        await axios.delete(
            `http://127.0.0.1:8000/api/student/leave-request/${id}`,
            {
                headers: { Authorization: `Bearer ${token}` },
            }
        );
        await Swal.fire({
            icon: "success",
            title: "Leave Cancelled",
            text: "Your leave request has been cancelled.",
            iconColor: "#16a34a",
            confirmButtonText: "OK",
            customClass: {
                confirmButton:
                    "bg-green-400 hover:bg-green-400 text-white text-sm py-2 rounded",
            },
            background: "#fff",
        });
        await fetchLeaveRequests(); // Refresh the leave requests
    } catch (err) {
        await Swal.fire({
            icon: "error",
            title: "Error",
            text: `Failed to cancel leave: ${err.response?.data?.message || err.message
                }`,
            iconColor: "#dc2626",
            confirmButtonText: "OK",
            customClass: {
                confirmButton:
                    "bg-red-400 hover:bg-red-400 text-white text-sm py-2 rounded",
            },
            background: "#fff",
        });
        if (err.response?.status === 401) router.push("/login");
        console.error("Cancel leave error:", err);
    }
};
</script>
