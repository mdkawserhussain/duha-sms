<template>
  <div class="fixed top-4 right-4 z-50 space-y-2" style="max-width: 400px;">
    <TransitionGroup name="toast">
      <div
        v-for="toast in toasts"
        :key="toast.id"
        :class="[
          'px-4 py-3 rounded-lg shadow-lg text-sm font-medium flex items-center gap-2 transition-all duration-300',
          toast.leaving ? 'opacity-0 translate-x-full' : 'opacity-100 translate-x-0',
          {
            'bg-green-50 text-green-800 border border-green-200': toast.type === 'success',
            'bg-red-50 text-red-800 border border-red-200': toast.type === 'error',
            'bg-yellow-50 text-yellow-800 border border-yellow-200': toast.type === 'warning',
            'bg-blue-50 text-blue-800 border border-blue-200': toast.type === 'info',
          }
        ]"
      >
        <span v-if="toast.type === 'success'" class="text-green-500">&#10003;</span>
        <span v-else-if="toast.type === 'error'" class="text-red-500">&#10007;</span>
        <span v-else-if="toast.type === 'warning'" class="text-yellow-500">&#9888;</span>
        <span v-else class="text-blue-500">&#8505;</span>
        <span class="flex-1">{{ toast.message }}</span>
      </div>
    </TransitionGroup>
  </div>
</template>

<script setup>
import { useToast } from '../composables/useToast';
const { state } = useToast();
const toasts = state.toasts;
</script>

<style scoped>
.toast-enter-active { transition: all 0.3s ease; }
.toast-leave-active { transition: all 0.3s ease; }
.toast-enter-from { opacity: 0; transform: translateX(100%); }
.toast-leave-to { opacity: 0; transform: translateX(100%); }
</style>
