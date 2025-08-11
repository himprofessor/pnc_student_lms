<template>
  <div class="container mx-auto max-w-7xl p-4">
    <!-- Notification -->
    <div v-if="eventAdded" class="mb-4 rounded-lg bg-emerald-100 p-3 text-sm text-emerald-800 shadow-sm transition-all duration-300">
      Event Added
    </div>
    
    <!-- Action Bar -->
    <div class="mb-6 flex justify-between">
      <button
        class="flex items-center space-x-2 rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-medium text-white shadow-sm transition-all hover:bg-blue-700 focus:ring-4 focus:ring-blue-200"
        @click="$router.push('/request-leave')"
      >
        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
        </svg>
        <span>New Leave Request</span>
      </button>
    </div>

    <!-- Calendar Header -->
    <div class="mb-4 flex items-center justify-between rounded-xl bg-gradient-to-r from-blue-700 to-blue-800 p-4 text-white shadow-md">
      <div class="flex items-center space-x-2">
        <button @click="prevMonth" class="rounded-lg p-2 hover:bg-blue-600/50">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
          </svg>
        </button>
        <button @click="goToday" class="rounded-lg bg-white/90 px-3 py-1.5 text-sm font-medium text-blue-800 shadow-sm hover:bg-white">
          Today
        </button>
        <button @click="nextMonth" class="rounded-lg p-2 hover:bg-blue-600/50">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
          </svg>
        </button>
      </div>
      
      <h2 class="text-xl font-semibold">{{ monthName }} {{ year }}</h2>
      
      <div class="flex space-x-2">
        <button class="rounded-lg bg-white/90 px-3 py-1.5 text-sm font-medium text-blue-800 shadow-sm hover:bg-white">
          Month
        </button>
        <button class="rounded-lg bg-white/20 px-3 py-1.5 text-sm font-medium text-white shadow-sm hover:bg-white/30">
          Week
        </button>
        <button class="rounded-lg bg-white/20 px-3 py-1.5 text-sm font-medium text-white shadow-sm hover:bg-white/30">
          Day
        </button>
      </div>
    </div>

    <!-- Calendar Grid -->
    <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
      <div class="grid grid-cols-7 gap-px border-b border-gray-200 bg-gray-100">
        <div v-for="day in daysOfWeek" :key="day" class="py-3 text-center text-sm font-semibold text-gray-600">
          {{ day }}
        </div>
      </div>
      <div class="grid grid-cols-7 gap-px bg-gray-100">
        <div 
          v-for="day in calendarDays" 
          :key="day.date" 
          class="relative min-h-[100px] bg-white p-2 transition-colors hover:bg-gray-50"
          :class="{ 
            'bg-gray-50 text-gray-400': !day.isCurrentMonth,
            'bg-red-50': isAbsent(day.date)
          }"
          @click="showAbsenceDetails(day.date)" 
          role="button" 
          tabindex="0" 
          @keydown.enter.prevent="showAbsenceDetails(day.date)"
        >
          <div class="flex items-center justify-between">
            <span class="text-sm font-medium" :class="{ 'text-gray-900': day.isCurrentMonth, 'text-gray-400': !day.isCurrentMonth }">
              {{ day.day }}
            </span>
            <span v-if="day.day === new Date().getDate() && day.isCurrentMonth && currentDate.getMonth() === new Date().getMonth()" 
                  class="flex h-6 w-6 items-center justify-center rounded-full bg-blue-600 text-xs font-medium text-white">
              {{ day.day }}
            </span>
          </div>
          <div v-if="day.events.length" class="mt-1 space-y-1">
            <div 
              v-for="(event, index) in day.events" 
              :key="index" 
              class="truncate rounded px-1.5 py-1 text-xs font-medium"
              :class="{
                'bg-red-100 text-red-800': event.type === 'absence'
              }"
            >
              {{ event.title }}
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Absence Details Modal -->
    <div 
      v-if="selectedAbsence" 
      class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4 backdrop-blur-sm"
      @click.self="selectedAbsence = null"
    >
      <div class="w-full max-w-md rounded-2xl bg-white p-6 shadow-xl">
        <div class="mb-4 flex items-center justify-between">
          <h3 class="text-lg font-semibold text-gray-900">Absence Details</h3>
          <button 
            @click="selectedAbsence = null" 
            class="text-gray-400 hover:text-gray-500 focus:outline-none"
            aria-label="Close modal"
          >
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>
        
        <div class="space-y-4 text-gray-700">
          <div class="flex items-start">
            <div class="w-24 shrink-0 text-sm font-medium text-gray-500">Date:</div>
            <div class="text-sm">{{ formatFullDate(selectedAbsence.date) }}</div>
          </div>
          <div class="flex items-start">
            <div class="w-24 shrink-0 text-sm font-medium text-gray-500">Reason:</div>
            <div class="text-sm">{{ selectedAbsence.reason || 'Not specified' }}</div>
          </div>
          <div v-if="selectedAbsence.notes" class="flex items-start">
            <div class="w-24 shrink-0 text-sm font-medium text-gray-500">Notes:</div>
            <div class="text-sm">{{ selectedAbsence.notes }}</div>
          </div>
          <div class="flex items-start">
            <div class="w-24 shrink-0 text-sm font-medium text-gray-500">Period:</div>
            <div class="text-sm">{{ selectedAbsence.from_date }} to {{ selectedAbsence.to_date }}</div>
          </div>
          <div class="flex items-start">
            <div class="w-24 shrink-0 text-sm font-medium text-gray-500">Status:</div>
            <div class="text-sm">
              <span 
                class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium"
                :class="{
                  'bg-green-100 text-green-800': selectedAbsence.status === 'approved',
                  'bg-yellow-100 text-yellow-800': selectedAbsence.status === 'pending',
                  'bg-red-100 text-red-800': selectedAbsence.status === 'rejected'
                }"
              >
                {{ selectedAbsence.status }}
              </span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch, onMounted } from 'vue'

