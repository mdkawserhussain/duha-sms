<template>
  <div>
    <div class="flex items-center justify-between mb-6">
      <h1 class="text-2xl font-bold text-gray-900">Terms</h1>
      <button @click="openModal()" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 text-sm font-medium">Add Term</button>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow p-4 mb-6">
      <div class="flex flex-col sm:flex-row gap-3">
        <div class="w-full sm:w-64">
          <select v-model="filters.academic_year_id" @change="fetchTerms" class="w-full rounded-md border-gray-300 text-sm">
            <option value="">All Academic Years</option>
            <option v-for="ay in academicYears" :key="ay.id" :value="ay.id">{{ ay.name }}</option>
          </select>
        </div>
      </div>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Academic Year</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Start Date</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">End Date</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
          <tr v-for="t in terms" :key="t.id">
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ t.academic_year?.name }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ t.name }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ t.start_date }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ t.end_date }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm">
              <span v-if="t.is_current" class="px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800">Current</span>
              <span v-else class="px-2 py-1 text-xs font-medium rounded-full bg-gray-100 text-gray-600">Past</span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm">
              <button v-if="!t.is_current" @click="setCurrent(t.id)" class="text-green-600 hover:text-green-900 mr-3">Set Current</button>
              <button @click="openModal(t)" class="text-indigo-600 hover:text-indigo-900 mr-3">Edit</button>
              <button @click="deleteTerm(t.id)" class="text-red-600 hover:text-red-900">Delete</button>
            </td>
          </tr>
          <tr v-if="!terms.length">
            <td colspan="6" class="px-6 py-12 text-center text-sm text-gray-500">No terms found</td>
          </tr>
        </tbody>
      </table>
    </div>

    <div v-if="showModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center z-50">
      <div class="bg-white rounded-lg shadow-xl max-w-lg w-full mx-4 p-6 relative z-10">
        <h2 class="text-lg font-semibold mb-4">{{ editing ? 'Edit' : 'Add' }} Term</h2>
        <form @submit.prevent="save">
          <div class="space-y-4">
            <div><label class="block text-sm font-medium text-gray-700 mb-1">Academic Year</label><select v-model="form.academic_year_id" required class="w-full rounded-md border-gray-300 text-sm"><option value="">Select...</option><option v-for="ay in academicYears" :key="ay.id" :value="ay.id">{{ ay.name }}</option></select></div>
            <div><label class="block text-sm font-medium text-gray-700 mb-1">Name</label><input v-model="form.name" required class="w-full rounded-md border-gray-300 text-sm" placeholder="e.g. Term 1" /></div>
            <div class="grid grid-cols-2 gap-4">
              <div><label class="block text-sm font-medium text-gray-700 mb-1">Start Date</label><input type="date" v-model="form.start_date" required class="w-full rounded-md border-gray-300 text-sm" /></div>
              <div><label class="block text-sm font-medium text-gray-700 mb-1">End Date</label><input type="date" v-model="form.end_date" required class="w-full rounded-md border-gray-300 text-sm" /></div>
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

const terms = ref([]);
const academicYears = ref([]);
const showModal = ref(false);
const editing = ref(null);
const saving = ref(false);
const filters = ref({ academic_year_id: '' });
const form = ref({ academic_year_id: '', name: '', start_date: '', end_date: '' });

const fetchYears = async () => { try { const r = await api.get('/admin/academic-years', { params: { per_page: 100 } }); academicYears.value = r.data.data || r.data; } catch (e) { console.error(e); } };
const fetchTerms = async () => {
  try {
    const params = {};
    if (filters.value.academic_year_id) params.academic_year_id = filters.value.academic_year_id;
    const r = await api.get('/admin/terms', { params });
    terms.value = r.data.data || r.data;
  } catch (e) { console.error(e); }
};
const openModal = (t = null) => {
  if (t) { editing.value = t.id; form.value = { academic_year_id: t.academic_year_id, name: t.name, start_date: t.start_date, end_date: t.end_date }; }
  else { editing.value = null; form.value = { academic_year_id: '', name: '', start_date: '', end_date: '' }; }
  showModal.value = true;
};
const save = async () => {
  saving.value = true;
  try {
    if (editing.value) await api.put(`/admin/terms/${editing.value}`, form.value);
    else await api.post('/admin/terms', form.value);
    showModal.value = false; fetchTerms();
  } catch (e) { alert(e.response?.data?.message || 'Error saving'); }
  finally { saving.value = false; }
};
const setCurrent = async (id) => {
  try { await api.post(`/admin/terms/${id}/set-current`); fetchTerms(); } catch (e) { alert('Error setting current term'); }
};
const deleteTerm = async (id) => { if (!confirm('Delete this term?')) return; try { await api.delete(`/admin/terms/${id}`); fetchTerms(); } catch (e) { alert(e.response?.data?.message || 'Error'); } };
onMounted(() => { fetchYears(); fetchTerms(); });
</script>
