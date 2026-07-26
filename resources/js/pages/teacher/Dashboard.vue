<template>
  <div>
    <h1 class="text-2xl font-bold text-gray-900 mb-6">Teacher Dashboard</h1>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
      <div v-for="stat in stats" :key="stat.label" class="bg-white rounded-lg shadow p-6">
        <p class="text-sm font-medium text-gray-500">{{ stat.label }}</p>
        <p class="text-3xl font-bold mt-1" :class="stat.color">{{ stat.value }}</p>
      </div>
    </div>
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
      <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-lg font-semibold mb-4">My Classes</h2>
        <ul class="space-y-3">
          <li v-for="cls in classes" :key="cls.id" class="flex justify-between items-center p-3 bg-gray-50 rounded-md">
            <span class="font-medium">{{ cls.name }}</span>
            <span class="text-sm text-gray-500">{{ cls.students_count || 0 }} students</span>
          </li>
          <li v-if="!classes.length" class="text-sm text-gray-500 text-center py-4">No classes assigned</li>
        </ul>
      </div>
      <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-lg font-semibold mb-4">Recent Attendance</h2>
        <ul class="space-y-3">
          <li v-for="a in recentAttendance" :key="a.id" class="flex justify-between items-center p-3 bg-gray-50 rounded-md">
            <span class="text-sm">{{ a.date }}</span>
            <span :class="a.status === 'present' ? 'text-green-600' : a.status === 'absent' ? 'text-red-600' : 'text-yellow-600'" class="text-sm font-medium capitalize">{{ a.status }}</span>
          </li>
          <li v-if="!recentAttendance.length" class="text-sm text-gray-500 text-center py-4">No recent attendance</li>
        </ul>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import api from '../../services/api';

const stats = ref([]);
const classes = ref([]);
const recentAttendance = ref([]);

onMounted(async () => {
  try {
    const r = await api.get('/teacher/dashboard');
    const d = r.data;
    stats.value = [
      { label: 'My Classes', value: d.classes_count || 0, color: 'text-indigo-600' },
      { label: 'Total Students', value: d.students_count || 0, color: 'text-green-600' },
      { label: 'Attendance Today', value: d.attendance_today || 'N/A', color: 'text-yellow-600' },
      { label: 'Pending Evaluations', value: d.pending_evaluations || 0, color: 'text-red-600' },
    ];
    classes.value = d.classes || [];
    recentAttendance.value = d.recent_attendance || [];
  } catch (e) { console.error(e); }
});
</script>
