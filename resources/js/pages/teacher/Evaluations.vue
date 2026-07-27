<template>
  <div>
    <div class="flex items-center justify-between mb-6">
      <h1 class="text-2xl font-bold text-gray-900">Evaluations</h1>
      <button @click="openModal()" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 text-sm font-medium">Add Evaluation</button>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow p-4 mb-6 flex flex-wrap gap-4 items-end">
      <div><label class="block text-sm font-medium text-gray-700 mb-1">Class</label><select v-model="filters.class_id" @change="fetchEvaluations" class="rounded-md border-gray-300 text-sm"><option value="">All Classes</option><option v-for="cls in classes" :key="cls.id" :value="cls.id">{{ cls.name }}</option></select></div>
      <div><label class="block text-sm font-medium text-gray-700 mb-1">Subject</label><input v-model="filters.subject" @input="fetchEvaluations" class="rounded-md border-gray-300 text-sm" placeholder="Filter by subject" /></div>
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
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ ev.student?.name }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ ev.class?.name || '-' }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ ev.subject }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 capitalize">{{ ev.term }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ ev.marks }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 font-semibold">{{ ev.grade }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm">
              <button @click="openModal(ev)" class="text-indigo-600 hover:text-indigo-900 mr-3">Edit</button>
              <button @click="deleteEval(ev.id)" class="text-red-600 hover:text-red-900">Delete</button>
            </td>
          </tr>
          <tr v-if="!evaluations.length"><td colspan="7" class="px-6 py-12 text-center text-sm text-gray-500">No evaluations found</td></tr>
        </tbody>
      </table>
    </div>

    <!-- Modal -->
    <div v-if="showModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center z-50">
      <div class="bg-white rounded-lg shadow-xl max-w-lg w-full mx-4 p-6">
        <div class="flex items-center justify-between mb-4">
          <h2 class="text-lg font-semibold">{{ editing ? 'Edit' : 'Add' }} Evaluation</h2>
          <button @click="showModal = false" class="text-gray-400 hover:text-gray-600">&times;</button>
        </div>
        <form @submit.prevent="saveEvaluation">
          <div class="space-y-4">
            <div><label class="block text-sm font-medium text-gray-700 mb-1">Class</label><select v-model="form.class_id" required class="w-full rounded-md border-gray-300 text-sm"><option value="">Select class</option><option v-for="cls in classes" :key="cls.id" :value="cls.id">{{ cls.name }}</option></select></div>
            <div><label class="block text-sm font-medium text-gray-700 mb-1">Student</label><select v-model="form.student_id" required class="w-full rounded-md border-gray-300 text-sm"><option value="">Select student</option><option v-for="s in classStudents" :key="s.id" :value="s.id">{{ s.name }}</option></select></div>
            <div><label class="block text-sm font-medium text-gray-700 mb-1">Subject</label><input v-model="form.subject" required class="w-full rounded-md border-gray-300 text-sm" /></div>
            <div><label class="block text-sm font-medium text-gray-700 mb-1">Term</label><select v-model="form.term" required class="w-full rounded-md border-gray-300 text-sm"><option value="first">First</option><option value="second">Second</option><option value="third">Third</option></select></div>
            <div class="grid grid-cols-2 gap-4">
              <div><label class="block text-sm font-medium text-gray-700 mb-1">Marks</label><input type="number" v-model="form.marks" required class="w-full rounded-md border-gray-300 text-sm" /></div>
              <div><label class="block text-sm font-medium text-gray-700 mb-1">Grade</label><input v-model="form.grade" required class="w-full rounded-md border-gray-300 text-sm" /></div>
            </div>
            <div><label class="block text-sm font-medium text-gray-700 mb-1">Comments</label><textarea v-model="form.comments" rows="2" class="w-full rounded-md border-gray-300 text-sm"></textarea></div>
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
import { ref, onMounted, watch } from 'vue';
import api from '../../services/api';
import { useToast } from '../../composables/useToast';

const toast = useToast();

const classes = ref([]);
const evaluations = ref([]);
const classStudents = ref([]);
const filters = ref({ class_id: '', subject: '' });
const showModal = ref(false);
const editing = ref(null);
const saving = ref(false);
const form = ref({ class_id: '', student_id: '', subject: '', term: 'first', marks: '', grade: '', comments: '' });

const fetchEvaluations = async () => {
  try { const params = {}; if (filters.value.class_id) params.class_id = filters.value.class_id; if (filters.value.subject) params.subject = filters.value.subject; const r = await api.get('/teacher/evaluations', { params }); evaluations.value = r.data.data || r.data || []; } catch (e) { toast.error('Failed to load evaluations'); }
};
const fetchClassStudents = async (classId) => {
  if (!classId) { classStudents.value = []; return; }
  try { const r = await api.get(`/teacher/classes/${classId}/students`); classStudents.value = r.data.data || r.data || []; } catch (e) { toast.error('Failed to load students'); }
};
watch(() => form.value.class_id, (v) => { fetchClassStudents(v); form.value.student_id = ''; });
const openModal = (ev = null) => {
  editing.value = ev;
  form.value = ev ? { class_id: ev.class_id, student_id: ev.student_id, subject: ev.subject, term: ev.term, marks: ev.marks, grade: ev.grade, comments: ev.comments || '' } : { class_id: '', student_id: '', subject: '', term: 'first', marks: '', grade: '', comments: '' };
  if (ev?.class_id) fetchClassStudents(ev.class_id);
  showModal.value = true;
};
const saveEvaluation = async () => {
  saving.value = true;
  try {
    if (editing.value) { await api.put(`/teacher/evaluations/${editing.value.id}`, form.value); }
    else { await api.post('/teacher/evaluations', form.value); }
    showModal.value = false; fetchEvaluations();
  } catch (e) { toast.error(e.response?.data?.message || 'Error'); }
  finally { saving.value = false; }
};
const deleteEval = async (id) => { if (!confirm('Delete?')) return; try { await api.delete(`/teacher/evaluations/${id}`); fetchEvaluations(); } catch (e) { toast.error(e.response?.data?.message || 'Error'); } };
onMounted(async () => {
  try { const r = await api.get('/teacher/classes'); classes.value = r.data.data || r.data || []; } catch (e) { toast.error('Failed to load classes'); }
  fetchEvaluations();
});
</script>
