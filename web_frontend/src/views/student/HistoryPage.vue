<template>
  <div class="min-h-screen bg-gray-50 text-gray-800">
    <div class="px-6 py-6 max-w-4xl mx-auto">
      <!-- Header -->
      <div class="flex justify-between items-center mb-2">
        <div>
          <h1 class="text-2xl font-bold text-blue-600">Leave History</h1>
          <p class="text-sm text-gray-500">
            View all your previous leave requests and their status
          </p>
        </div>
        <button @click="goToNewLeaveRequest"
          class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm font-medium flex items-center space-x-1">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
          </svg>
          <span>New Leave Request</span>
        </button>
      </div>

      <!-- Filters -->
      <div class="bg-white p-4 rounded-lg shadow border mb-6 flex flex-wrap items-center gap-4">
        <div class="relative flex-1">
          <input v-model="searchQuery" type="text" placeholder="Search by reason or leave type..."
            class="border border-gray-300 p-2 pl-10 rounded-md w-full focus:outline-none focus:ring-blue-500 focus:border-blue-500"
            @input="filterLeaveRequests" />
          <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
          </div>
        </div>

        <select v-model="selectedStatus" @change="filterLeaveRequests" class="border p-2 rounded-md">
          <option value="">All Statuses</option>
          <option value="pending">Pending</option>
          <option value="approved">Approved</option>
          <option value="rejected">Rejected</option>
        </select>

        <select v-model="selectedLeaveType" @change="filterLeaveRequests" class="border p-2 rounded-md">
          <option value="">All Types</option>
          <option v-if="loadingLeaveTypes">Loading types...</option>
          <option v-else-if="leaveTypesError" disabled>
            {{ leaveTypesError }}
          </option>
          <option v-else-if="leaveTypes.length === 0" disabled>
            No leave types available
          </option>
          <option v-for="type in leaveTypes" :key="type.id" :value="type.id">
            {{ type.name }}
          </option>
        </select>

        <span class="text-gray-600 text-sm">{{ filteredLeaveRequests.length }} requests</span>
      </div>

      <!-- Leave List -->
      <div class="bg-white rounded-lg shadow border">
        <div class="border-b px-6 py-4">
          <h2 class="text-lg font-semibold">Leave Requests</h2>
        </div>

        <div v-if="loading" class="p-6 text-center text-gray-500">
          Loading leave history...
        </div>
        <div v-else-if="error" class="p-6 text-center text-red-600">
          Error: {{ error }}
        </div>
        <div v-else-if="paginatedLeaveRequests.length === 0" class="p-6 text-center text-gray-500">
          No leave requests found.
        </div>

        <div v-else class="divide-y">
          <div v-for="request in paginatedLeaveRequests" :key="request.id"
            class="flex justify-between items-center px-6 py-4">
            <div class="flex items-start space-x-3">
              <svg class="w-6 h-6 text-gray-400 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2" />
              </svg>
              <div>
                <h3 class="font-semibold">
                  {{
                    typeof request.leave_type === "object"
                      ? request.leave_type.name
                      : request.leave_type || "Unknown"
                  }}
                </h3>
                <p class="text-sm text-gray-500">
                  {{ formatDate(request.from_date) }} -
                  {{ formatDate(request.to_date) }}
                </p>
                <p class="text-sm text-gray-500 mt-1">{{ request.reason }}</p>
                <p class="text-xs text-gray-400 mt-1">
                  Submitted {{ formatDate(request.created_at) }}
                </p>
              </div>
            </div>
            <div class="flex items-center gap-4">
              <!-- Status badge -->
              <span :class="{
                'bg-yellow-100 text-yellow-700': request.status === 'pending',
                'bg-green-100 text-green-700': request.status === 'approved',
                'bg-red-100 text-red-700': request.status === 'rejected',
              }" class="px-2 py-0.5 text-xs font-semibold rounded-full capitalize">
                {{ request.status }}
              </span>

              <!-- View button -->
              <button @click.prevent="openDetails(request)"
                class="text-blue-600 text-sm font-medium transition hover:bg-blue-100 hover:rounded-md py-1">
                View
              </button>
              <ConfirmDialog :visible="showConfirm" message="Are you sure you want to cancel this leave request?"
                @confirm="handleConfirm" />

              <!-- Cancel button -->
              <button v-if="request.status === 'pending'" @click="cancelLeave(request.id)"
                class="text-red-600 text-sm font-medium transition hover:bg-red-100 hover:rounded-md py-1">
                Cancel
              </button>
            </div>
          </div>
        </div>

        <!-- Pagination -->
        <div class="flex justify-center items-center px-6 py-4 border-t bg-gray-50">
          <button @click="prevPage" :disabled="currentPage === 1"
            class="px-3 py-1 text-gray-600 hover:text-gray-800 disabled:text-gray-400 disabled:cursor-not-allowed">
            Prev
          </button>
          <span class="mx-2 text-sm text-gray-600">
            Page {{ currentPage }} of {{ totalPages }}
          </span>
          <button @click="nextPage" :disabled="currentPage === totalPages"
            class="px-3 py-1 text-gray-600 hover:text-gray-800 disabled:text-gray-400 disabled:cursor-not-allowed">
            Next
          </button>
        </div>
      </div>
    </div>

    <transition name="scale">
      <div v-if="showModal"
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm p-4">
        <div
          class="relative w-full max-w-2xl overflow-hidden rounded-3xl bg-white shadow-2xl animate-fade-in-up max-h-[90vh] flex flex-col border border-white/20">
          <!-- Header with gradient and improved close button -->
          <div class="bg-blue-600 px-8 py-6 flex items-center justify-between">
            <div class="flex items-center space-x-3">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-white/90" fill="none" viewBox="0 0 24 24"
                stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
              </svg>
              <h2 class="text-2xl font-bold text-white tracking-tight">
                Leave Request Details
              </h2>
            </div>
            <button @click="showModal = false"
              class="p-1.5 rounded-full bg-white/10 hover:bg-white/20 transition-all duration-200 text-white/80 hover:text-white">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
              </svg>
            </button>
          </div>

          <!-- Content area with improved spacing and cards -->
          <div class="flex-1 overflow-y-auto p-8 space-y-8">
            <!-- Employee and Leave Type in card -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div class="bg-white p-5 rounded-xl border border-gray-100 shadow-sm">
                <label class="label-text text-gray-600">Status</label>
                <div class="mt-2">
                  <span
                    class="inline-flex items-center px-3 py-1.5 rounded-full text-sm font-bold capitalize transform transition-all duration-200"
                    :class="{
                      'bg-yellow-100 text-yellow-800': selectedLeave.status === 'pending',
                      'bg-green-100 text-green-800': selectedLeave.status === 'approved',
                      'bg-red-100 text-red-800': selectedLeave.status === 'rejected',
                    }">
                    <span class="w-2 h-2 rounded-full mr-2" :class="{
                      'bg-yellow-600': selectedLeave.status === 'pending',
                      'bg-green-600': selectedLeave.status === 'approved',
                      'bg-red-600': selectedLeave.status === 'rejected',
                    }"></span>
                    {{ selectedLeave.status }}
                  </span>
                </div>
              </div>
              <div
                class="bg-gradient-to-br from-indigo-50 to-purple-50 p-5 rounded-xl border border-indigo-100 shadow-sm">
                <label class="label-text text-indigo-800/80">Leave Type</label>
                <div class="flex items-center gap-3 data-text mt-1">
                  <span class="w-3 h-3 rounded-full flex-shrink-0" :class="{
                    'bg-blue-500 animate-ping-slow': selectedLeave.status === 'pending',
                    'bg-green-500': selectedLeave.status === 'approved',
                    'bg-red-500': selectedLeave.status === 'rejected',
                  }"></span>
                  <span class="text-gray-900 font-medium">
                    {{
                      typeof selectedLeave.leave_type === "object"
                        ? selectedLeave.leave_type.name
                        : selectedLeave.leave_type
                    }}
                  </span>
                </div>
              </div>
            </div>

            <!-- Date information in timeline style -->
            <div class="bg-gray-50 p-6 rounded-xl border border-gray-100 shadow-sm">
              <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-gray-500" fill="none"
                  viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                Leave Period
              </h3>
              <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="space-y-1">
                  <label class="label-text text-gray-600">Start Date</label>
                  <div class="data-text font-medium text-gray-900">
                    {{ formatDate(selectedLeave.from_date) }}
                  </div>
                </div>
                <div class="space-y-1">
                  <label class="label-text text-gray-600">End Date</label>
                  <div class="data-text font-medium text-gray-900">
                    {{ formatDate(selectedLeave.to_date) }}
                  </div>
                </div>
                <div class=" space-y-1">
                  <label class="label-text text-gray-600">Submitted On</label>
                  <div class="data-text mt-1 font-medium text-gray-900">
                    {{ formatDate(selectedLeave.created_at) }}
                  </div>
                </div>
              </div>
            </div>

            <!-- Status and submission info -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">


            </div>

            <!-- Reason with improved styling -->
            <div class="bg-gray-50 p-6 rounded-xl border border-gray-100 shadow-sm">
              <h3 class="text-lg font-semibold text-gray-800 mb-3 flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-gray-500" fill="none"
                  viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z" />
                </svg>
                Reason for Leave
              </h3>
              <div class="data-text-reason bg-white p-4 rounded-lg border border-gray-200 text-gray-700">
                {{ selectedLeave.reason || "No reason provided" }}
              </div>
            </div>
          </div>
        </div>
      </div>
    </transition>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from "vue";
