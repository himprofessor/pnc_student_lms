<template>
  <main class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100 p-6 md:p-10">
    <div class="max-w-7xl mx-auto">
      <!-- Controls -->
      <div class="flex justify-between items-center mb-8">
        <div class="text-2xl font-bold text-gray-900 tracking-tight">
          {{ dateRangeText }}
        </div>

        <div class="flex items-center gap-4">
          <!-- Total Leave Requests -->
          <div class="px-4 py-2 bg-indigo-50 text-indigo-800 rounded-lg shadow-sm text-sm font-medium">
            Total Leave Requests: {{ totalLeaveRequests }}
          </div>

          <button 
            @click="goToToday"
            class="px-5 py-2.5 text-sm font-semibold text-white bg-indigo-600 rounded-full hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-all duration-200"
          >
            Today
          </button>
          <div class="flex bg-white rounded-full shadow-sm overflow-hidden">
            <button 
              @click="changeWeek(-1)"
              class="px-4 py-2.5 text-gray-600 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-all duration-200"
            >
              &lt;
            </button>
            <button 
              @click="changeWeek(1)"
              class="px-4 py-2.5 text-gray-600 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-all duration-200"
            >
              &gt;
            </button>
          </div>
        </div>
      </div>

      <!-- Calendar Grid -->
      <div class="grid grid-cols-8 border border-gray-200 bg-white rounded-xl shadow-lg overflow-hidden">
        <!-- Time column -->
        <div class="col-span-1 bg-gray-50/50">
          <div class="h-12 flex items-center justify-center text-sm font-semibold text-gray-600 border-b border-r border-gray-200">
            All-day
          </div>
          <div 
            v-for="hour in hours" 
            :key="hour" 
            class="h-20 flex items-start justify-end pr-3 text-sm font-medium text-gray-500 border-b border-r border-gray-200"
          >
            {{ hour }}
          </div>
        </div>

        <!-- Day columns -->
        <div 
          v-for="day in weekDates" 
          :key="day.dateString" 
          class="flex flex-col"
        >
          <div class="h-12 flex items-center justify-center text-sm font-semibold text-gray-800 border-b border-r border-gray-200 bg-gray-50/30">
            {{ day.dayHeader }}
          </div>
          <div class="min-h-[5rem] p-2 border-b border-r border-gray-200">
            <div
              v-for="event in getEventsForDay(day.dateString)"
              :key="event.id"
              @click="viewEventDetails(event)"
              :class="{
                'bg-amber-100/80 text-amber-900 hover:bg-amber-100': event.type === 'leave',
                'bg-rose-100/80 text-rose-900 border border-rose-200 hover:bg-rose-100': event.type === 'absence'
              }"
              class="mb-2 p-3 rounded-lg text-sm cursor-pointer transition-all duration-200 transform hover:scale-[1.02] shadow-sm"
            >
              <div class="font-semibold truncate">{{ event.title }}</div>
              <div class="text-xs text-gray-600">{{ formatDate(event.day) }}</div>
              <div v-if="event.subtitle" class="text-xs text-gray-500 truncate">{{ event.subtitle }}</div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </main>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import axios from 'axios';

const startOfWeek = ref(new Date());
const events = ref([]);

const hours = computed(() => {
  const result = [];
  for (let i = 8; i <= 18; i++) {
    result.push(`${i > 12 ? i - 12 : i}${i >= 12 ? 'pm' : 'am'}`);
  }
  return result;
});

const weekDates = computed(() => {
  const dates = [];
  const start = new Date(startOfWeek.value);
  for (let i = 0; i < 7; i++) {
    const currentDate = new Date(start);
    currentDate.setDate(start.getDate() + i);
    dates.push({
      date: currentDate,
      dateString: currentDate.toISOString().split('T')[0],
      dayHeader: `${['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'][currentDate.getDay()]} ${currentDate.getMonth() + 1}/${currentDate.getDate()}`
    });
  }
  return dates;
});

