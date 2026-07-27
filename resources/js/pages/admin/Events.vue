<template>
  <div>
    <div class="flex items-center justify-between mb-6">
      <h1 class="text-2xl font-bold text-gray-900">Events</h1>
      <button @click="openModal()" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 text-sm font-medium">Add Event</button>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Title</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Start</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">End</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
          <tr v-for="e in events" :key="e.id">
            <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ e.title }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ e.start_date }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ e.end_date }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ e.event_type || '-' }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm">
              <button @click="openModal(e)" class="text-indigo-600 hover:text-indigo-900 mr-3">Edit</button>
              <button @click="deleteEvent(e.id)" class="text-red-600 hover:text-red-900">Delete</button>
            </td>
          </tr>
          <tr v-if="!events.length">
            <td colspan="5" class="px-6 py-12 text-center text-sm text-gray-500">No events found</td>
          </tr>
        </tbody>
      </table>
    </div>

    <div v-if="showModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center z-50">
      <div class="bg-white rounded-lg shadow-xl max-w-lg w-full mx-4 p-6">
        <h2 class="text-lg font-semibold mb-4">{{ editing ? 'Edit' : 'Add' }} Event</h2>
        <form @submit.prevent="save">
          <div class="space-y-4">
            <div><label class="block text-sm font-medium text-gray-700 mb-1">Title</label><input v-model="form.title" required class="w-full rounded-md border-gray-300 text-sm" /></div>
            <div><label class="block text-sm font-medium text-gray-700 mb-1">Description</label><textarea v-model="form.description" rows="3" class="w-full rounded-md border-gray-300 text-sm"></textarea></div>
            <div class="grid grid-cols-2 gap-4">
              <div><label class="block text-sm font-medium text-gray-700 mb-1">Start Date</label><input type="datetime-local" v-model="form.start_date" required class="w-full rounded-md border-gray-300 text-sm" /></div>
              <div><label class="block text-sm font-medium text-gray-700 mb-1">End Date</label><input type="datetime-local" v-model="form.end_date" required class="w-full rounded-md border-gray-300 text-sm" /></div>
            </div>
            <div><label class="block text-sm font-medium text-gray-700 mb-1">Type</label><input v-model="form.event_type" placeholder="e.g. exam, holiday, meeting" class="w-full rounded-md border-gray-300 text-sm" /></div>
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

const events = ref([]);
const showModal = ref(false);
const editing = ref(null);
const saving = ref(false);
const form = ref({ title: '', description: '', start_date: '', end_date: '', event_type: '' });

const fetch = async () => { try { const r = await api.get('/admin/events'); events.value = r.data.data || r.data; } catch (e) { toast.error('Failed to load events'); } };
const openModal = (e = null) => {
  if (e) { editing.value = e.id; form.value = { title: e.title, description: e.description || '', start_date: e.start_date, end_date: e.end_date, event_type: e.event_type || '' }; }
  else { editing.value = null; form.value = { title: '', description: '', start_date: '', end_date: '', event_type: '' }; }
  showModal.value = true;
};
const save = async () => {
  saving.value = true;
  try {
    if (editing.value) await api.put(`/admin/events/${editing.value}`, form.value);
    else await api.post('/admin/events', form.value);
    showModal.value = false; fetch();
  } catch (e) { toast.error(e.response?.data?.message || 'Error saving'); }
  finally { saving.value = false; }
};
const deleteEvent = async (id) => { if (!confirm('Delete?')) return; try { await api.delete(`/admin/events/${id}`); fetch(); } catch (e) { toast.error(e.response?.data?.message || 'Error'); } };
onMounted(fetch);
</script>
