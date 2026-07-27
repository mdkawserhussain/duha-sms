<template>
  <div>
    <div class="flex items-center justify-between mb-6">
      <h1 class="text-2xl font-bold text-gray-900">My Routine</h1>
      <div class="flex gap-2">
        <select v-model="day" class="rounded-md border-gray-300 text-sm">
          <option value="">All Days</option>
          <option v-for="d in days" :key="d" :value="d">{{ d }}</option>
        </select>
        <select v-model="classId" class="rounded-md border-gray-300 text-sm">
          <option value="">All Classes</option>
          <option v-for="c in classes" :key="c.id" :value="c.id">{{ c.name }}</option>
        </select>
      </div>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Day</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Time</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Subject</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Class</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Room</th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
          <tr v-for="r in routines" :key="r.id">
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ r.day }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ r.start_time }} - {{ r.end_time }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ r.subject?.name || r.subject }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ r.class?.name || '-' }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ r.room || '-' }}</td>
          </tr>
          <tr v-if="!routines.length">
            <td colspan="5" class="px-6 py-12 text-center text-sm text-gray-500">No routine found</td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue';
import api from '../../services/api';
import { useToast } from '../../composables/useToast';

const toast = useToast();
const routines = ref([]);
const classes = ref([]);
const day = ref('');
const classId = ref('');
const days = ['Saturday', 'Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'];

const fetchRoutines = async () => {
  try {
    const params = {};
    if (day.value) params.day = day.value;
    if (classId.value) params.class_id = classId.value;
    const r = await api.get('/teacher/routine', { params });
    routines.value = r.data.data || r.data || [];
  } catch (e) {
    toast.error('Failed to load routines');
  }
};

const fetchClasses = async () => {
  try {
    const r = await api.get('/teacher/classes');
    classes.value = r.data.data || r.data || [];
  } catch (e) {
    toast.error('Failed to load classes');
  }
};

watch([day, classId], fetchRoutines);

onMounted(() => {
  fetchClasses();
  fetchRoutines();
});
</script>
