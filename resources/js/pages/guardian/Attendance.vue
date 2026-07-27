<template>
  <div>
    <div class="flex items-center justify-between mb-6">
      <h1 class="text-2xl font-bold text-gray-900">Attendance</h1>
      <div class="flex gap-2">
        <select v-model="studentId" @change="fetchAttendance" class="rounded-md border-gray-300 text-sm">
          <option value="">Select Child</option>
          <option v-for="child in children" :key="child.id" :value="child.id">{{ child.name }}</option>
        </select>
        <input type="month" v-model="month" @change="fetchAttendance" class="rounded-md border-gray-300 text-sm">
      </div>
    </div>

    <div v-if="studentId" class="bg-white rounded-lg shadow overflow-hidden">
      <!-- Summary Cards -->
      <div class="grid grid-cols-3 gap-4 p-6 border-b border-gray-200">
        <div class="text-center">
          <p class="text-3xl font-bold text-green-600">{{ summary.present || 0 }}</p>
          <p class="text-sm text-gray-500">Present</p>
        </div>
        <div class="text-center">
          <p class="text-3xl font-bold text-red-600">{{ summary.absent || 0 }}</p>
          <p class="text-sm text-gray-500">Absent</p>
        </div>
        <div class="text-center">
          <p class="text-3xl font-bold text-yellow-600">{{ summary.late || 0 }}</p>
          <p class="text-sm text-gray-500">Late</p>
        </div>
      </div>

      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Check In</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Check Out</th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
          <tr v-for="a in attendance" :key="a.id">
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ a.date }}</td>
            <td class="px-6 py-4 whitespace-nowrap">
              <span :class="statusClass(a.status)" class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full capitalize">{{ a.status }}</span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ a.check_in || '-' }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ a.check_out || '-' }}</td>
          </tr>
          <tr v-if="!attendance.length">
            <td colspan="4" class="px-6 py-12 text-center text-sm text-gray-500">No attendance records found</td>
          </tr>
        </tbody>
      </table>
    </div>

    <div v-else class="bg-white rounded-lg shadow p-12 text-center">
      <p class="text-gray-500">Select a child to view attendance.</p>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import api from '../../services/api';
import { useRoute } from 'vue-router';
import { useToast } from '../../composables/useToast';

const toast = useToast();

const route = useRoute();
const children = ref([]);
const attendance = ref([]);
const summary = ref({});
const studentId = ref('');
const month = ref(new Date().toISOString().slice(0, 7));

const statusClass = (status) => {
  const classes = { 'present': 'bg-green-100 text-green-800', 'absent': 'bg-red-100 text-red-800', 'late': 'bg-yellow-100 text-yellow-800' };
  return classes[status] || 'bg-gray-100 text-gray-800';
};

const fetchAttendance = async () => {
  if (!studentId.value) return;
  try {
    const r = await api.get(`/guardian/attendance/${studentId.value}`, { params: { month: month.value } });
    attendance.value = r.data.data || r.data.attendance || [];
    summary.value = r.data.summary || {};
  } catch (e) {
    toast.error('Failed to load attendance');
  }
};

const fetchChildren = async () => {
  try {
    const r = await api.get('/guardian/children');
    children.value = r.data.data || r.data || [];
    if (route.params.studentId) {
      studentId.value = route.params.studentId;
      fetchAttendance();
    }
  } catch (e) {
    toast.error('Failed to load children');
  }
};

onMounted(() => { fetchChildren(); });
</script>
