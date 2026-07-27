<template>
  <div>
    <div class="flex items-center justify-between mb-6">
      <h1 class="text-2xl font-bold text-gray-900">Academic Years</h1>
      <button @click="openModal()" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 text-sm font-medium">Add Academic Year</button>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Start Date</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">End Date</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Terms</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
          <tr v-for="ay in academicYears" :key="ay.id">
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ ay.name }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ ay.start_date }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ ay.end_date }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ ay.terms?.length || 0 }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm">
              <span v-if="ay.is_current" class="px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800">Current</span>
              <span v-else class="px-2 py-1 text-xs font-medium rounded-full bg-gray-100 text-gray-600">Past</span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm">
              <button v-if="!ay.is_current" @click="setCurrent(ay.id)" class="text-green-600 hover:text-green-900 mr-3">Set Current</button>
              <button @click="openModal(ay)" class="text-indigo-600 hover:text-indigo-900 mr-3">Edit</button>
              <button @click="deleteYear(ay.id)" class="text-red-600 hover:text-red-900">Delete</button>
            </td>
          </tr>
          <tr v-if="!academicYears.length">
            <td colspan="6" class="px-6 py-12 text-center text-sm text-gray-500">No academic years found</td>
          </tr>
        </tbody>
      </table>
    </div>

    <div v-if="showModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center z-50">
      <div class="bg-white rounded-lg shadow-xl max-w-lg w-full mx-4 p-6 relative z-10">
        <h2 class="text-lg font-semibold mb-4">{{ editing ? 'Edit' : 'Add' }} Academic Year</h2>
        <form @submit.prevent="save">
          <div class="space-y-4">
            <div><label class="block text-sm font-medium text-gray-700 mb-1">Name</label><input v-model="form.name" required class="w-full rounded-md border-gray-300 text-sm" placeholder="e.g. 2026-2027" /></div>
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

const academicYears = ref([]);
const showModal = ref(false);
const editing = ref(null);
const saving = ref(false);
const form = ref({ name: '', start_date: '', end_date: '' });

const fetchYears = async () => {
  try {
    const r = await api.get('/admin/academic-years');
    academicYears.value = r.data.data || r.data;
  } catch (e) { console.error(e); }
};
const openModal = (ay = null) => {
  if (ay) { editing.value = ay.id; form.value = { name: ay.name, start_date: ay.start_date, end_date: ay.end_date }; }
  else { editing.value = null; form.value = { name: '', start_date: '', end_date: '' }; }
  showModal.value = true;
};
const save = async () => {
  saving.value = true;
  try {
    if (editing.value) await api.put(`/admin/academic-years/${editing.value}`, form.value);
    else await api.post('/admin/academic-years', form.value);
    showModal.value = false; fetchYears();
  } catch (e) { alert(e.response?.data?.message || 'Error saving'); }
  finally { saving.value = false; }
};
const setCurrent = async (id) => {
  try { await api.post(`/admin/academic-years/${id}/set-current`); fetchYears(); } catch (e) { alert('Error setting current year'); }
};
const deleteYear = async (id) => { if (!confirm('Delete this academic year?')) return; try { await api.delete(`/admin/academic-years/${id}`); fetchYears(); } catch (e) { alert(e.response?.data?.message || 'Error'); } };
onMounted(() => { fetchYears(); });
</script>
