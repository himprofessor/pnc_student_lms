<template>
    <div v-if="show" class="fixed inset-0 z-50 flex items-center justify-center p-4 modal-overlay">
        <div class="bg-white rounded-lg shadow-xl p-6 w-full max-w-sm">
            <h3 class="text-lg font-bold text-gray-900 mb-4">{{ title }}</h3>
            <p class="text-gray-700 mb-6">{{ message }}</p>
            <div class="flex justify-end space-x-3">
                <button @click="$emit('cancel')"
                        class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-400">
                    {{ cancelText }}
                </button>
                <button @click="$emit('confirm')"
                        :class="confirmButtonClass"
                        class="px-4 py-2 text-white rounded-md focus:outline-none focus:ring-2 focus:ring-red-500">
                    {{ confirmText }}
                </button>
            </div>
        </div>
    </div>
</template>

<script setup>
import { defineProps, defineEmits } from 'vue';

// Define component props
const props = defineProps({
    show: Boolean,
    title: String,
    message: String,
    confirmText: {
        type: String,
        default: 'Confirm'
    },
    cancelText: {
        type: String,
        default: 'Cancel'
    },
    confirmButtonClass: {
        type: String,
        default: 'bg-red-600 hover:bg-red-700'
    }
});

// Define emitted events
const emit = defineEmits(['confirm', 'cancel']);
</script>

<style scoped>
/* Scoped styles for this component */
.modal-overlay {
    background-color: rgba(0, 0, 0, 0.5); /* Ensure overlay style is here if not global */
}
</style>
