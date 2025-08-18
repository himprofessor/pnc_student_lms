<template>
  <div class="container mx-auto max-w-7xl px-4 py-8">
    <div class="flex flex-col md:flex-row justify-between items-center mb-8 gap-4">
      <div>
        <h1 class="text-2xl md:text-3xl font-bold text-gray-900 dark:text-white">Attendance Calendar</h1>
        <p class="text-gray-600 dark:text-gray-400 mt-1">Track your leaves and attendance</p>
      </div>

      <button
        class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium flex items-center space-x-1 transition-colors duration-200"
        @click="$router.push('/request-leave')">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
        </svg>
        <span>New Leave Request</span>
      </button>
    </div>

    <!-- Month Navigation -->
    <div
      class="flex items-center justify-between mb-6 bg-white dark:bg-gray-800 p-4 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700">
      <button @click="prevMonth" aria-label="Previous month"
        class="text-gray-500 hover:text-indigo-600 dark:hover:text-indigo-400 hover:bg-indigo-50 dark:hover:bg-gray-700 focus:outline-none p-2 rounded-full transition-colors duration-200">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"
          stroke-linecap="round" stroke-linejoin="round">
          <polyline points="15 18 9 12 15 6"></polyline>
        </svg>
      </button>

      <h2 class="text-xl font-semibold text-gray-800 dark:text-white">{{ monthName }} {{ year }}</h2>

      <button @click="nextMonth" aria-label="Next month"
        class="text-gray-500 hover:text-indigo-600 dark:hover:text-indigo-400 hover:bg-indigo-50 dark:hover:bg-gray-700 focus:outline-none p-2 rounded-full transition-colors duration-200">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"
          stroke-linecap="round" stroke-linejoin="round">
          <polyline points="9 18 15 12 9 6"></polyline>
        </svg>
      </button>
    </div>

    <!-- Loading -->
    <div v-if="loading" class="text-center py-8">
      <div class="inline-flex items-center gap-2 text-gray-500 dark:text-gray-400">
        <svg class="animate-spin h-5 w-5 text-indigo-600 dark:text-indigo-400" xmlns="http://www.w3.org/2000/svg"
          fill="none" viewBox="0 0 24 24">
          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
          <path class="opacity-75" fill="currentColor"
            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
          </path>
        </svg>
        Loading attendance data...
      </div>
    </div>

    <!-- Weekday headers - Sunday to Saturday -->
    <div class="grid grid-cols-7 gap-px mb-2 bg-gray-100 dark:bg-gray-700 rounded-t-lg overflow-hidden">
      <div v-for="(day, index) in daysOfWeek" :key="index" :class="[
        'py-3 text-center text-sm font-medium',
        (index === 0 || index === 6)
          ? 'text-red-500 dark:text-red-400'
          : 'text-gray-600 dark:text-gray-300'
      ]">
        {{ day }}
      </div>
    </div>


    <!-- Calendar grid - Sunday to Saturday (7 columns) -->
    <div class="grid grid-cols-7 gap-px bg-gray-100 dark:bg-gray-700 rounded-b-lg overflow-hidden shadow-sm">
      <template v-for="day in calendarDays" :key="day.date">
        <div :class="[
          'min-h-[100px] p-2 flex flex-col transition-colors duration-150',
          day.isCurrentMonth ? 'text-gray-900 dark:text-gray-100' : 'text-gray-400 dark:text-gray-500',
          isToday(day.date) ? 'ring-2 ring-indigo-500 dark:ring-indigo-400' : '',
          getAbsenceDetails(day.date) ? getStatusBg(getAbsenceDetails(day.date).status) : 'bg-white dark:bg-gray-800',
          isWeekend(day.date) ? 'bg-gray-50 dark:bg-gray-700 text-red-500 dark:text-red-400' : '',
          'hover:bg-gray-50 dark:hover:bg-gray-700/50'
        ]" @click="handleDayClick(day.date)" role="button" tabindex="0"
          @keydown.enter.prevent="handleDayClick(day.date)">
          <div class="flex justify-between items-start mb-1">
            <span class="text-sm font-medium">{{ day.day }}</span>
            <span v-if="getAbsenceDetails(day.date)" class="w-2 h-2 rounded-full mt-1.5"
              :class="getStatusDot(getAbsenceDetails(day.date).status)" :title="getAbsenceDetails(day.date).status">
            </span>
          </div>

          <!-- Display leave type with status-based colors and bold text -->
          <div v-if="getAbsenceDetails(day.date)" class="mt-auto">
            <p class="text-xs rounded px-1.5 py-1 truncate text-left font-bold"
              :class="getStatusTextBg(getAbsenceDetails(day.date).status)"
              :title="typeof getAbsenceDetails(day.date).leave_type === 'object' ? getAbsenceDetails(day.date).leave_type.name : getAbsenceDetails(day.date).leave_type || 'Unknown'">
              {{ typeof getAbsenceDetails(day.date).leave_type === 'object' ?
                getAbsenceDetails(day.date).leave_type.name : getAbsenceDetails(day.date).leave_type || 'Unknown' }}
            </p>
          </div>
        </div>
      </template>
    </div>

    <!-- Modern Absence Details Modal -->
    <div v-if="selectedAbsence"
      class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm p-4"
      @click.self="selectedAbsence = null">
      <div
        class="relative w-full max-w-2xl overflow-hidden rounded-3xl bg-white dark:bg-gray-800 shadow-2xl animate-modal-in max-h-[90vh] flex flex-col border border-gray-200/50 dark:border-gray-700/50">
        <!-- Header with gradient and improved close button -->
        <div class="bg-blue-500 px-8 py-6 flex items-center justify-between">
          <div class="flex items-center space-x-3">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-white/90" fill="none" viewBox="0 0 24 24"
              stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
            </svg>
            <h2 class="text-2xl font-bold text-white tracking-tight">
              Absence Details
            </h2>
          </div>
        </div>

        <!-- Content area with improved spacing and cards -->
        <div class="flex-1 overflow-y-auto p-8 space-y-8">
          <!-- Status and Leave Type in card -->
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="bg-white p-5 rounded-xl border border-gray-100 shadow-sm">
              <label class="label-text text-gray-600">Status</label>
              <div class="mt-2">
                <span
                  class="inline-flex items-center px-3 py-1.5 rounded-full text-sm font-bold capitalize transform transition-all duration-200"
                  :class="{
                    'bg-yellow-100 text-yellow-800': selectedAbsence.status === 'pending',
                    'bg-green-100 text-green-800': selectedAbsence.status === 'approved',
                    'bg-red-100 text-red-800': selectedAbsence.status === 'rejected',
                  }">
                  <span class="w-2 h-2 rounded-full mr-2" :class="{
                    'bg-yellow-600': selectedAbsence.status === 'pending',
                    'bg-green-600': selectedAbsence.status === 'approved',
                    'bg-red-600': selectedAbsence.status === 'rejected',
                  }"></span>
                  {{ selectedAbsence.status }}
                </span>
              </div>
            </div>
            <div
              class="bg-gradient-to-br from-indigo-50 to-purple-50 p-5 rounded-xl border border-indigo-100 shadow-sm">
              <label class="label-text text-indigo-800/80">Leave Type</label>
              <div class="flex items-center gap-3 data-text mt-1">
                <span class="w-3 h-3 rounded-full flex-shrink-0" :class="{
                  'bg-blue-500 animate-ping-slow': selectedAbsence.status === 'pending',
                  'bg-green-500': selectedAbsence.status === 'approved',
                  'bg-red-500': selectedAbsence.status === 'rejected',
                }"></span>
                <span class="text-gray-900 font-medium">
                  {{
                    typeof selectedAbsence.leave_type === "object"
                      ? selectedAbsence.leave_type.name
                      : selectedAbsence.leave_type || "Unknown"
                  }}
                </span>
              </div>
            </div>
          </div>

          <!-- Date information in timeline style -->
          <div class="bg-gray-50 p-6 rounded-xl border border-gray-100 shadow-sm">
            <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-gray-500" fill="none" viewBox="0 0 24 24"
                stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
              </svg>
              Leave Period
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
              <div class="space-y-1">
                <label class="label-text text-gray-600">Start Date</label>
                <div class="data-text font-medium text-gray-900">
                  {{ formatDate(selectedAbsence.from_date) }}
                </div>
              </div>
              <div class="space-y-1">
                <label class="label-text text-gray-600">End Date</label>
                <div class="data-text font-medium text-gray-900">
                  {{ formatDate(selectedAbsence.to_date) }}
                </div>
              </div>
              <div class="space-y-1">
                <label class="label-text text-gray-600">Submitted On</label>
                <div class="data-text mt-1 font-medium text-gray-900">
                  {{ formatDate(selectedAbsence.created_at) }}
                </div>
              </div>
            </div>
          </div>

          <!-- Reason with improved styling -->
          <div class="bg-gray-50 p-6 rounded-xl border border-gray-100 shadow-sm">
            <h3 class="text-lg font-semibold text-gray-800 mb-3 flex items-center">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-gray-500" fill="none" viewBox="0 0 24 24"
                stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z" />
              </svg>
              Reason for Leave
            </h3>
            <div class="data-text-reason bg-white p-4 rounded-lg border border-gray-200 text-gray-700">
              {{ selectedAbsence.reason || "No reason provided" }}
            </div>
          </div>
        </div>

        <!-- Footer with improved button -->
        <div
          class="sticky bottom-0 bg-gradient-to-r from-blue-50 to-indigo-50 px-8 py-5 border-t border-gray-100 flex justify-end space-x-4">
          <button @click="selectedAbsence = null"
            class="rounded-xl bg-blue-600 px-8 py-3 font-bold text-white shadow-lg hover:from-blue-700 hover:to-indigo-700 transition-all duration-200 transform hover:scale-[1.02] active:scale-95 flex items-center">
            Close
          </button>
        </div>
      </div>
    </div>

    <!-- Error message -->
    <div v-if="error" class="mt-6 p-4 bg-red-50 dark:bg-red-900/20 rounded-lg border border-red-900/30">
      <div class="flex items-center gap-3 text-red-600 dark:text-red-400">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
          <path fill-rule="evenodd"
            d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
            clip-rule="evenodd" />
        </svg>
        <p class="font-medium">{{ error }}</p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch, onMounted } from 'vue'
