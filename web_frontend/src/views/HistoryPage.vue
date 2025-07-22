<template>
  <div class="min-h-screen bg-gray-50 text-gray-800">
    <div class="px-6 py-6 max-w-4xl mx-auto">
      <!-- Header -->
      <div class="flex justify-between items-center mb-2">
        <div>
          <h1 class="text-2xl font-bold">Leave History</h1>
          <p class="text-sm text-gray-500">View all your previous leave requests and their status</p>
        </div>
        <button @click="goToNewLeaveRequest" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm font-medium flex items-center space-x-1">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
          </svg>
          <span>New Leave Request</span>
        </button>
      </div>

      <!-- Filters -->
      <div class="bg-white p-4 rounded-lg shadow border mb-6 flex flex-wrap items-center gap-4">
        <div class="relative flex-1">
          <input 
            v-model="searchQuery" 
            type="text" 
            placeholder="Search by reason or leave type..." 
            class="border border-gray-300 p-2 pl-10 rounded-md w-full focus:outline-none focus:ring-blue-500 focus:border-blue-500"
            @input="filterLeaveRequests"
          />
          <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
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
          <option v-else-if="leaveTypesError" disabled>{{ leaveTypesError }}</option>
          <option v-else-if="leaveTypes.length === 0" disabled>No leave types available</option>
          <option v-for="type in leaveTypes" :key="type.id" :value="type.id">{{ type.name }}</option>
        </select>

        <span class="text-gray-600 text-sm">{{ filteredLeaveRequests.length }} requests</span>
      </div>

      <!-- Leave List -->
      <div class="bg-white rounded-lg shadow border">
        <div class="border-b px-6 py-4">
          <h2 class="text-lg font-semibold">Leave Requests</h2>
        </div>

        <div v-if="loading" class="p-6 text-center text-gray-500">Loading leave history...</div>
        <div v-else-if="error" class="p-6 text-center text-red-600">Error: {{ error }}</div>
        <div v-else-if="filteredLeaveRequests.length === 0" class="p-6 text-center text-gray-500">No leave requests found.</div>

        <div v-else class="divide-y">
          <div v-for="request in filteredLeaveRequests" :key="request.id" class="flex justify-between items-center px-6 py-4">
            <div class="flex items-start space-x-3">
              <svg class="w-6 h-6 text-gray-400 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2" />
              </svg>
              <div>
                <h3 class="font-semibold">
                  {{ typeof request.leave_type === 'object' ? request.leave_type.name : request.leave_type || 'Unknown' }}
                </h3>
                <p class="text-sm text-gray-500">{{ formatDate(request.from_date) }} - {{ formatDate(request.to_date) }}</p>
                <p class="text-sm text-gray-500 mt-1">{{ request.reason }}</p>
                <p class="text-xs text-gray-400 mt-1">Submitted {{ formatDate(request.created_at) }}</p>
                <p v-if="request.status === 'approved'" class="text-xs text-gray-400">
                  Approved {{ formatDate(request.approved_at || request.updated_at) }}
                </p>
              </div>
            </div>
            <div class="flex items-center space-x-3">
              <span :class="{
                'bg-yellow-100 text-yellow-700': request.status === 'pending',
                'bg-green-100 text-green-700': request.status === 'approved',
                'bg-red-100 text-red-700': request.status === 'rejected'
              }" class="text-xs font-medium px-2 py-1 rounded capitalize">
                {{ request.status }}
              </span>
              <a href="#" @click.prevent="openDetails(request)" class="text-blue-600 text-sm hover:underline">View</a>

              <button v-if="request.status === 'pending'" @click="cancelLeave(request.id)" class="text-red-500 text-xs hover:underline">Cancel</button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
 <!-- View Leave Details Modal -->
<transition name="scale">
  <div v-if="showModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm px-4">
    <div class="bg-white/80 backdrop-blur-lg rounded-3xl shadow-2xl w-full max-w-lg p-8 relative animate-zoom overflow-y-auto max-h-[90vh] border border-gray-200">
      
      <!-- Close Button -->
      <button @click="showModal = false"
        class="absolute top-3 right-4 text-gray-500 hover:text-red-500 text-3xl font-bold transition">
        &times;
      </button>

      <!-- Header -->
      <h2 class="text-3xl font-extrabold text-center text-gray-800 mb-6">📝 Leave Details</h2>

      <!-- Leave Info -->
      <div class="space-y-4 text-gray-700 text-[16px] leading-6">
        <div class="flex items-center gap-2">
          <span class="text-lg">📋</span>
          <p><span class="font-semibold">Leave Type:</span>
            {{ typeof selectedLeave.leave_type === 'object' ? selectedLeave.leave_type.name : selectedLeave.leave_type }}
          </p>
        </div>

        <div class="flex items-center gap-2">
          <span class="text-lg">📆</span>
          <p><span class="font-semibold">From:</span> {{ formatDate(selectedLeave.from_date) }}</p>
        </div>

        <div class="flex items-center gap-2">
          <span class="text-lg">📆</span>
          <p><span class="font-semibold">To:</span> {{ formatDate(selectedLeave.to_date) }}</p>
        </div>

        <div class="flex items-center gap-2">
          <span class="text-lg">⚙️</span>
          <p><span class="font-semibold">Status:</span> 
            <span
              :class="{
                'bg-yellow-100 text-yellow-700': selectedLeave.status === 'pending',
                'bg-green-100 text-green-700': selectedLeave.status === 'approved',
                'bg-red-100 text-red-700': selectedLeave.status === 'rejected'
              }"
              class="px-2 py-1 rounded-lg text-sm font-semibold capitalize"
            >
              {{ selectedLeave.status }}
            </span>
          </p>
        </div>

        <div class="flex items-start gap-2">
          <span class="text-lg">🗒️</span>
          <p><span class="font-semibold">Reason:</span> {{ selectedLeave.reason }}</p>
        </div>

        <div class="flex items-center gap-2">
          <span class="text-lg">📅</span>
          <p><span class="font-semibold">Submitted:</span> {{ formatDate(selectedLeave.created_at) }}</p>
        </div>

        <div class="flex items-center gap-2" v-if="selectedLeave.status === 'approved'">
          <span class="text-lg">✅</span>
          <p><span class="font-semibold">Approved At:</span> {{ formatDate(selectedLeave.approved_at || selectedLeave.updated_at) }}</p>
        </div>
      </div>

      <!-- Footer -->
      <!-- <div class="mt-6 text-center">
        <button @click="showModal = false"
          class="bg-gradient-to-r from-red-700 to-indigo-500 text-white px-6 py-2 rounded-xl font-semibold hover:scale-105 transition">
          Close
        </button>
      </div> -->

    </div>
  </div>
