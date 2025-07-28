// src/composables/useAlert.js
import { ref } from 'vue';

export const alertState = ref({
  visible: false,
  type: 'info',
  title: '',
  message: '',
});

export const useAlert = () => {
  const showAlert = (type, title, message) => {
    alertState.value = { visible: true, type, title, message };
    setTimeout(() => {
      alertState.value.visible = false;
    }, 3000); // auto close
  };

  return { alertState, showAlert };
};