import { useRouter } from 'vue-router'

const router = useRouter()

const absences = ref([])
const loading = ref(false)
const error = ref(null)
const selectedAbsence = ref(null)

// Calendar state - Initialize to current month
const currentDate = ref(new Date())
const year = ref(currentDate.value.getFullYear())
const month = ref(currentDate.value.getMonth())

// Sunday to Saturday
const daysOfWeek = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat']
const monthNames = [
  'January', 'February', 'March', 'April', 'May', 'June',
  'July', 'August', 'September', 'October', 'November', 'December'
]

const monthName = computed(() => monthNames[month.value])

const calendarDays = computed(() => {
  const days = []
  const firstDayOfMonth = new Date(year.value, month.value, 1)
  const lastDayOfMonth = new Date(year.value, month.value + 1, 0)
  const startDay = firstDayOfMonth.getDay() // Starts with Sunday (0)
  const totalDays = lastDayOfMonth.getDate()

  // Adjust for Sunday start (0)
  const daysFromPrevMonth = startDay

  const prevMonthLastDay = new Date(year.value, month.value, 0).getDate()
  for (let i = daysFromPrevMonth - 1; i >= 0; i--) {
    const date = new Date(year.value, month.value - 1, prevMonthLastDay - i)
    days.push({
      day: prevMonthLastDay - i,
      date: formatYMD(date),
      isCurrentMonth: false
    })
  }

  for (let i = 1; i <= totalDays; i++) {
    const date = new Date(year.value, month.value, i)
    days.push({
      day: i,
      date: formatYMD(date),
      isCurrentMonth: true
    })
  }


  const isWeekendHeader = (i, number) => {
    // adjust depending on whether your week starts Sunday (0) or Monday (1)
    return i === 0 || i === 6
  }



  // Adjust remaining days to maintain 7 columns (Sunday-Saturday)
  const remainingDays = (7 - (days.length % 7)) % 7
  for (let i = 1; i <= remainingDays; i++) {
    const date = new Date(year.value, month.value + 1, i)
    days.push({
      day: i,
      date: formatYMD(date),
      isCurrentMonth: false
    })
  }

  return days
})

