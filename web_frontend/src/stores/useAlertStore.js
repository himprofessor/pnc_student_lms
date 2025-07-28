// composables/useAlertStore.js
import { reactive } from 'vue'

export const alertState = reactive({
  show: false,
  type: 'info',
  message: '',
  icon: null,
})

export function showAlert(type, message, icon = null) {
  alertState.type = type
  alertState.message = message
  alertState.icon = icon
  alertState.show = true
  setTimeout(() => {
    alertState.show = false
  }, 3000)
}
