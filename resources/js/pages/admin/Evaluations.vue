<template>
  <div>
    <div class="flex items-center justify-between mb-6">
      <h1 class="text-2xl font-bold text-gray-900">Evaluations</h1>
      <button @click="openModal()" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 text-sm font-medium">Add Evaluation</button>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow p-4 mb-6">
      <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Class</label>
          <select v-model="filters.class_id" @change="fetchEvaluations" class="w-full rounded-md border-gray-300 text-sm">
            <option value="">All Classes</option>
            <option v-for="cls in classes" :key="cls.id" :value="cls.id">{{ cls.name }}</option>
          </select>
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Academic Year</label>
          <select v-model="filters.academic_year" @change="fetchEvaluations" class="w-full rounded-md border-gray-300 text-sm">
            <option value="">All Years</option>
            <option v-for="y in academicYears" :key="y" :value="y">{{ y }}</option>
          </select>
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Search Student</label>
          <input v-model="filters.search" @input="fetchEvaluations" placeholder="Student name..." class="w-full rounded-md border-gray-300 text-sm" />
        </div>
      </div>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Student</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Class</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Subject</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Term</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Marks</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Grade</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
          <tr v-for="ev in evaluations" :key="ev.id">
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ ev.student?.first_name }} {{ ev.student?.last_name }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ ev.student?.class?.name }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ ev.subject }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ ev.term }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ ev.marks_obtained }}/{{ ev.total_marks }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium" :class="ev.grade === 'A+' || ev.grade === 'A' ? 'text-green-600' : ev.grade === 'F' ? 'text-red-600' : 'text-gray-900'">{{ ev.grade }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm">
              <button @click="openModal(ev)" class="text-indigo-600 hover:text-indigo-900 mr-3">Edit</button>
              <button @click="deleteEvaluation(ev.id)" class="text-red-600 hover:text-red-900">Delete</button>
            </td>
          </tr>
          <tr v-if="!evaluations.length">
            <td colspan="7" class="px-6 py-12 text-center text-sm text-gray-500">No evaluations found</td>
          </tr>
        </tbody>
      </table>
      <div v-if="meta.last_page > 1" class="bg-white px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6">
        <p class="text-sm text-gray-700">Page {{ meta.current_page }} of {{ meta.last_page }}</p>
        <div class="flex gap-2">
          <button @click="page--; fetchEvaluations()" :disabled="page <= 1" class="px-3 py-1 border rounded text-sm disabled:opacity-50">Prev</button>
          <button @click="page++; fetchEvaluations()" :disabled="page >= meta.last_page" class="px-3 py-1 border rounded text-sm disabled:opacity-50">Next</button>
        </div>
      </div>
    </div>

    <!-- Modal -->
    <div v-if="showModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center z-50">
      <div class="bg-white rounded-lg shadow-xl max-w-lg w-full mx-4 p-6">
        <h2 class="text-lg font-semibold mb-4">{{ editing ? 'Edit' : 'Add' }} Evaluation</h2>
        <form @submit.prevent="saveEvaluation">
          <div class="space-y-4">
            <div v-if="!editing">
              <label class="block text-sm font-medium text-gray-700 mb-1">Student</label>
              <select v-model="form.student_id" required class="w-full rounded-md border-gray-300 text-sm">
                <option value="">Select student...</option>
                <option v-for="s in students" :key="s.id" :value="s.id">{{ s.first_name }} {{ s.last_name }}</option>
              </select>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Subject</label>
              <input v-model="form.subject" required class="w-full rounded-md border-gray-300 text-sm" />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Term</label>
              <select v-model="form.term" required class="w-full rounded-md border-gray-300 text-sm">
                <option value="1st Term">1st Term</option>
                <option value="2nd Term">2nd Term</option>
                <option value="3rd Term">3rd Term</option>
              </select>
            </div>
            <div class="grid grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Marks Obtained</label>
                <input type="number" v-model="form.marks_obtained" required min="0" class="w-full rounded-md border-gray-300 text-sm" />
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Total Marks</label>
                <input type="number" v-model="form.total_marks" required min="1" class="w-full rounded-md border-gray-300 text-sm" />
              </div>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Remarks</label>
              <textarea v-model="form.remarks" rows="2" class="w-full rounded-md border-gray-300 text-sm"></textarea>
            </div>
          </div>
          <div class="mt-6 flex justify-end gap-3">
            <button type="button" @click="showModal = false" class="px-4 py-2 border rounded-md text-sm">Cancel</button>
            <button type="submit" :disabled="saving" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 text-sm disabled:opacity-50">{{ saving ? 'Saving...' : 'Save' }}</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import api from '../../services/api';

const evaluations = ref([]);
const classes = ref([]);
const students = ref([]);
const meta = ref({ current_page: 1, last_page: 1 });
const page = ref(1);
const filters = ref({ class_id: '', academic_year: '', search: '' });
const showModal = ref(false);
const editing = ref(null);
const saving = ref(false);
const academicYears = ref(['2024', '2025', '2026']);
const defaultForm = { student_id: '', subject: '', term: '1st Term', marks_obtained: '', total_marks: '100', remarks: '' };
const form = ref({ ...defaultForm });

const fetchClasses = async () => {
  try { const r = await api.get('/admin/classes', { params: { per_page: 100 } }); classes.value = r.data.data || r.data; } catch (e) { console.error(e); }
};
const fetchStudents = async () => {
  try { const r = await api.get('/admin/students', { params: { per_page: 200 } }); students.value = r.data.data || r.data; } catch (e) { console.error(e); }
};
const fetchEvaluations = async () => {
  try {
    const params = { page: page.value };
    Object.entries(filters.value).forEach(([k, v]) => { if (v) params[k] = v; });
    const r = await api.get('/admin/evaluations', { params });
    evaluations.value = r.data.data || [];
    meta.value = r.data.meta || { current_page: 1, last_page: 1 };
  } catch (e) { console.error(e); }
};
const openModal = (ev = null) => {
  if (ev) { editing.value = ev.id; form.value = { subject: ev.subject, term: ev.term, marks_obtained: ev.marks_obtained, total_marks: ev.total_marks, remarks: ev.remarks || '' }; }
  else { editing.value = null; form.value = { ...defaultForm }; }
  showModal.value = true;
};
const saveEvaluation = async () => {
  saving.value = true;
  try {
    if (editing.value) { await api.put(`/admin/evaluations/${editing.value}`, form.value); }
    else { await api.post('/admin/evaluations', form.value); }
    showModal.value = false; fetchEvaluations();
  } catch (e) { alert(e.response?.data?.message || 'Error saving'); }
  finally { saving.value = false; }
};
const deleteEvaluation = async (id) => {
  if (!confirm('Delete this evaluation?')) return;
  try { await api.delete(`/admin/evaluations/${id}`); fetchEvaluations(); } catch (e) { alert(e.response?.data?.message || 'Error deleting'); }
};
onMounted(() => { fetchClasses(); fetchStudents(); fetchEvaluations(); });
</script>
