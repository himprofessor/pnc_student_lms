<template>
  <div class="fixed top-4 right-4 z-50" v-if="alert.show">
    <AlertStyle :type="alert.type" :message="alert.message" :icon="alert.icon" />
  </div>
</template>

<script setup>
import AlertStyle from '@/components/AlertStyle.vue'
import { alertState as alert } from '@/composables/useAlertStore.js'
</script>
