<template>
  <div>
    <div class="flex items-center justify-between mb-6">
      <h1 class="text-2xl font-bold text-gray-900">Attendance</h1>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow p-4 mb-6">
      <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Class</label>
          <select v-model="filters.class_id" @change="fetchAttendance" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
            <option value="">All Classes</option>
            <option v-for="cls in classes" :key="cls.id" :value="cls.id">{{ cls.name }}</option>
          </select>
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Date</label>
          <input type="date" v-model="filters.date" @change="fetchAttendance" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm" />
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
          <select v-model="filters.status" @change="fetchAttendance" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
            <option value="">All</option>
            <option value="present">Present</option>
            <option value="absent">Absent</option>
            <option value="late">Late</option>
            <option value="excused">Excused</option>
          </select>
        </div>
        <div class="flex items-end">
          <button @click="openReportModal" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 text-sm font-medium">View Report</button>
        </div>
      </div>
    </div>

    <!-- Attendance Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Student</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Class</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Remarks</th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
          <tr v-for="record in attendance" :key="record.id">
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ record.student?.first_name }} {{ record.student?.last_name }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ record.student?.class?.name }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ record.date }}</td>
            <td class="px-6 py-4 whitespace-nowrap">
              <span :class="statusClass(record.status)" class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full">{{ record.status }}</span>
            </td>
            <td class="px-6 py-4 text-sm text-gray-500">{{ record.remarks || '-' }}</td>
          </tr>
          <tr v-if="!attendance.length">
            <td colspan="5" class="px-6 py-12 text-center text-sm text-gray-500">No attendance records found</td>
          </tr>
        </tbody>
      </table>
      <!-- Pagination -->
      <div v-if="meta.last_page > 1" class="bg-white px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6">
        <div class="flex-1 flex justify-between sm:hidden">
          <button @click="page--" :disabled="page <= 1" class="relative inline-flex items-center px-4 py-2 border text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 disabled:opacity-50">Previous</button>
          <button @click="page++" :disabled="page >= meta.last_page" class="ml-3 relative inline-flex items-center px-4 py-2 border text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 disabled:opacity-50">Next</button>
        </div>
        <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
          <p class="text-sm text-gray-700">Page {{ meta.current_page }} of {{ meta.last_page }}</p>
        </div>
      </div>
    </div>

    <!-- Report Modal -->
    <div v-if="showReport" class="fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center z-50">
      <div class="bg-white rounded-lg shadow-xl max-w-4xl w-full mx-4 max-h-[90vh] overflow-y-auto p-6">
        <div class="flex items-center justify-between mb-4">
          <h2 class="text-lg font-semibold">Attendance Report</h2>
          <button @click="showReport = false" class="text-gray-400 hover:text-gray-600">&times;</button>
        </div>
        <div class="grid grid-cols-4 gap-4 mb-4">
          <div class="bg-green-50 rounded-lg p-4 text-center">
            <p class="text-2xl font-bold text-green-600">{{ report.present }}</p>
            <p class="text-sm text-green-700">Present</p>
          </div>
          <div class="bg-red-50 rounded-lg p-4 text-center">
            <p class="text-2xl font-bold text-red-600">{{ report.absent }}</p>
            <p class="text-sm text-red-700">Absent</p>
          </div>
          <div class="bg-yellow-50 rounded-lg p-4 text-center">
            <p class="text-2xl font-bold text-yellow-600">{{ report.late }}</p>
            <p class="text-sm text-yellow-700">Late</p>
          </div>
          <div class="bg-blue-50 rounded-lg p-4 text-center">
            <p class="text-2xl font-bold text-blue-600">{{ report.excused }}</p>
            <p class="text-sm text-blue-700">Excused</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import api from '../../services/api';

const attendance = ref([]);
const classes = ref([]);
const meta = ref({ current_page: 1, last_page: 1 });
const page = ref(1);
const filters = ref({ class_id: '', date: '', status: '' });
const showReport = ref(false);
const report = ref({ present: 0, absent: 0, late: 0, excused: 0 });

const statusClass = (s) => ({
  'bg-green-100 text-green-800': s === 'present',
  'bg-red-100 text-red-800': s === 'absent',
  'bg-yellow-100 text-yellow-800': s === 'late',
  'bg-blue-100 text-blue-800': s === 'excused',
});

const fetchClasses = async () => {
  try {
    const r = await api.get('/admin/classes', { params: { per_page: 100 } });
    classes.value = r.data.data || r.data;
  } catch (e) { console.error(e); }
};

const fetchAttendance = async () => {
  try {
    const params = { page: page.value, ...filters.value };
    Object.keys(params).forEach(k => { if (!params[k]) delete params[k]; });
    const r = await api.get('/admin/attendance', { params });
    attendance.value = r.data.data || [];
    meta.value = r.data.meta || { current_page: 1, last_page: 1 };
  } catch (e) { console.error(e); }
};

const openReportModal = async () => {
  try {
    const params = {};
    if (filters.value.class_id) params.class_id = filters.value.class_id;
    if (filters.value.date) params.date = filters.value.date;
    const r = await api.get('/admin/attendance/report', { params });
    report.value = r.data;
    showReport.value = true;
  } catch (e) { console.error(e); }
};

onMounted(() => { fetchClasses(); fetchAttendance(); });
</script>
