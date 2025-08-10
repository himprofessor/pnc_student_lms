<template>
  <transition name="toast-fade">
    <div 
      v-if="isVisible"
      class="fixed p-4 rounded-lg shadow-xl z-50 text-white transition-all duration-300"
      :class="[positionClass, typeClass]"
    >
      <div class="flex items-center space-x-3">
        <svg v-if="type === 'success'" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        <svg v-if="type === 'error'" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        <span>{{ message }}</span>
      </div>
    </div>
  </transition>
</template>

<script setup>
import { ref, onMounted, onUnmounted, computed } from 'vue';

const props = defineProps({
  message: {
    type: String,
    required: true,
  },
  type: {
    type: String,
    default: 'success', // 'success' or 'error'
  },
  position: {
    type: String,
    default: 'top-right', // 'top-right', 'top-left'
  },
  duration: {
    type: Number,
    default: 3000, // in milliseconds
  },
});

const isVisible = ref(false);
let timeout = null;

const positionClass = computed(() => {
  return props.position === 'top-left' ? 'top-4 left-4' : 'top-4 right-4';
});

const typeClass = computed(() => {
  return props.type === 'success' ? 'bg-green-500' : 'bg-red-500';
});

onMounted(() => {
  isVisible.value = true;
  timeout = setTimeout(() => {
    isVisible.value = false;
  }, props.duration);
});

onUnmounted(() => {
  clearTimeout(timeout);
});
</script>

<style scoped>
.toast-fade-enter-active,
.toast-fade-leave-active {
  transition: all 0.5s ease-out;
}

.toast-fade-enter-from,
.toast-fade-leave-to {
  opacity: 0;
  transform: translateY(-20px);
}
</style>