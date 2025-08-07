<script setup>
import { useAlert } from '@/stores/useAlertStore';

</script>

<template>
  <div v-if="useAlert.visible" :class="['alert', `alert-${useAlert.type}`]">
    <span>{{ useAlert.title }}</span>
    <span>{{ useAlert.message }}</span>
  </div>
</template>