function formatYMD(date) {
  const y = date.getFullYear()
  const m = String(date.getMonth() + 1).padStart(2, '0')
  const d = String(date.getDate()).padStart(2, '0')
  return `${y}-${m}-${d}`
}

function formatDate(dateStr) {
  if (!dateStr) return "N/A"
  const d = new Date(dateStr)
  return isNaN(d)
    ? dateStr
    : d.toLocaleDateString(undefined, {
      year: "numeric",
      month: "short",
      day: "numeric",
    })
}

const isAbsent = (date) => {
  return absences.value.some(absence =>
    date >= absence.from_date && date <= absence.to_date
  )
}

const isWeekend = (dateString) => {
  const date = new Date(dateString)
  const day = date.getDay()
  return day === 0 || day === 6 // Sunday (0) or Saturday (6)
}

const getAbsenceDetails = (date) => {
  return absences.value.find(absence =>
    date >= absence.from_date && date <= absence.to_date
  )
}

const isToday = (date) => {
  const today = formatYMD(new Date())
  return date === today
}

const formatFullDate = (dateString) => {
  const [y, m, d] = dateString.split('-').map(Number)
  const date = new Date(y, m - 1, d)
  return date.toLocaleDateString('en-US', {
    weekday: 'long',
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  })
}

const prevMonth = () => {
  month.value--
  if (month.value < 0) {
    month.value = 11
    year.value--
  }
}

