<template>
  <div>
    <div class="flex items-center justify-between mb-6">
      <h1 class="text-2xl font-bold text-gray-900">Attendance</h1>
      <button @click="showReport = true" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 text-sm font-medium">View Report</button>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow p-4 mb-6 flex flex-wrap gap-4 items-end">
      <div><label class="block text-sm font-medium text-gray-700 mb-1">Class</label><select v-model="filters.class_id" @change="fetchAttendance" class="rounded-md border-gray-300 text-sm"><option value="">All Classes</option><option v-for="cls in classes" :key="cls.id" :value="cls.id">{{ cls.name }}</option></select></div>
      <div><label class="block text-sm font-medium text-gray-700 mb-1">Date</label><input type="date" v-model="filters.date" @change="fetchAttendance" class="rounded-md border-gray-300 text-sm" /></div>
    </div>

    <!-- Take Attendance -->
    <div class="bg-white rounded-lg shadow p-6 mb-6">
      <h2 class="text-lg font-semibold mb-4">Take Attendance</h2>
      <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
        <div><label class="block text-sm font-medium text-gray-700 mb-1">Class</label><select v-model="takeForm.class_id" @change="fetchClassStudents" class="w-full rounded-md border-gray-300 text-sm"><option value="">Select class</option><option v-for="cls in classes" :key="cls.id" :value="cls.id">{{ cls.name }}</option></select></div>
        <div><label class="block text-sm font-medium text-gray-700 mb-1">Date</label><input type="date" v-model="takeForm.date" class="w-full rounded-md border-gray-300 text-sm" /></div>
      </div>
      <div v-if="takeForm.students.length" class="space-y-2">
        <div v-for="s in takeForm.students" :key="s.id" class="flex items-center gap-4 p-3 bg-gray-50 rounded-md">
          <span class="flex-1 text-sm font-medium">{{ s.first_name }} {{ s.last_name }}</span>
          <label class="flex items-center gap-1"><input type="radio" :name="'att_' + s.id" value="present" v-model="s.status" class="text-indigo-600" /> Present</label>
          <label class="flex items-center gap-1"><input type="radio" :name="'att_' + s.id" value="absent" v-model="s.status" class="text-red-600" /> Absent</label>
          <label class="flex items-center gap-1"><input type="radio" :name="'att_' + s.id" value="late" v-model="s.status" class="text-yellow-600" /> Late</label>
        </div>
        <button @click="submitAttendance" :disabled="saving" class="mt-4 px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 text-sm disabled:opacity-50">{{ saving ? 'Saving...' : 'Save Attendance' }}</button>
      </div>
      <p v-else-if="takeForm.class_id && takeForm.date" class="text-sm text-gray-500">No students found for this class.</p>
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
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
          <tr v-for="a in attendance" :key="a.id">
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ a.student?.first_name }} {{ a.student?.last_name }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ a.class?.name || '-' }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ a.date }}</td>
            <td class="px-6 py-4 whitespace-nowrap"><span :class="statusClass(a.status)" class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full capitalize">{{ a.status }}</span></td>
          </tr>
          <tr v-if="!attendance.length"><td colspan="4" class="px-6 py-12 text-center text-sm text-gray-500">No attendance records</td></tr>
        </tbody>
      </table>
    </div>

    <!-- Report Modal -->
    <div v-if="showReport" class="fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center z-50">
      <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full mx-4 p-6">
        <div class="flex items-center justify-between mb-4">
          <h2 class="text-lg font-semibold">Attendance Report</h2>
          <button @click="showReport = false" class="text-gray-400 hover:text-gray-600">&times;</button>
        </div>
        <div class="space-y-4">
          <div class="grid grid-cols-2 gap-4">
            <div><label class="block text-sm font-medium text-gray-700 mb-1">Class</label><select v-model="reportForm.class_id" class="w-full rounded-md border-gray-300 text-sm"><option value="">Select class</option><option v-for="cls in classes" :key="cls.id" :value="cls.id">{{ cls.name }}</option></select></div>
            <div><label class="block text-sm font-medium text-gray-700 mb-1">Date Range</label><div class="flex gap-2"><input type="date" v-model="reportForm.start_date" class="rounded-md border-gray-300 text-sm" /><input type="date" v-model="reportForm.end_date" class="rounded-md border-gray-300 text-sm" /></div></div>
          </div>
          <button @click="fetchReport" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 text-sm">Generate</button>
          <div v-if="reportData" class="mt-4 p-4 bg-gray-50 rounded-md">
            <div class="grid grid-cols-3 gap-4 text-center">
              <div><p class="text-2xl font-bold text-green-600">{{ reportData.present || 0 }}</p><p class="text-xs text-gray-500">Present</p></div>
              <div><p class="text-2xl font-bold text-red-600">{{ reportData.absent || 0 }}</p><p class="text-xs text-gray-500">Absent</p></div>
              <div><p class="text-2xl font-bold text-yellow-600">{{ reportData.late || 0 }}</p><p class="text-xs text-gray-500">Late</p></div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import api from '../../services/api';

const classes = ref([]);
const attendance = ref([]);
const filters = ref({ class_id: '', date: '' });
const takeForm = ref({ class_id: '', date: new Date().toISOString().split('T')[0], students: [] });
const saving = ref(false);
const showReport = ref(false);
const reportForm = ref({ class_id: '', start_date: '', end_date: '' });
const reportData = ref(null);

const statusClass = (s) => ({ 'bg-green-100 text-green-800': s === 'present', 'bg-red-100 text-red-800': s === 'absent', 'bg-yellow-100 text-yellow-800': s === 'late' });

const fetchAttendance = async () => {
  try { const params = {}; if (filters.value.class_id) params.class_id = filters.value.class_id; if (filters.value.date) params.date = filters.value.date; const r = await api.get('/teacher/attendance', { params }); attendance.value = r.data.data || r.data || []; } catch (e) { console.error(e); }
};
const fetchClassStudents = async () => {
  if (!takeForm.value.class_id) { takeForm.value.students = []; return; }
  try { const r = await api.get(`/teacher/classes/${takeForm.value.class_id}/students`); takeForm.value.students = (r.data.data || r.data || []).map(s => ({ ...s, status: 'present' })); } catch (e) { console.error(e); }
};
const submitAttendance = async () => {
  saving.value = true;
  try {
    await api.post('/teacher/attendance', { class_id: takeForm.value.class_id, date: takeForm.value.date, records: takeForm.value.students.map(s => ({ student_id: s.id, status: s.status })) });
    fetchAttendance();
  } catch (e) { alert(e.response?.data?.message || 'Error'); }
  finally { saving.value = false; }
};
const fetchReport = async () => {
  try { const r = await api.get('/teacher/attendance/report', { params: reportForm.value }); reportData.value = r.data; } catch (e) { console.error(e); }
};
onMounted(async () => {
  try { const r = await api.get('/teacher/classes'); classes.value = r.data.data || r.data || []; } catch (e) { console.error(e); }
  fetchAttendance();
});
</script>
