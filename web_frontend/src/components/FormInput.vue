<template>
    <div class="mb-4">
        <label :for="id" class="block text-gray-700 text-sm font-semibold mb-2">{{ label }}:</label>
        <input :type="type" :id="id" :value="modelValue"
               @input="$emit('update:modelValue', $event.target.value)"
               :placeholder="placeholder"
               :required="required"
               :disabled="disabled"
               class="shadow-sm appearance-none border border-gray-300 rounded-md w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
               :class="{'border-red-500': error, 'bg-gray-100 cursor-not-allowed': disabled}">
        <p v-if="error" class="text-red-500 text-xs italic mt-1">{{ error }}</p>
        <small v-if="hint" class="text-gray-500 text-xs mt-1 block">{{ hint }}</small>
    </div>
</template>

<script setup>
import { defineProps, defineEmits } from 'vue';

// Define component props
const props = defineProps({
    id: String,
    label: String,
    type: {
        type: String,
        default: 'text'
    },
    modelValue: [String, Number],
    placeholder: String,
    error: String,
    hint: String,
    required: {
        type: Boolean,
        default: false
    },
    disabled: {
        type: Boolean,
        default: false
    }
});

// Define emitted events
const emit = defineEmits(['update:modelValue']);
</script>

<style scoped>
/* Scoped styles for this component if any specific styling is needed */
</style>
