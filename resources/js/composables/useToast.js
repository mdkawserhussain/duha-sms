import { reactive } from 'vue';

const state = reactive({
  toasts: [],
  nextId: 0,
});

let timer = null;

export function useToast() {
  function addToast(message, type = 'info', duration = 4000) {
    const id = state.nextId++;
    state.toasts.push({ id, message, type, leaving: false });

    setTimeout(() => {
      const toast = state.toasts.find(t => t.id === id);
      if (toast) toast.leaving = true;
      setTimeout(() => {
        state.toasts = state.toasts.filter(t => t.id !== id);
      }, 300);
    }, duration);
  }

  function success(message) { addToast(message, 'success'); }
  function error(message) { addToast(message, 'error', 6000); }
  function warning(message) { addToast(message, 'warning', 5000); }
  function info(message) { addToast(message, 'info'); }

  return { state, success, error, warning, info };
}
