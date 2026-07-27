<template>
  <div>
    <div class="flex items-center justify-between mb-6">
      <h1 class="text-2xl font-bold text-gray-900">Subjects</h1>
      <button @click="openModal()" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 text-sm font-medium">Add Subject</button>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow p-4 mb-6">
      <div class="flex flex-col sm:flex-row gap-3">
        <div class="flex-1">
          <input v-model="filters.search" @input="debouncedFetch" placeholder="Search by name or code..." class="w-full rounded-md border-gray-300 text-sm" />
        </div>
        <div class="w-full sm:w-48">
          <select v-model="filters.class_id" @change="fetchSubjects" class="w-full rounded-md border-gray-300 text-sm">
            <option value="">All Classes</option>
            <option v-for="cls in classes" :key="cls.id" :value="cls.id">{{ cls.name }}</option>
          </select>
        </div>
      </div>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Code</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Class</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
          <tr v-for="s in subjects" :key="s.id">
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ s.name }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ s.code || '-' }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ s.class?.name }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm">
              <button @click="openModal(s)" class="text-indigo-600 hover:text-indigo-900 mr-3">Edit</button>
              <button @click="deleteSubject(s.id)" class="text-red-600 hover:text-red-900">Delete</button>
            </td>
          </tr>
          <tr v-if="!subjects.length">
            <td colspan="4" class="px-6 py-12 text-center text-sm text-gray-500">No subjects found</td>
          </tr>
        </tbody>
      </table>
    </div>

    <div v-if="showModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center z-50">
      <div class="bg-white rounded-lg shadow-xl max-w-lg w-full mx-4 p-6 relative z-10">
        <h2 class="text-lg font-semibold mb-4">{{ editing ? 'Edit' : 'Add' }} Subject</h2>
        <form @submit.prevent="save">
          <div class="space-y-4">
            <div><label class="block text-sm font-medium text-gray-700 mb-1">Name</label><input v-model="form.name" required class="w-full rounded-md border-gray-300 text-sm" /></div>
            <div><label class="block text-sm font-medium text-gray-700 mb-1">Code</label><input v-model="form.code" class="w-full rounded-md border-gray-300 text-sm" placeholder="e.g. MATH101" /></div>
            <div><label class="block text-sm font-medium text-gray-700 mb-1">Class</label><select v-model="form.class_id" required class="w-full rounded-md border-gray-300 text-sm"><option value="">Select...</option><option v-for="cls in classes" :key="cls.id" :value="cls.id">{{ cls.name }}</option></select></div>
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

const subjects = ref([]);
const classes = ref([]);
const showModal = ref(false);
const editing = ref(null);
const saving = ref(false);
const filters = ref({ search: '', class_id: '' });
const form = ref({ name: '', code: '', class_id: '' });

let debounceTimer = null;
const debouncedFetch = () => {
  clearTimeout(debounceTimer);
  debounceTimer = setTimeout(fetchSubjects, 300);
};

const fetchClasses = async () => { try { const r = await api.get('/admin/classes', { params: { per_page: 100 } }); classes.value = r.data.data || r.data; } catch (e) { console.error(e); } };
const fetchSubjects = async () => {
  try {
    const params = {};
    if (filters.value.search) params.search = filters.value.search;
    if (filters.value.class_id) params.class_id = filters.value.class_id;
    const r = await api.get('/admin/subjects', { params });
    subjects.value = r.data.data || r.data;
  } catch (e) { console.error(e); }
};
const openModal = (s = null) => {
  if (s) { editing.value = s.id; form.value = { name: s.name, code: s.code || '', class_id: s.class_id }; }
  else { editing.value = null; form.value = { name: '', code: '', class_id: '' }; }
  showModal.value = true;
};
const save = async () => {
  saving.value = true;
  try {
    if (editing.value) await api.put(`/admin/subjects/${editing.value}`, form.value);
    else await api.post('/admin/subjects', form.value);
    showModal.value = false; fetchSubjects();
  } catch (e) { alert(e.response?.data?.message || 'Error saving'); }
  finally { saving.value = false; }
};
const deleteSubject = async (id) => { if (!confirm('Delete this subject?')) return; try { await api.delete(`/admin/subjects/${id}`); fetchSubjects(); } catch (e) { alert(e.response?.data?.message || 'Error'); } };
onMounted(() => { fetchClasses(); fetchSubjects(); });
</script>
