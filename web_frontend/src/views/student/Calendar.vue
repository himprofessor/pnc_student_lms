<template>
  <div class="container mx-auto max-w-7xl px-4 py-8">
    <div class="flex flex-col md:flex-row justify-between items-center mb-8 gap-4">
      <div>
        <h1 class="text-2xl md:text-3xl font-bold text-gray-900">Attendance Calendar</h1>
        <p class="text-gray-600 mt-1">Track your leaves and attendance</p>
      </div>
      
      <button 
        @click="router.push('/request-leave')"
        class="flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg shadow-sm transition-colors duration-200"
      >
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
          <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
        </svg>
        Request Leave
      </button>
    </div>

    <!-- Month Navigation -->
    <div class="flex items-center justify-between mb-6 bg-white p-4 rounded-xl shadow-sm">
      <button @click="prevMonth" aria-label="Previous month"
        class="text-gray-500 hover:text-indigo-600 hover:bg-indigo-50 focus:outline-none p-2 rounded-full transition-colors duration-200">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"
          stroke-linecap="round" stroke-linejoin="round">
          <polyline points="15 18 9 12 15 6"></polyline>
        </svg>
      </button>

      <h2 class="text-xl font-semibold text-gray-800">{{ monthName }} {{ year }}</h2>

      <button @click="nextMonth" aria-label="Next month"
        class="text-gray-500 hover:text-indigo-600 hover:bg-indigo-50 focus:outline-none p-2 rounded-full transition-colors duration-200">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"
          stroke-linecap="round" stroke-linejoin="round">
          <polyline points="9 18 15 12 9 6"></polyline>
        </svg>
      </button>
    </div>

    <!-- Loading -->
    <div v-if="loading" class="text-center py-8">
      <div class="inline-flex items-center gap-2 text-gray-500">
        <svg class="animate-spin h-5 w-5 text-indigo-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
        Loading attendance data...
      </div>
    </div>

    <!-- Weekday headers - Monday to Saturday -->
    <div class="grid grid-cols-6 gap-px mb-2 bg-gray-100 rounded-t-lg overflow-hidden">
      <div v-for="day in daysOfWeek" :key="day" 
           class="py-3 text-center text-sm font-medium text-gray-600">
        {{ day }}
      </div>
    </div>

    <!-- Calendar grid - Monday to Saturday (6 columns) -->
    <div class="grid grid-cols-6 gap-px bg-gray-100 rounded-b-lg overflow-hidden shadow-sm">
      <template v-for="day in calendarDays" :key="day.date">
        <!-- Skip Sunday (day.date's day of week is 0) -->
        <div v-if="new Date(day.date).getDay() !== 0"
             :class="[
               'min-h-[100px] p-2 flex flex-col bg-white hover:bg-gray-50 transition-colors duration-150',
               day.isCurrentMonth ? 'text-gray-900' : 'text-gray-400',
               isToday(day.date) ? 'ring-2 ring-indigo-500' : '',
               getAbsenceDetails(day.date) ? getStatusBg(getAbsenceDetails(day.date).status) : 'bg-white',
               new Date(day.date).getDay() === 6 ? 'bg-gray-50' : ''
             ]"
             @click="handleDayClick(day.date)" 
             role="button" 
             tabindex="0" 
             @keydown.enter.prevent="handleDayClick(day.date)">
          <div class="flex justify-between items-start mb-1">
            <span class="text-sm font-medium">{{ day.day }}</span>
            <span v-if="getAbsenceDetails(day.date)" 
                  class="w-2 h-2 rounded-full mt-1.5"
                  :class="getStatusDot(getAbsenceDetails(day.date).status)" 
                  :title="getAbsenceDetails(day.date).status">
            </span>
          </div>

          <!-- Absence reason badge -->
          <div v-if="getAbsenceDetails(day.date)" class="mt-auto">
            <p class="text-xs rounded px-1.5 py-1 truncate text-left" 
               :class="getStatusTextBg(getAbsenceDetails(day.date).status)"
               :title="getAbsenceDetails(day.date).reason || 'Absent'">
              {{ getAbsenceDetails(day.date).reason || 'Absent' }}
            </p>
          </div>
        </div>
      </template>
    </div>

    <!-- Absence Details Modal -->
    <div v-if="selectedAbsence" class="fixed inset-0 bg-black bg-opacity-30 flex items-center justify-center p-4 z-50 backdrop-blur-sm"
      @click.self="selectedAbsence = null">
      <div class="bg-white rounded-xl p-6 max-w-md w-full shadow-2xl animate-fade-in">
        <div class="flex justify-between items-center mb-4">
          <h3 class="text-lg font-semibold text-gray-900">Absence Details</h3>
          <button @click="selectedAbsence = null"
            class="text-gray-400 hover:text-gray-600 focus:outline-none transition-colors duration-200 p-1 rounded-full hover:bg-gray-100">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
              <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
            </svg>
          </button>
        </div>
        <div v-if="selectedAbsence" class="space-y-4 text-gray-700">
          <div class="flex items-start gap-3">
            <div class="p-2 rounded-lg" :class="getStatusBg(selectedAbsence.status)">
              <span class="w-3 h-3 block rounded-full" :class="getStatusDot(selectedAbsence.status)"></span>
            </div>
            <div>
              <p class="font-medium text-gray-900">{{ formatFullDate(selectedAbsence.date) }}</p>
              <p class="text-sm text-gray-500">{{ selectedAbsence.status }}</p>
            </div>
          </div>
          
          <div class="grid grid-cols-2 gap-4 pt-2 border-t border-gray-100">
            <div>
              <p class="text-xs text-gray-500 font-medium">Period</p>
              <p>{{ selectedAbsence.from_date }} to {{ selectedAbsence.to_date }}</p>
            </div>
            <div>
              <p class="text-xs text-gray-500 font-medium">Reason</p>
              <p>{{ selectedAbsence.reason || 'Not specified' }}</p>
            </div>
          </div>
          
          <div v-if="selectedAbsence.notes" class="pt-2 border-t border-gray-100">
            <p class="text-xs text-gray-500 font-medium">Notes</p>
            <p class="whitespace-pre-line">{{ selectedAbsence.notes }}</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Error message -->
    <div v-if="error" class="mt-6 p-4 bg-red-50 rounded-lg border border-red-100">
      <div class="flex items-center gap-3 text-red-600">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
          <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
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