const dateRangeText = computed(() => {
  const start = weekDates.value[0].date;
  const end = weekDates.value[6].date;
  const startMonth = start.toLocaleString('default', { month: 'short' });
  const endMonth = end.toLocaleString('default', { month: 'short' });
  return `${startMonth} ${start.getDate()} – ${endMonth} ${end.getDate()}, ${start.getFullYear()}`;
});

// ✅ Count total leave requests (group multi-day leave as 1 request)
const totalLeaveRequests = computed(() => {
  const leaveIds = new Set(
    events.value
      .filter(e => e.type === 'leave')
      .map(e => e.id.split('-')[1])
  );
  return leaveIds.size;
});

const getEventsForDay = (dateString) => {
  return events.value.filter(event => event.day === dateString);
};

const changeWeek = (direction) => {
  const newDate = new Date(startOfWeek.value);
  newDate.setDate(newDate.getDate() + (direction * 7));
  startOfWeek.value = newDate;
};

const goToToday = () => {
  const today = new Date();
  const dayOfWeek = today.getDay();
  const diff = today.getDate() - dayOfWeek;
  const newStartOfWeek = new Date(today.setDate(diff));
  startOfWeek.value = newStartOfWeek;
};

const viewEventDetails = (event) => {
  if (event.type === 'leave') {
    alert(
      `Event: ${event.title}\n` +
      `From: ${formatDate(event.from_date)}\n` +
      `To: ${formatDate(event.to_date)}\n` +
      `Details: ${event.subtitle || 'No details'}`
    );
  } else {
    alert(
      `Event: ${event.title}\nDate: ${formatDate(event.day)}\nDetails: ${event.subtitle || 'No details'}`
    );
  }
};

const formatDate = (isoDateStr) => {
  const d = new Date(isoDateStr);
  return d.toLocaleDateString(undefined, { year: 'numeric', month: 'short', day: 'numeric' });
};

const fetchLeaveRequests = async () => {
  try {
    const res = await axios.get('http://127.0.0.1:8000/api/student/my-leaves', {
      headers: {
        Authorization: `Bearer ${localStorage.getItem('token')}`
      }
    });

    const leaveEvents = res.data.leaves.flatMap((leave, index) => {
      const from = new Date(leave.from_date);
      const to = new Date(leave.to_date);
      const days = [];

      for (let d = new Date(from); d <= to; d.setDate(d.getDate() + 1)) {
        const dayStr = d.toISOString().split('T')[0];
        days.push({
          id: `leave-${index}-${dayStr}`,
          title: `Leave: ${leave.leave_type}`,
          subtitle: leave.reason,
          day: dayStr,
          from_date: leave.from_date,  // store start date
          to_date: leave.to_date,      // store end date
          type: 'leave'
        });
      }

      return days;
    });

    events.value = [...events.value, ...leaveEvents];

  } catch (err) {
    console.error('Error fetching leaves:', err);
  }
};

const fetchAbsences = async () => {
  try {
    const res = await axios.get('http://127.0.0.1:8000/api/student/absences', {
      headers: {
        Authorization: `Bearer ${localStorage.getItem('token')}`
      }
    });

    const absenceEvents = res.data.flatMap((absence, index) => {
      const from = new Date(absence.from_date || absence.date);
      const to = new Date(absence.to_date || absence.date);
      const days = [];

      for (let d = new Date(from); d <= to; d.setDate(d.getDate() + 1)) {
        const dayStr = d.toISOString().split('T')[0];
        days.push({
          id: `absence-${index}-${dayStr}`,
          title: 'Absent',
          subtitle: 'Marked absent',
          day: dayStr,
          type: 'absence'
        });
      }

      return days;
    });

    events.value = [...events.value, ...absenceEvents];

  } catch (err) {
    console.error('Error fetching absences:', err);
  }
};

onMounted(() => {
  fetchLeaveRequests();
  fetchAbsences();
});
</script>
