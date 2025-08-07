<template>
  <div class="max-w-3xl mx-auto p-4">
    <h2 class="text-2xl font-bold text-center mb-6">My Absence Calendar</h2>

    <!-- Calendar Header (Day Names) -->
    <div class="grid grid-cols-7 text-center text-sm font-medium text-gray-600 mb-2">
      <div v-for="day in weekDays" :key="day">{{ day }}</div>
    </div>

    <!-- Calendar Days -->
    <div class="grid grid-cols-7 gap-2 text-center">
      <!-- Empty cells for days before the 1st -->
      <div v-for="n in firstDayOfMonth" :key="'empty-' + n"></div>

      <!-- Days of current month -->
      <div
        v-for="day in daysInMonth"
        :key="day"
        class="rounded-lg p-2 transition"
        :class="{
          'bg-red-200 text-red-800 font-semibold': isAbsent(day),
          'hover:bg-gray-200 cursor-pointer': !isAbsent(day)
        }"
      >
        {{ day }}
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'AbsenceCalendar',
  data() {
    const today = new Date()
    const year = today.getFullYear()
    const month = today.getMonth()

    return {
      weekDays: ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'],
      year,
      month,
      absenceDates: [
        '2025-08-03',
        '2025-08-07',
        '2025-08-14'
      ],
    }
  },
  computed: {
    daysInMonth() {
      return new Date(this.year, this.month + 1, 0).getDate()
    },
    firstDayOfMonth() {
      return new Date(this.year, this.month, 1).getDay()
    }
  },
  methods: {
    isAbsent(day) {
      const date = `${this.year}-${String(this.month + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`
      return this.absenceDates.includes(date)
    }
  }
}
</script>
