<template>
  <div>
    <div class="flex items-center justify-between mb-6">
      <h1 class="text-2xl font-bold text-gray-900">Class Routines</h1>
      <button @click="openModal()" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 text-sm font-medium">Add Routine</button>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow p-4 mb-6">
      <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Class</label>
          <select v-model="filters.class_id" @change="fetchRoutines" class="w-full rounded-md border-gray-300 text-sm">
            <option value="">All Classes</option>
            <option v-for="cls in classes" :key="cls.id" :value="cls.id">{{ cls.name }}</option>
          </select>
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Day</label>
          <select v-model="filters.day" @change="fetchRoutines" class="w-full rounded-md border-gray-300 text-sm">
            <option value="">All Days</option>
            <option v-for="d in days" :key="d" :value="d">{{ d }}</option>
          </select>
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Teacher</label>
          <select v-model="filters.teacher_id" @change="fetchRoutines" class="w-full rounded-md border-gray-300 text-sm">
            <option value="">All Teachers</option>
            <option v-for="t in teachers" :key="t.id" :value="t.id">{{ t.name }}</option>
          </select>
        </div>
      </div>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Class</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Day</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Start</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">End</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Subject</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Teacher</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Room</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
          <tr v-for="r in routines" :key="r.id">
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ r.class?.name }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ r.day_of_week }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ r.start_time }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ r.end_time }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ r.subject?.name }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ r.teacher?.name }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ r.room || '-' }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm">
              <button @click="openModal(r)" class="text-indigo-600 hover:text-indigo-900 mr-3">Edit</button>
              <button @click="deleteRoutine(r.id)" class="text-red-600 hover:text-red-900">Delete</button>
            </td>
          </tr>
          <tr v-if="!routines.length">
            <td colspan="8" class="px-6 py-12 text-center text-sm text-gray-500">No routines found</td>
          </tr>
        </tbody>
      </table>
    </div>

    <div v-if="showModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center z-50">
      <div class="bg-white rounded-lg shadow-xl max-w-lg w-full mx-4 p-6">
        <h2 class="text-lg font-semibold mb-4">{{ editing ? 'Edit' : 'Add' }} Routine</h2>
        <form @submit.prevent="save">
          <div class="space-y-4">
            <div class="grid grid-cols-2 gap-4">
              <div><label class="block text-sm font-medium text-gray-700 mb-1">Class</label><select v-model="form.class_id" required class="w-full rounded-md border-gray-300 text-sm"><option value="">Select...</option><option v-for="cls in classes" :key="cls.id" :value="cls.id">{{ cls.name }}</option></select></div>
              <div><label class="block text-sm font-medium text-gray-700 mb-1">Day</label><select v-model="form.day_of_week" required class="w-full rounded-md border-gray-300 text-sm"><option v-for="d in days" :key="d" :value="d">{{ d }}</option></select></div>
            </div>
            <div class="grid grid-cols-2 gap-4">
              <div><label class="block text-sm font-medium text-gray-700 mb-1">Start Time</label><input type="time" v-model="form.start_time" required class="w-full rounded-md border-gray-300 text-sm" /></div>
              <div><label class="block text-sm font-medium text-gray-700 mb-1">End Time</label><input type="time" v-model="form.end_time" required class="w-full rounded-md border-gray-300 text-sm" /></div>
            </div>
            <div class="grid grid-cols-2 gap-4">
              <div><label class="block text-sm font-medium text-gray-700 mb-1">Subject</label><select v-model="form.subject_id" required class="w-full rounded-md border-gray-300 text-sm"><option value="">Select...</option><option v-for="s in subjects" :key="s.id" :value="s.id">{{ s.name }}</option></select></div>
              <div><label class="block text-sm font-medium text-gray-700 mb-1">Teacher</label><select v-model="form.teacher_id" required class="w-full rounded-md border-gray-300 text-sm"><option value="">Select...</option><option v-for="t in teachers" :key="t.id" :value="t.id">{{ t.name }}</option></select></div>
            </div>
            <div><label class="block text-sm font-medium text-gray-700 mb-1">Room</label><input v-model="form.room" class="w-full rounded-md border-gray-300 text-sm" /></div>
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

const routines = ref([]);
const classes = ref([]);
const teachers = ref([]);
const subjects = ref([]);
const showModal = ref(false);
const editing = ref(null);
const saving = ref(false);
const filters = ref({ class_id: '', day: '', teacher_id: '' });
const days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
const form = ref({ class_id: '', day_of_week: 'Sunday', start_time: '', end_time: '', subject_id: '', teacher_id: '', room: '' });

const fetchClasses = async () => { try { const r = await api.get('/admin/classes', { params: { per_page: 100 } }); classes.value = r.data.data || r.data; } catch (e) { toast.error('Failed to load classes'); } };
const fetchTeachers = async () => { try { const r = await api.get('/admin/teachers', { params: { per_page: 200 } }); teachers.value = r.data.data || r.data; } catch (e) { toast.error('Failed to load teachers'); } };
const fetchSubjects = async () => { try { const r = await api.get('/admin/subjects', { params: { per_page: 200 } }); subjects.value = r.data.data || r.data; } catch (e) { toast.error('Failed to load subjects'); } };
const fetchRoutines = async () => {
  try {
    const params = {}; Object.entries(filters.value).forEach(([k, v]) => { if (v) params[k] = v; });
    const r = await api.get('/admin/routines', { params });
    routines.value = r.data.data || r.data;
  } catch (e) { toast.error('Failed to load routines'); }
};
const openModal = (r = null) => {
  if (r) { editing.value = r.id; form.value = { class_id: r.class_id, day_of_week: r.day_of_week, start_time: r.start_time, end_time: r.end_time, subject_id: r.subject_id || '', teacher_id: r.teacher_id, room: r.room || '' }; }
  else { editing.value = null; form.value = { class_id: '', day_of_week: 'Sunday', start_time: '', end_time: '', subject_id: '', teacher_id: '', room: '' }; }
  showModal.value = true;
};
const save = async () => {
  saving.value = true;
  try {
    if (editing.value) await api.put(`/admin/routines/${editing.value}`, form.value);
    else await api.post('/admin/routines', form.value);
    showModal.value = false; fetchRoutines();
  } catch (e) { toast.error(e.response?.data?.message || 'Error saving'); }
  finally { saving.value = false; }
};
const deleteRoutine = async (id) => { if (!confirm('Delete?')) return; try { await api.delete(`/admin/routines/${id}`); fetchRoutines(); } catch (e) { toast.error(e.response?.data?.message || 'Error'); } };
onMounted(() => { fetchClasses(); fetchTeachers(); fetchSubjects(); fetchRoutines(); });
</script>