// Monday to Saturday only
const daysOfWeek = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat']
const monthNames = [
  'January', 'February', 'March', 'April', 'May', 'June',
  'July', 'August', 'September', 'October', 'November', 'December'
]

const monthName = computed(() => monthNames[month.value])

const calendarDays = computed(() => {
  const days = []
  const firstDayOfMonth = new Date(year.value, month.value, 1)
  const lastDayOfMonth = new Date(year.value, month.value + 1, 0)
  const startDay = firstDayOfMonth.getDay()
  const totalDays = lastDayOfMonth.getDate()

  // Adjust for Monday start (1) instead of Sunday (0)
  const daysFromPrevMonth = startDay === 0 ? 6 : startDay - 1
  
  const prevMonthLastDay = new Date(year.value, month.value, 0).getDate()
  for (let i = daysFromPrevMonth; i > 0; i--) {
    const date = new Date(year.value, month.value - 1, prevMonthLastDay - i + 1)
    days.push({
      day: prevMonthLastDay - i + 1,
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

  // Adjust remaining days to maintain 6 columns (Monday-Saturday)
  const remainingDays = (6 - (days.length % 6)) % 6
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

const isAbsent = (date) => {
  return absences.value.some(absence =>
    date >= absence.from_date && date <= absence.to_date
  )
}

const isWeekend = (dateString) => {
  const date = new Date(dateString)
  const day = date.getDay()
  return day === 6 // Saturday only (Sunday is already hidden)
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
    case 'approved': return 'bg-green-50';
    case 'pending': return 'bg-yellow-50';
    case 'rejected': return 'bg-red-50';
    default: return 'bg-gray-50';
  }
}

const getStatusDot = (status) => {
  switch (status.toLowerCase()) {
    case 'approved': return 'bg-green-500';
    case 'pending': return 'bg-yellow-500';
    case 'rejected': return 'bg-red-500';
    default: return 'bg-gray-400';
  }
}

const getStatusTextBg = (status) => {
  switch (status.toLowerCase()) {
    case 'approved': return 'text-green-800 bg-green-100';
    case 'pending': return 'text-yellow-800 bg-yellow-100';
    case 'rejected': return 'text-red-800 bg-red-100';
    default: return 'text-gray-800 bg-gray-100';
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
.animate-fade-in {
  animation: fadeIn 0.2s ease-out;
}

@keyframes fadeIn {
  from { opacity: 0; transform: translateY(-10px); }
  to { opacity: 1; transform: translateY(0); }
}
</style>