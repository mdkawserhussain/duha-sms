<template>
  <div>
    <div class="flex items-center justify-between mb-6">
      <h1 class="text-2xl font-bold text-gray-900">Diary</h1>
      <button @click="openModal()" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 text-sm font-medium">Add Entry</button>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow p-4 mb-6 flex flex-wrap gap-4 items-end">
      <div><label class="block text-sm font-medium text-gray-700 mb-1">Class</label><select v-model="filters.class_id" @change="fetchDiary" class="rounded-md border-gray-300 text-sm"><option value="">All Classes</option><option v-for="cls in classes" :key="cls.id" :value="cls.id">{{ cls.name }}</option></select></div>
    </div>

    <!-- Diary List -->
    <div class="space-y-4">
      <div v-for="entry in diary" :key="entry.id" class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center justify-between mb-2">
          <div class="flex items-center gap-3">
            <span class="px-2 py-1 bg-indigo-100 text-indigo-800 text-xs font-semibold rounded-full">{{ entry.class?.name || '-' }}</span>
            <span class="text-sm text-gray-500">{{ new Date(entry.date).toLocaleDateString() }}</span>
          </div>
          <div class="flex gap-2">
            <button @click="openModal(entry)" class="text-indigo-600 hover:text-indigo-900 text-sm">Edit</button>
            <button @click="deleteEntry(entry.id)" class="text-red-600 hover:text-red-900 text-sm">Delete</button>
          </div>
        </div>
        <h3 class="font-semibold text-gray-900 mb-1">{{ entry.subject }}</h3>
        <p class="text-sm text-gray-600">{{ entry.content }}</p>
      </div>
      <div v-if="!diary.length" class="text-center py-12 text-gray-500">No diary entries</div>
    </div>

    <!-- Modal -->
    <div v-if="showModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center z-50">
      <div class="bg-white rounded-lg shadow-xl max-w-lg w-full mx-4 p-6">
        <div class="flex items-center justify-between mb-4">
          <h2 class="text-lg font-semibold">{{ editing ? 'Edit' : 'Add' }} Diary Entry</h2>
          <button @click="showModal = false" class="text-gray-400 hover:text-gray-600">&times;</button>
        </div>
        <form @submit.prevent="saveEntry">
          <div class="space-y-4">
            <div><label class="block text-sm font-medium text-gray-700 mb-1">Class</label><select v-model="form.class_id" required class="w-full rounded-md border-gray-300 text-sm"><option value="">Select class</option><option v-for="cls in classes" :key="cls.id" :value="cls.id">{{ cls.name }}</option></select></div>
            <div><label class="block text-sm font-medium text-gray-700 mb-1">Date</label><input type="date" v-model="form.date" required class="w-full rounded-md border-gray-300 text-sm" /></div>
            <div><label class="block text-sm font-medium text-gray-700 mb-1">Subject</label><input v-model="form.subject" required class="w-full rounded-md border-gray-300 text-sm" /></div>
            <div><label class="block text-sm font-medium text-gray-700 mb-1">Content</label><textarea v-model="form.content" rows="4" required class="w-full rounded-md border-gray-300 text-sm"></textarea></div>
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

const classes = ref([]);
const diary = ref([]);
const filters = ref({ class_id: '' });
const showModal = ref(false);
const editing = ref(null);
const saving = ref(false);
const form = ref({ class_id: '', date: '', subject: '', content: '' });

const fetchDiary = async () => {
  try { const params = {}; if (filters.value.class_id) params.class_id = filters.value.class_id; const r = await api.get('/teacher/diary', { params }); diary.value = r.data.data || r.data || []; } catch (e) { console.error(e); }
};
const openModal = (entry = null) => {
  editing.value = entry;
  form.value = entry ? { class_id: entry.class_id, date: entry.date, subject: entry.subject, content: entry.content } : { class_id: '', date: '', subject: '', content: '' };
  showModal.value = true;
};
const saveEntry = async () => {
  saving.value = true;
  try {
    if (editing.value) { await api.put(`/teacher/diary/${editing.value.id}`, form.value); }
    else { await api.post('/teacher/diary', form.value); }
    showModal.value = false; fetchDiary();
  } catch (e) { alert(e.response?.data?.message || 'Error'); }
  finally { saving.value = false; }
};
const deleteEntry = async (id) => { if (!confirm('Delete?')) return; try { await api.delete(`/teacher/diary/${id}`); fetchDiary(); } catch (e) { alert(e.response?.data?.message || 'Error'); } };
onMounted(async () => {
  try { const r = await api.get('/teacher/classes'); classes.value = r.data.data || r.data || []; } catch (e) { console.error(e); }
  fetchDiary();
});
</script>
