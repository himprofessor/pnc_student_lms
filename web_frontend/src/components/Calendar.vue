<template>
  <div class="container mx-auto max-w-7xl p-4">
    <div class="bg-green-100 text-green-800 p-2 mb-4" v-if="eventAdded">Event Added</div>
    <div class="flex justify-between mb-4">
      <div>
        <button
            class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm font-medium flex items-center space-x-1"
            @click="$router.push('/add-absence')">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            <span>Add absence</span>
          </button>
        <button @click="editEvent" class="bg-blue-500 text-white px-4 py-2 rounded mr-2">Edit Absence</button>
        <button @click="deleteEvent" class="bg-red-500 text-white px-4 py-2 rounded">Delete Absence</button>
      </div>
    </div>

    <!-- Calendar Header -->
    <div class="bg-blue-800 text-white p-2 mb-4 flex justify-between items-center">
      <div class="flex space-x-2">
        <button @click="prevMonth" class="text-white hover:text-gray-300">&lt;</button>
        <button @click="goToday" class="bg-gray-300 text-black px-2 py-1 rounded">today</button>
        <button @click="nextMonth" class="text-white hover:text-gray-300">&gt;</button>
      </div>
      <h2 class="text-xl font-semibold">{{ monthName }} {{ year }}</h2>
      <div class="flex space-x-2">
        <button class="bg-gray-300 text-black px-2 py-1 rounded">month</button>
        <button class="bg-gray-300 text-black px-2 py-1 rounded">week</button>
        <button class="bg-gray-300 text-black px-2 py-1 rounded">day</button>
      </div>
    </div>

    <!-- Calendar Grid -->
    <div class="grid grid-cols-7 gap-px bg-gray-300">
      <div v-for="day in daysOfWeek" :key="day" class="bg-white p-2 text-center font-semibold text-gray-700">
        {{ day }}
      </div>
      <div v-for="day in calendarDays" :key="day.date" class="bg-white p-2 text-center relative"
           :class="{ 'bg-gray-100': !day.isCurrentMonth, 'bg-red-100': isAbsent(day.date) }"
           @click="showAbsenceDetails(day.date)" role="button" tabindex="0" @keydown.enter.prevent="showAbsenceDetails(day.date)">
        <div class="text-sm">{{ day.day }}</div>
        <div v-if="day.events.length" class="mt-1">
          <div v-for="(event, index) in day.events" :key="index" class="text-xs p-1 rounded"
               :class="{
                 'bg-red-500 text-white': event.type === 'absence'
               }">
            {{ event.title }}
          </div>
        </div>
      </div>
    </div>

    <!-- Absence Details Modal -->
    <div v-if="selectedAbsence" class="fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center p-4 z-50"
         @click.self="selectedAbsence = null">
      <div class="bg-white rounded-lg p-6 max-w-md w-full shadow-lg">
        <div class="flex justify-between items-center mb-4">
          <h3 class="text-lg font-semibold text-gray-900">Absence Details</h3>
          <button @click="selectedAbsence = null" class="text-gray-500 hover:text-gray-700 focus:outline-none text-2xl leading-none"
                  aria-label="Close modal">&times;</button>
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

const editEvent = () => {
  alert('Edit Absence functionality to be implemented')
}

const deleteEvent = () => {
  alert('Delete Absence functionality to be implemented')
}

onMounted(fetchAbsences)
watch([() => currentDate.value.getMonth(), () => currentDate.value.getFullYear()], fetchAbsences)
</script>

<style scoped>
.bg-gray-100 {
  color: #9ca3af;
}

.grid-cols-7 > div {
  min-height: 80px;
}

.bg-red-500 {
  display: block;
  width: 100%;
  overflow: hidden;
  text-overflow: ellipsis;
}

.bg-red-100 {
  cursor: pointer;
}

.bg-red-100:hover {
  background-color: #fee2e2;
}
</style>