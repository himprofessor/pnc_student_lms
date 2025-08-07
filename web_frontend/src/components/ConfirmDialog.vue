<!-- components/ConfirmDialog.vue -->
<template>
  <div v-if="visible" class="confirm-backdrop">
    <div class="confirm-box">
      <p>{{ message }}</p>
      <div class="actions">
        <button @click="confirm(true)" class="px-4 py-2 text-black rounded-md hover:bg-red-400 transition">Yes</button>
        <button @click="confirm(false)" class="px-4 py-2 text-black rounded-md hover:bg-blue-400 transition">No</button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { defineProps, defineEmits } from 'vue';

const props = defineProps({
    visible: Boolean,
    message: String,
});

const emit = defineEmits(['confirm']);

function confirm(result) {
    emit('confirm', result);
}
</script>

<style scoped>
.confirm-backdrop {
  position: fixed;
  top: 0; left: 0;
  width: 100vw;
  height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
}

.confirm-box {
  background: white;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
  padding: 20px;
  border-radius: 10px;
  text-align: center;
  width: 300px;
}

.actions {
  display: flex;
  justify-content: space-around;
  margin-top: 16px;
}
</style>