</transition>


</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import { useRouter } from 'vue-router';

const router = useRouter();

const leaveRequests = ref([]);
const filteredLeaveRequests = ref([]);
const searchQuery = ref('');
const selectedStatus = ref('');
const selectedLeaveType = ref('');
const loading = ref(true);
const error = ref(null);

const leaveTypes = ref([]);
const loadingLeaveTypes = ref(true);
const leaveTypesError = ref(null);
const showModal = ref(false);
const selectedLeave = ref(null);

const openDetails = (leave) => {
  selectedLeave.value = leave;
  showModal.value = true;
};

const goToNewLeaveRequest = () => {
  router.push('/request-leave');
};

const formatDate = (dateStr) => {
  if (!dateStr) return 'N/A';
  const d = new Date(dateStr);
  return isNaN(d) ? dateStr : d.toLocaleDateString(undefined, { year: 'numeric', month: 'short', day: 'numeric' });
};

const fetchLeaveRequests = async () => {
  loading.value = true;
  error.value = null;
  try {
    const token = localStorage.getItem('authToken');
    const { data } = await axios.get('http://127.0.0.1:8000/api/student/my-leaves', {
      headers: { Authorization: `Bearer ${token}` }
    });
    leaveRequests.value = Array.isArray(data.leaves) ? data.leaves : [];
    filteredLeaveRequests.value = [...leaveRequests.value];
  } catch (err) {
    console.error(err);
    error.value = err.response?.data?.message || 'Could not load leave history.';
    if (err.response?.status === 401) router.push('/login');
  } finally {
    loading.value = false;
  }
};

const fetchLeaveTypes = async () => {
  loadingLeaveTypes.value = true;
  try {
    const token = localStorage.getItem('authToken');
    const { data } = await axios.get('http://127.0.0.1:8000/api/leave-types', {
      headers: { Authorization: `Bearer ${token}` }
    });
    leaveTypes.value = Array.isArray(data.data) ? data.data : [];
  } catch (err) {
    console.error(err);
    leaveTypesError.value = err.response?.data?.message || 'Could not load leave types.';
    if (err.response?.status === 401) router.push('/login');
  } finally {
    loadingLeaveTypes.value = false;
  }
};

const filterLeaveRequests = () => {
  filteredLeaveRequests.value = leaveRequests.value.filter(request => {
    const matchesSearch =
      !searchQuery.value ||
      (request.reason?.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
       (typeof request.leave_type === 'object' && request.leave_type?.name?.toLowerCase().includes(searchQuery.value.toLowerCase())) ||
       (typeof request.leave_type === 'string' && request.leave_type.toLowerCase().includes(searchQuery.value.toLowerCase())));

    const matchesStatus = !selectedStatus.value || request.status === selectedStatus.value;

    const matchesLeaveType =
      !selectedLeaveType.value ||
      (typeof request.leave_type === 'object' && request.leave_type?.id == selectedLeaveType.value) ||
      (typeof request.leave_type === 'string' && request.leave_type == selectedLeaveType.value);

    return matchesSearch && matchesStatus && matchesLeaveType;
  });
};

const cancelLeave = async (id) => {
  if (confirm('Are you sure you want to cancel this leave request?')) {
    try {
      const token = localStorage.getItem('authToken');
      await axios.delete(`http://127.0.0.1:8000/api/student/leave-request/${id}`, {
        headers: { Authorization: `Bearer ${token}` }
      });
      alert('Leave request cancelled successfully.');
      await fetchLeaveRequests();
      filterLeaveRequests();
    } catch (err) {
      alert('Failed to cancel leave: ' + (err.response?.data?.message || 'Please try again.'));
      if (err.response?.status === 401) router.push('/login');
    }
  }
};

onMounted(() => {
  fetchLeaveRequests();
  fetchLeaveTypes();
});
</script>
