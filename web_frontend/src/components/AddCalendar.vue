<template>
  <div class="container mx-auto max-w-7xl p-4">
    <h1 class="text-2xl font-semibold mb-6 text-blue-700">Add Event to Calendar</h1>
    <div class="max-w-md mx-auto bg-white p-6 rounded shadow-md">
      <h2 class="text-xl font-semibold mb-4">Task : Add Data</h2>
      <form @submit.prevent="addEvent" class="space-y-4">
        <div>
          <label for="eventName" class="block text-sm font-medium text-gray-700">Enter Name of The Event</label>
          <input v-model="event.name" id="eventName" type="text" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"
                 placeholder="Sumit Colocus" required>
        </div>
        <div>
          <label for="eventColor" class="block text-sm font-medium text-gray-700">Enter Color</label>
          <input v-model="event.color" id="eventColor" type="color" class="mt-1 block w-full h-10 border-gray-300 rounded-md shadow-sm"
                 value="#ff69b4" required>
        </div>
        <div>
          <label for="startDate" class="block text-sm font-medium text-gray-700">Enter Start Date of Event</label>
          <input v-model="event.startDate" id="startDate" type="date" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"
                 required>
        </div>
        <div>
          <label for="endDate" class="block text-sm font-medium text-gray-700">Enter End Date of Event</label>
          <input v-model="event.endDate" id="endDate" type="date" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"
                 required>
        </div>
        <div class="flex justify-end space-x-4">
          <button type="button" @click="goBack" class="bg-blue-500 text-white px-4 py-2 rounded">Back</button>
          <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Add Event Data</button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router' // Assuming Vue Router is used for navigation

const router = useRouter()
const eventAdded = ref(false)
const event = ref({
  name: '',
  color: '#ff69b4', // Default to pink as shown in the image
  startDate: '',
  endDate: ''
})

const goBack = () => {
  router.push('/') // Navigate back to the previous page or home
}

const addEvent = () => {
  // Validate dates
  if (new Date(event.value.startDate) > new Date(event.value.endDate)) {
    alert('End date must be greater than or equal to start date')
    return
  }

  // Here you can add logic to send data to an API or update a calendar
  console.log('Event Added:', event.value)
  eventAdded.value = true
  setTimeout(() => eventAdded.value = false, 2000) // Temporary feedback

  // Reset form
  event.value = {
    name: '',
    color: '#ff69b4',
    startDate: '',
    endDate: ''
  }
}
</script>

<style scoped>
/* Additional custom styles if needed */
input[type="color"]::-webkit-color-swatch {
  border: none;
  border-radius: 0.375rem; /* Matches Tailwind rounded-md */
}
input[type="color"]::-moz-color-swatch {
  border: none;
  border-radius: 0.375rem;
}
</style>