import axios from "axios";
import { useRouter } from "vue-router";
import ConfirmDialog from "@/components/ConfirmDialog.vue";
import { useAlert } from "@/stores/useAlertStore";

const router = useRouter();

const leaveRequests = ref([]);
const filteredLeaveRequests = ref([]);
const searchQuery = ref("");
const selectedStatus = ref("");
const selectedLeaveType = ref("");
const loading = ref(true);
const error = ref(null);

const leaveTypes = ref([]);
const loadingLeaveTypes = ref(true);
const leaveTypesError = ref(null);
const showModal = ref(false);
const selectedLeave = ref(null);

// Pagination state
const currentPage = ref(1);
const itemsPerPage = 5;

// Computed property for paginated leave requests
const paginatedLeaveRequests = computed(() => {
  const start = (currentPage.value - 1) * itemsPerPage;
  const end = start + itemsPerPage;
  return filteredLeaveRequests.value.slice(start, end);
});

// Computed property for total pages
const totalPages = computed(() => {
  return Math.ceil(filteredLeaveRequests.value.length / itemsPerPage);
});

// Pagination navigation methods
const prevPage = () => {
  if (currentPage.value > 1) {
    currentPage.value--;
  }
};

const nextPage = () => {
  if (currentPage.value < totalPages.value) {
    currentPage.value++;
  }
};

