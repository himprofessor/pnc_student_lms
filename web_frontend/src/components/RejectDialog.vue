<!-- RejectModal.vue -->
<template>
  <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white p-6 rounded-lg shadow-lg w-full max-w-md">
      <h2 class="text-lg font-semibold text-gray-800 mb-4">Reject Leave Request</h2>
      <textarea
        v-model="reason"
        rows="4"
        class="w-full border border-gray-300 rounded px-3 py-2 text-sm"
        placeholder="Enter rejection reason..."
      ></textarea>
      <div class="mt-4 flex justify-end gap-2">
        <button
          class="px-4 py-2 text-sm bg-gray-300 rounded hover:bg-gray-400"
          @click="$emit('cancel')"
        >
          Cancel
        </button>
        <button
          class="px-4 py-2 text-sm bg-red-500 text-white rounded hover:bg-red-600"
          @click="submit"
        >
          Submit
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
const emit = defineEmits(['submit', 'cancel'])
const reason = ref('')

const submit = () => {
  if (!reason.value.trim()) {
    alert('Please enter a rejection reason')
    return
  }
  console.log('Emit reason:', reason.value) // ✅ Debug log
  emit('submit', reason.value)
}
</script>
