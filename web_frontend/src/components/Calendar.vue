<template>
  <div class="container mx-auto max-w-7xl">
    <h1 class="text-3xl font-semibold mb-6 text-center text-gray-800">ATTENDENCE CALENDAR</h1>

    <!-- Month Navigation -->
    <div class="flex items-center justify-between mb-6">
      <button
        @click="prevMonth"
        aria-label="Previous month"
        class="text-gray-600 hover:text-gray-900 focus:outline-none box-content border border-gray-300 rounded p-1"
      >
        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round">
          <polyline points="15 18 9 12 15 6"></polyline>
        </svg>
      </button>

      <h2 class="text-xl font-medium text-gray-900">{{ monthName }} {{ year }}</h2>

      <button
        @click="nextMonth"
        aria-label="Next month"
        class="text-gray-600 hover:text-gray-900 focus:outline-none box-content border border-gray-300 rounded p-1"
      >
        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round">
          <polyline points="9 18 15 12 9 6"></polyline>
        </svg>
      </button>
    </div>

    <!-- Loading -->
    <div v-if="loading" class="text-center text-gray-500 py-6">
      Loading absences...
    </div>

    <!-- Weekday headers -->
    <div class="grid grid-cols-7 border-b border-gray-300 mb-2">
      <div
        v-for="day in daysOfWeek"
        :key="day"
        class="py-2 text-center text-xs font-medium text-gray-500 uppercase tracking-wider"
      >
        {{ day }}
      </div>
    </div>

    <!-- Calendar grid -->
    <div class="grid grid-cols-7 gap-px bg-gray-300 rounded-md overflow-hidden shadow-sm">
      <div
        v-for="day in calendarDays"
        :key="day.date"
        :class="[
          'bg-white min-h-[80px] p-2 flex flex-col',
          day.isCurrentMonth ? 'text-gray-900' : 'text-gray-400',
          isToday(day.date) ? 'border-2 border-blue-500 rounded-md' : '',
          isAbsent(day.date) ? 'bg-red-50' : '',
          isWeekend(day.date) ? 'text-red-600' : ''
        ]"
        @click="showAbsenceDetails(day.date)"
        role="button"
        tabindex="0"
        @keydown.enter.prevent="showAbsenceDetails(day.date)"
      >
        <div class="flex justify-between items-start mb-1">
          <span class="text-sm font-semibold">{{ day.day }}</span>
          <span
            v-if="isAbsent(day.date)"
            class="w-2 h-2 bg-red-500 rounded-full mt-1"
            aria-label="Absent"
            title="Absent"
          ></span>
        </div>

        <!-- Absence reason badge -->
        <div v-if="getAbsenceDetails(day.date)" class="flex-grow">
          <p
            class="text-xs text-red-700 bg-red-100 rounded px-1 py-0.5 truncate"
            :title="getAbsenceDetails(day.date).reason || 'Absent'"
          >
            {{ getAbsenceDetails(day.date).reason || 'Absent' }}
          </p>
        </div>
      </div>
    </div>

    <!-- Absence Details Modal -->
    <div
      v-if="selectedAbsence"
      class="fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center p-4 z-50"
      @click.self="selectedAbsence = null"
    >
      <div class="bg-white rounded-lg p-6 max-w-md w-full shadow-lg">
        <div class="flex justify-between items-center mb-4">
          <h3 class="text-lg font-semibold text-gray-900">Absence Details</h3>
          <button
            @click="selectedAbsence = null"
            class="text-gray-500 hover:text-gray-700 focus:outline-none text-2xl leading-none"
            aria-label="Close modal"
          >
            &times;
          </button>
        </div>
        <div class="space-y-3 text-gray-800 text-sm">
          <p><span class="font-semibold">Date:</span> {{ formatFullDate(selectedAbsence.date) }}</p>
          <p><span class="font-semibold">Reason:</span> {{ selectedAbsence.reason || 'Not specified' }}</p>
          <p v-if="selectedAbsence.notes"><span class="font-semibold">Notes:</span> {{ selectedAbsence.notes }}</p>
          <p><span class="font-semibold">Period:</span> {{ selectedAbsence.from_date }} to {{ selectedAbsence.to_date }}</p>
          <p><span class="font-semibold">Status:</span> {{ selectedAbsence.status }}</p>
        </div>
      </div>
    </div>

    <!-- Error message -->
    <div v-if="error" class="mt-4 text-center text-red-600 font-medium">
      {{ error }}
    </div>
  </div>
</template>


<script setup>
import { ref, computed, watch, onMounted } from 'vue'

const absences = ref([])
const loading = ref(false)
const error = ref(null)
const selectedAbsence = ref(null)

// Calendar state
const currentDate = ref(new Date())
const year = ref(currentDate.value.getFullYear())
const month = ref(currentDate.value.getMonth())

const daysOfWeek = [ 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun']
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

    // Previous month padding
    const prevMonthLastDay = new Date(year.value, month.value, 0).getDate()
    for (let i = startDay - 1; i >= 0; i--) {
        const date = new Date(year.value, month.value - 1, prevMonthLastDay - i)
        days.push({
            day: prevMonthLastDay - i,
            date: date.toISOString().split('T')[0],
            isCurrentMonth: false,
            dateObj: date
        })
    }

    // Current month days
    for (let i = 1; i <= totalDays; i++) {
        const date = new Date(year.value, month.value, i)
        days.push({
            day: i,
            date: date.toISOString().split('T')[0],
            isCurrentMonth: true,
            dateObj: date
        })
    }

    // Next month padding
    const remainingDays = (7 - (days.length % 7)) % 7
    for (let i = 1; i <= remainingDays; i++) {
        const date = new Date(year.value, month.value + 1, i)
        days.push({
            day: i,
            date: date.toISOString().split('T')[0],
            isCurrentMonth: false,
            dateObj: date
        })
    }

    return days
})

const isAbsent = (date) => {
    return absences.value.some(absence => 
        date >= absence.from_date && date <= absence.to_date
    )
}

const isWeekend = (dateString) => {
    const date = new Date(dateString);
    const day = date.getDay(); // Sunday = 0, Saturday = 6
    return day === 4 || day === 5
};

const getAbsenceDetails = (date) => {
    return absences.value.find(absence => 
        date >= absence.from_date && date <= absence.to_date
    )
}

const isToday = (date) => {
    const today = new Date().toISOString().split('T')[0]
    return date === today
}

const formatDate = (dateString) => {
    const date = new Date(dateString)
    return `${daysOfWeek[date.getDay()]} ${date.getDate()}`
}

const formatFullDate = (dateString) => {
    const date = new Date(dateString)
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

const showAbsenceDetails = (date) => {
    selectedAbsence.value = {
        ...getAbsenceDetails(date),
        date: date
    }
}

const fetchAbsences = async () => {
    loading.value = true
    error.value = null
    absences.value = []

    // Use first day of selected month for API parameter
    const apiDate = new Date(year.value, month.value, 1).toISOString().split('T')[0]
    try {
        const res = await fetch(`http://127.0.0.1:8000/api/student/my-leaves?month=${apiDate}`, {
            headers: {
                Authorization: `Bearer 259|XCTwKBUin65WkH6OtoVzUHnWu7sbPrhT7yhFRkFn115ec94c `,
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

onMounted(fetchAbsences)
watch([month, year], fetchAbsences)
</script>

<style scoped>
/* Optional custom styles */
</style>