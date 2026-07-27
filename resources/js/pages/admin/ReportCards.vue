<template>
  <div>
    <div class="flex items-center justify-between mb-6">
      <h1 class="text-2xl font-bold text-gray-900">Report Cards</h1>
      <button @click="openModal()" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 text-sm font-medium">Generate Report Card</button>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow p-4 mb-6">
      <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Class</label>
          <select v-model="filters.class_id" @change="fetchReportCards" class="w-full rounded-md border-gray-300 text-sm">
            <option value="">All Classes</option>
            <option v-for="cls in classes" :key="cls.id" :value="cls.id">{{ cls.name }}</option>
          </select>
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Term</label>
          <select v-model="filters.term" @change="fetchReportCards" class="w-full rounded-md border-gray-300 text-sm">
            <option value="">All Terms</option>
            <option value="1st Term">1st Term</option>
            <option value="2nd Term">2nd Term</option>
            <option value="3rd Term">3rd Term</option>
          </select>
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Academic Year</label>
          <select v-model="filters.academic_year" @change="fetchReportCards" class="w-full rounded-md border-gray-300 text-sm">
            <option value="">All Years</option>
            <option v-for="y in ['2024','2025','2026']" :key="y" :value="y">{{ y }}</option>
          </select>
        </div>
      </div>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Student</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Class</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Term</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Year</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Avg Score</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Grade</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Published</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
          <tr v-for="rc in reportCards" :key="rc.id">
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ rc.student?.name }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ rc.student?.class?.name }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ rc.term }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ rc.academic_year }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ rc.average_score }}%</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium" :class="rc.overall_grade === 'A+' || rc.overall_grade === 'A' ? 'text-green-600' : rc.overall_grade === 'F' ? 'text-red-600' : 'text-gray-900'">{{ rc.overall_grade }}</td>
            <td class="px-6 py-4 whitespace-nowrap">
              <span :class="rc.is_published ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'" class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full">{{ rc.is_published ? 'Published' : 'Draft' }}</span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm">
              <button v-if="!rc.is_published" @click="publish(rc.id)" class="text-green-600 hover:text-green-900 mr-3">Publish</button>
              <button @click="openModal(rc)" class="text-indigo-600 hover:text-indigo-900 mr-3">Edit</button>
              <button @click="deleteReportCard(rc.id)" class="text-red-600 hover:text-red-900">Delete</button>
            </td>
          </tr>
          <tr v-if="!reportCards.length">
            <td colspan="8" class="px-6 py-12 text-center text-sm text-gray-500">No report cards found</td>
          </tr>
        </tbody>
      </table>
    </div>

    <div v-if="showModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center z-50">
      <div class="bg-white rounded-lg shadow-xl max-w-lg w-full mx-4 p-6">
        <h2 class="text-lg font-semibold mb-4">{{ editing ? 'Edit' : 'Generate' }} Report Card</h2>
        <form @submit.prevent="save">
          <div class="space-y-4">
            <div v-if="!editing"><label class="block text-sm font-medium text-gray-700 mb-1">Student</label><select v-model="form.student_id" required class="w-full rounded-md border-gray-300 text-sm"><option value="">Select...</option><option v-for="s in students" :key="s.id" :value="s.id">{{ s.name }}</option></select></div>
            <div class="grid grid-cols-2 gap-4">
              <div><label class="block text-sm font-medium text-gray-700 mb-1">Term</label><select v-model="form.term" required class="w-full rounded-md border-gray-300 text-sm"><option value="1st Term">1st Term</option><option value="2nd Term">2nd Term</option><option value="3rd Term">3rd Term</option></select></div>
              <div><label class="block text-sm font-medium text-gray-700 mb-1">Academic Year</label><input v-model="form.academic_year" required class="w-full rounded-md border-gray-300 text-sm" /></div>
            </div>
            <div><label class="block text-sm font-medium text-gray-700 mb-1">Comments</label><textarea v-model="form.comments" rows="3" class="w-full rounded-md border-gray-300 text-sm"></textarea></div>
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
import { useToast } from '../../composables/useToast';

const toast = useToast();

const reportCards = ref([]);
const classes = ref([]);
const students = ref([]);
const showModal = ref(false);
const editing = ref(null);
const saving = ref(false);
const filters = ref({ class_id: '', term: '', academic_year: '' });
const form = ref({ student_id: '', term: '1st Term', academic_year: '2026', comments: '' });

const fetchClasses = async () => { try { const r = await api.get('/admin/classes', { params: { per_page: 100 } }); classes.value = r.data.data || r.data; } catch (e) { toast.error('Failed to load classes'); } };
const fetchStudents = async () => { try { const r = await api.get('/admin/students', { params: { per_page: 200 } }); students.value = r.data.data || r.data; } catch (e) { toast.error('Failed to load students'); } };
const fetchReportCards = async () => {
  try {
    const params = {}; Object.entries(filters.value).forEach(([k, v]) => { if (v) params[k] = v; });
    const r = await api.get('/admin/report-cards', { params });
    reportCards.value = r.data.data || r.data;
  } catch (e) { toast.error('Failed to load report cards'); }
};
const openModal = (rc = null) => {
  if (rc) { editing.value = rc.id; form.value = { term: rc.term, academic_year: rc.academic_year, comments: rc.comments || '' }; }
  else { editing.value = null; form.value = { student_id: '', term: '1st Term', academic_year: '2026', comments: '' }; }
  showModal.value = true;
};
const save = async () => {
  saving.value = true;
  try {
    if (editing.value) await api.put(`/admin/report-cards/${editing.value}`, form.value);
    else await api.post('/admin/report-cards', form.value);
    showModal.value = false; fetchReportCards();
  } catch (e) { toast.error(e.response?.data?.message || 'Error saving'); }
  finally { saving.value = false; }
};
const publish = async (id) => { if (!confirm('Publish this report card?')) return; try { await api.post(`/admin/report-cards/${id}/publish`); fetchReportCards(); } catch (e) { toast.error(e.response?.data?.message || 'Error'); } };
const deleteReportCard = async (id) => { if (!confirm('Delete?')) return; try { await api.delete(`/admin/report-cards/${id}`); fetchReportCards(); } catch (e) { toast.error(e.response?.data?.message || 'Error'); } };
onMounted(() => { fetchClasses(); fetchStudents(); fetchReportCards(); });
</script>
