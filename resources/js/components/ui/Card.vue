<script setup>
defineProps({
  title: String,
  value: [String, Number],
  icon: String,
  color: {
    type: String,
    default: 'blue',
  },
})

const colors = {
  blue: 'bg-blue-500',
  green: 'bg-green-500',
  yellow: 'bg-yellow-500',
  red: 'bg-red-500',
  purple: 'bg-purple-500',
}
</script>

<template>
  <div class="bg-white rounded-lg shadow p-6">
    <div class="flex items-center">
      <div
        v-if="icon"
        :class="[
          'flex-shrink-0 rounded-md p-3',
          colors[color],
        ]"
      >
        <component :is="icon" class="h-6 w-6 text-white" />
      </div>
      <div class="ml-4">
        <p class="text-sm font-medium text-gray-500">{{ title }}</p>
        <p class="text-2xl font-semibold text-gray-900">{{ value }}</p>
      </div>
    </div>
  </div>
</template>
