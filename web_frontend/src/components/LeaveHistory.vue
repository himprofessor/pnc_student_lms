<template>
  <div>
    <div v-for="leave in leaveRequests" :key="leave.id" class="leave-item p-4 border-b">
      <p>{{ leave.reason }}</p>
      <span :class="statusClass(leave.status)">
        {{ leave.status }}
      </span>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'

// state
const leaveRequests = ref([])

// fetch leave requests
const fetchLeaveRequests = async () => {
  try {
    const res = await axios.get('/api/leave-requests')
    leaveRequests.value = res.data
  } catch (error) {
    console.error('Error fetching leave requests', error)
  }
}

// get color class for status
const statusClass = (status) => {
  switch (status) {
    case 'pending':
      return 'text-blue-500 font-medium'
    case 'approved':
      return 'text-green-600 font-medium'
    case 'rejected':
      return 'text-red-500 font-medium'
    case 'cancelled':
      return 'text-gray-400 font-medium'
    default:
      return ''
  }
}

// fetch when component mounts
onMounted(() => {
  fetchLeaveRequests()
  setInterval(fetchLeaveRequests, 10000) // refresh every 10 sec
})
</script>

<style scoped>
.leave-item {
  display: flex;
  justify-content: space-between;
  align-items: center;
}
</style>