const openDetails = (leave) => {
  selectedLeave.value = leave;
  showModal.value = true;
};

const goToNewLeaveRequest = () => {
  router.push("/request-leave");
};

const formatDate = (dateStr) => {
  if (!dateStr) return "N/A";
  const d = new Date(dateStr);
  return isNaN(d)
    ? dateStr
    : d.toLocaleDateString(undefined, {
      year: "numeric",
      month: "short",
      day: "numeric",
    });
};

const fetchLeaveRequests = async () => {
  loading.value = true;
  error.value = null;
  try {
    const token = localStorage.getItem("authToken");
    const { data } = await axios.get(
      "http://127.0.0.1:8000/api/student/my-leaves",
      {
        headers: { Authorization: `Bearer ${token}` },
      }
    );
    leaveRequests.value = Array.isArray(data.leaves) ? data.leaves : [];
    filteredLeaveRequests.value = [...leaveRequests.value];
    currentPage.value = 1; // Reset to first page when new data is fetched
  } catch (err) {
    console.error(err);
    error.value =
      err.response?.data?.message || "Could not load leave history.";
    if (err.response?.status === 401) router.push("/login");
  } finally {
    loading.value = false;
  }
};

const fetchLeaveTypes = async () => {
  loadingLeaveTypes.value = true;
  leaveTypesError.value = null;
  try {
    const token = localStorage.getItem("authToken");
    const { data } = await axios.get("http://127.0.0.1:8000/api/leave-types", {
      headers: { Authorization: `Bearer ${token}` },
    });
    leaveTypes.value = Array.isArray(data.data) ? data.data : [];
  } catch (err) {
    console.error(err);
    leaveTypesError.value =
      err.response?.data?.message || "Could not load leave types.";
    if (err.response?.status === 401) router.push("/login");
  } finally {
    loadingLeaveTypes.value = false;
  }
};

const filterLeaveRequests = () => {
  filteredLeaveRequests.value = leaveRequests.value.filter((request) => {
    const matchesSearch =
      !searchQuery.value ||
      request.reason?.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
      (typeof request.leave_type === "object" &&
        request.leave_type?.name
          ?.toLowerCase()
          .includes(searchQuery.value.toLowerCase())) ||
      (typeof request.leave_type === "string" &&
        request.leave_type
          .toLowerCase()
          .includes(searchQuery.value.toLowerCase()));

    const matchesStatus =
      !selectedStatus.value || request.status === selectedStatus.value;

    const matchesLeaveType =
      !selectedLeaveType.value ||
      (typeof request.leave_type === "object" &&
        request.leave_type?.id == selectedLeaveType.value) ||
      (typeof request.leave_type === "string" &&
        request.leave_type.toLowerCase() ===
        leaveTypes.value.find((t) => t.id == selectedLeaveType.value)?.name.toLowerCase());

    return matchesSearch && matchesStatus && matchesLeaveType;
  });
  currentPage.value = 1; // Reset to first page when filtering
};

const { showAlert } = useAlert();
const showConfirm = ref(false);
const leaveRequestId = ref(null);

const cancelLeave = (id) => {
  leaveRequestId.value = id;
  showConfirm.value = true;
};

const handleConfirm = async (confirmed) => {
  showConfirm.value = false;
  if (!confirmed) return;

  try {
    const token = localStorage.getItem("authToken");
    await axios.delete(
      `http://127.0.0.1:8000/api/student/leave-request/${leaveRequestId.value}`,
      {
        headers: { Authorization: `Bearer ${token}` },
      }
    );

    showAlert("success", "Success!", "Your leave request has been cancelled.");
    await fetchLeaveRequests();
  } catch (err) {
    if (err.response?.status === 401) router.push("/login");
    console.error("Cancel leave error:", err);
    showAlert("error", "Error", "Failed to cancel leave request.");
  }
};

onMounted(() => {
  fetchLeaveRequests();
  fetchLeaveTypes();
});
</script>