const currentDate = ref(new Date('2025-08-11T08:46:00+07:00'))
const eventAdded = ref(false)
const absences = ref([])
const loading = ref(false)
const error = ref(null)
const selectedAbsence = ref(null)

const daysOfWeek = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat']
const monthNames = [
  'January', 'February', 'March', 'April', 'May', 'June',
  'July', 'August', 'September', 'October', 'November', 'December'
]

const monthName = computed(() => monthNames[currentDate.value.getMonth()])
const year = computed(() => currentDate.value.getFullYear())

const calendarDays = computed(() => {
  const days = []
  const firstDayOfMonth = new Date(currentDate.value.getFullYear(), currentDate.value.getMonth(), 1)
  const lastDayOfMonth = new Date(currentDate.value.getFullYear(), currentDate.value.getMonth() + 1, 0)
  const startDay = firstDayOfMonth.getDay()
  const totalDays = lastDayOfMonth.getDate()

  // Previous month padding
  const prevMonthLastDay = new Date(currentDate.value.getFullYear(), currentDate.value.getMonth(), 0).getDate()
  for (let i = startDay - 1; i >= 0; i--) {
    const date = new Date(currentDate.value.getFullYear(), currentDate.value.getMonth() - 1, prevMonthLastDay - i)
    days.push({ day: prevMonthLastDay - i, date: date.toISOString().split('T')[0], isCurrentMonth: false, events: [] })
  }

  // Current month days
  for (let i = 1; i <= totalDays; i++) {
    const date = new Date(currentDate.value.getFullYear(), currentDate.value.getMonth(), i)
    days.push({ day: i, date: date.toISOString().split('T')[0], isCurrentMonth: true, events: getEventsForDate(date) })
  }

  // Next month padding
  const remainingDays = (7 - (days.length % 7)) % 7
  for (let i = 1; i <= remainingDays; i++) {
    const date = new Date(currentDate.value.getFullYear(), currentDate.value.getMonth() + 1, i)
    days.push({ day: i, date: date.toISOString().split('T')[0], isCurrentMonth: false, events: [] })
  }

  return days
})

const isAbsent = (date) => {
  return absences.value.some(absence => date >= absence.from_date && date <= absence.to_date)
}

const getEventsForDate = (date) => {
  const absence = absences.value.find(absence => date >= absence.from_date && date <= absence.to_date)
  return absence ? [{ title: absence.reason || 'Absent', type: 'absence' }] : []
}

const formatFullDate = (dateString) => {
  const date = new Date(dateString)
  return date.toLocaleDateString('en-US', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' })
}

const showAbsenceDetails = (date) => {
  selectedAbsence.value = {
    ...absences.value.find(absence => date >= absence.from_date && date <= absence.to_date),
    date: date
  }
}

const fetchAbsences = async () => {
  loading.value = true
  error.value = null
  absences.value = []

  const apiDate = new Date(currentDate.value.getFullYear(), currentDate.value.getMonth(), 1).toISOString().split('T')[0]
  try {
    const res = await fetch(`http://127.0.0.1:8000/api/student/my-leaves?month=${apiDate}`, {
      headers: {
        Authorization: `Bearer 259|XCTwKBUin65WkH6OtoVzUHnWu7sbPrhT7yhFRkFn115ec94c`,
        Accept: 'application/json'
      }
    })
    if (!res.ok) throw new Error(`API error: ${res.status}`)
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
  } finally {
    loading.value = false
  }
}

const prevMonth = () => {
  currentDate.value.setMonth(currentDate.value.getMonth() - 1)
}

const nextMonth = () => {
  currentDate.value.setMonth(currentDate.value.getMonth() + 1)
}

const goToday = () => {
  currentDate.value = new Date('2025-08-11T08:46:00+07:00')
}

const addEvent = () => {
  eventAdded.value = true
  setTimeout(() => eventAdded.value = false, 2000)
}

onMounted(fetchAbsences)
watch([() => currentDate.value.getMonth(), () => currentDate.value.getFullYear()], fetchAbsences)
</script>

<style scoped>
/* Smooth transitions for interactive elements */
button, [role="button"] {
  transition: all 0.2s ease;
}

/* Better focus states for accessibility */
button:focus-visible, [role="button"]:focus-visible {
  outline: 2px solid #3b82f6;
  outline-offset: 2px;
}

/* Calendar day hover effect */
.grid-cols-7 > div:hover {
  @apply shadow-[inset_0_0_0_1px_rgba(59,130,246,0.5)];
}
</style>