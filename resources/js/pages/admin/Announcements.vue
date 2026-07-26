<template>
  <div>
    <div class="flex items-center justify-between mb-6">
      <h1 class="text-2xl font-bold text-gray-900">Announcements</h1>
      <button @click="openModal()" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 text-sm font-medium">Create Announcement</button>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Title</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Target</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Published</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
          <tr v-for="a in announcements" :key="a.id">
            <td class="px-6 py-4 text-sm font-medium text-gray-900 max-w-xs truncate">{{ a.title }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ a.target_audience || 'All' }}</td>
            <td class="px-6 py-4 whitespace-nowrap">
              <span :class="a.is_published ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'" class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full">{{ a.is_published ? 'Published' : 'Draft' }}</span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ a.published_at ? new Date(a.published_at).toLocaleDateString() : '-' }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm">
              <button @click="openModal(a)" class="text-indigo-600 hover:text-indigo-900 mr-3">Edit</button>
              <button v-if="!a.is_published" @click="publish(a.id)" class="text-green-600 hover:text-green-900 mr-3">Publish</button>
              <button @click="deleteAnnouncement(a.id)" class="text-red-600 hover:text-red-900">Delete</button>
            </td>
          </tr>
          <tr v-if="!announcements.length">
            <td colspan="5" class="px-6 py-12 text-center text-sm text-gray-500">No announcements found</td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Modal -->
    <div v-if="showModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center z-50">
      <div class="bg-white rounded-lg shadow-xl max-w-lg w-full mx-4 p-6">
        <h2 class="text-lg font-semibold mb-4">{{ editing ? 'Edit' : 'Create' }} Announcement</h2>
        <form @submit.prevent="save">
          <div class="space-y-4">
            <div><label class="block text-sm font-medium text-gray-700 mb-1">Title</label><input v-model="form.title" required class="w-full rounded-md border-gray-300 text-sm" /></div>
            <div><label class="block text-sm font-medium text-gray-700 mb-1">Content</label><textarea v-model="form.content" rows="4" required class="w-full rounded-md border-gray-300 text-sm"></textarea></div>
            <div><label class="block text-sm font-medium text-gray-700 mb-1">Target Audience</label><select v-model="form.target_audience" class="w-full rounded-md border-gray-300 text-sm"><option value="all">All</option><option value="teachers">Teachers</option><option value="guardians">Guardians</option><option value="students">Students</option></select></div>
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

const announcements = ref([]);
const showModal = ref(false);
const editing = ref(null);
const saving = ref(false);
const form = ref({ title: '', content: '', target_audience: 'all' });

const fetch = async () => { try { const r = await api.get('/admin/announcements'); announcements.value = r.data.data || r.data; } catch (e) { console.error(e); } };
const openModal = (a = null) => {
  if (a) { editing.value = a.id; form.value = { title: a.title, content: a.content, target_audience: a.target_audience || 'all' }; }
  else { editing.value = null; form.value = { title: '', content: '', target_audience: 'all' }; }
  showModal.value = true;
};
const save = async () => {
  saving.value = true;
  try {
    if (editing.value) await api.put(`/admin/announcements/${editing.value}`, form.value);
    else await api.post('/admin/announcements', form.value);
    showModal.value = false; fetch();
  } catch (e) { alert(e.response?.data?.message || 'Error saving'); }
  finally { saving.value = false; }
};
const publish = async (id) => { try { await api.post(`/admin/announcements/${id}/publish`); fetch(); } catch (e) { alert(e.response?.data?.message || 'Error'); } };
const deleteAnnouncement = async (id) => { if (!confirm('Delete?')) return; try { await api.delete(`/admin/announcements/${id}`); fetch(); } catch (e) { alert(e.response?.data?.message || 'Error'); } };
onMounted(fetch);
</script>