const nextMonth = () => {
  month.value++
  if (month.value > 11) {
    month.value = 0
    year.value++
  }
}

const handleDayClick = (date) => {
  const details = getAbsenceDetails(date)
  if (details) {
    selectedAbsence.value = { ...details, date }
  } else {
    router.push({ path: '/request-leave', query: { date } })
  }
}

const getStatusBg = (status) => {
  switch (status.toLowerCase()) {
    case 'approved': return 'bg-green-50 dark:bg-green-900/20'
    case 'pending': return 'bg-yellow-50 dark:bg-yellow-900/20'
    case 'rejected': return 'bg-red-50 dark:bg-red-900/20'
    default: return 'bg-gray-50 dark:bg-gray-700'
  }
}

const getStatusDot = (status) => {
  switch (status.toLowerCase()) {
    case 'approved': return 'bg-green-500 dark:bg-green-400'
    case 'pending': return 'bg-yellow-500 dark:bg-yellow-400'
    case 'rejected': return 'bg-red-500 dark:bg-red-400'
    default: return 'bg-gray-400 dark:bg-gray-500'
  }
}

const getStatusTextBg = (status) => {
  switch (status.toLowerCase()) {
    case 'approved': return 'text-green-500 dark:text-green-400 bg-green-100 dark:bg-green-900/30'
    case 'pending': return 'text-yellow-500 dark:text-yellow-400 bg-yellow-100 dark:bg-yellow-900/30'
    case 'rejected': return 'text-red-500 dark:text-red-400 bg-red-100 dark:bg-red-900/30'
    default: return 'text-gray-500 dark:text-gray-400 bg-gray-100 dark:bg-gray-700'
  }
}

const fetchAbsences = async () => {
  loading.value = true
  error.value = null
  absences.value = []

  const apiDate = formatYMD(new Date(year.value, month.value, 1))
  try {
    const res = await fetch(`http://127.0.0.1:8000/api/student/my-leaves?month=${apiDate}`, {
      headers: {
        Authorization: `Bearer ${localStorage.getItem('authToken')}`,
        Accept: 'application/json'
      }
    })
    if (!res.ok) throw new Error(`Failed to load data: ${res.status}`)
    const data = await res.json()

    if (Array.isArray(data.leaves)) {
      absences.value = data.leaves.map(leave => ({
        ...leave,
        from_date: leave.from_date.split('T')[0],
        to_date: leave.to_date.split('T')[0]
      }))
    } else {
      throw new Error('Invalid data format from API')
    }
  } catch (err) {
    error.value = err.message
    console.error('Error fetching absences:', err)
  } finally {
    loading.value = false
  }
}

onMounted(fetchAbsences)
watch([month, year], fetchAbsences)
</script>

<style>
.animate-modal-in {
  animation: modalIn 0.3s ease-out forwards;
}

@keyframes modalIn {
  from {
    opacity: 0;
    transform: scale(0.95) translateY(-20px);
  }

  to {
    opacity: 1;
    transform: scale(1) translateY(0);
  }
}

.animate-ping-slow {
  animation: ping-slow 2s cubic-bezier(0, 0, 0.2, 1) infinite;
}

@keyframes ping-slow {

  75%,
  100% {
    transform: scale(1.5);
    opacity: 0;
  }
}

/* Smooth transitions for dark mode */
body {
  @apply transition-colors duration-200;
}
</style>