<template>
  <div>
    <div class="flex items-center justify-between mb-6">
      <h1 class="text-2xl font-bold text-gray-900">Exam Routines</h1>
      <button @click="openModal()" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 text-sm font-medium">Add Exam Routine</button>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Exam Name</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Class</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Subject</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Start</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">End</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Room</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
          <tr v-for="er in examRoutines" :key="er.id">
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ er.exam_name }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ er.class?.name }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ er.subject }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ er.date }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ er.start_time }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ er.end_time }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ er.room || '-' }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm">
              <button @click="openModal(er)" class="text-indigo-600 hover:text-indigo-900 mr-3">Edit</button>
              <button @click="deleteExamRoutine(er.id)" class="text-red-600 hover:text-red-900">Delete</button>
            </td>
          </tr>
          <tr v-if="!examRoutines.length">
            <td colspan="8" class="px-6 py-12 text-center text-sm text-gray-500">No exam routines found</td>
          </tr>
        </tbody>
      </table>
    </div>

    <div v-if="showModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center z-50">
      <div class="bg-white rounded-lg shadow-xl max-w-lg w-full mx-4 p-6">
        <h2 class="text-lg font-semibold mb-4">{{ editing ? 'Edit' : 'Add' }} Exam Routine</h2>
        <form @submit.prevent="save">
          <div class="space-y-4">
            <div class="grid grid-cols-2 gap-4">
              <div><label class="block text-sm font-medium text-gray-700 mb-1">Exam Name</label><input v-model="form.exam_name" required class="w-full rounded-md border-gray-300 text-sm" /></div>
              <div><label class="block text-sm font-medium text-gray-700 mb-1">Class</label><select v-model="form.class_id" required class="w-full rounded-md border-gray-300 text-sm"><option value="">Select...</option><option v-for="cls in classes" :key="cls.id" :value="cls.id">{{ cls.name }}</option></select></div>
            </div>
            <div class="grid grid-cols-2 gap-4">
              <div><label class="block text-sm font-medium text-gray-700 mb-1">Subject</label><input v-model="form.subject" required class="w-full rounded-md border-gray-300 text-sm" /></div>
              <div><label class="block text-sm font-medium text-gray-700 mb-1">Date</label><input type="date" v-model="form.date" required class="w-full rounded-md border-gray-300 text-sm" /></div>
            </div>
            <div class="grid grid-cols-3 gap-4">
              <div><label class="block text-sm font-medium text-gray-700 mb-1">Start</label><input type="time" v-model="form.start_time" required class="w-full rounded-md border-gray-300 text-sm" /></div>
              <div><label class="block text-sm font-medium text-gray-700 mb-1">End</label><input type="time" v-model="form.end_time" required class="w-full rounded-md border-gray-300 text-sm" /></div>
              <div><label class="block text-sm font-medium text-gray-700 mb-1">Room</label><input v-model="form.room" class="w-full rounded-md border-gray-300 text-sm" /></div>
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

const examRoutines = ref([]);
const classes = ref([]);
const showModal = ref(false);
const editing = ref(null);
const saving = ref(false);
const form = ref({ exam_name: '', class_id: '', subject: '', date: '', start_time: '', end_time: '', room: '' });

const fetchClasses = async () => { try { const r = await api.get('/admin/classes', { params: { per_page: 100 } }); classes.value = r.data.data || r.data; } catch (e) { console.error(e); } };
const fetch = async () => { try { const r = await api.get('/admin/exam-routines'); examRoutines.value = r.data.data || r.data; } catch (e) { console.error(e); } };
const openModal = (er = null) => {
  if (er) { editing.value = er.id; form.value = { exam_name: er.exam_name, class_id: er.class_id, subject: er.subject, date: er.date, start_time: er.start_time, end_time: er.end_time, room: er.room || '' }; }
  else { editing.value = null; form.value = { exam_name: '', class_id: '', subject: '', date: '', start_time: '', end_time: '', room: '' }; }
  showModal.value = true;
};
const save = async () => {
  saving.value = true;
  try {
    if (editing.value) await api.put(`/admin/exam-routines/${editing.value}`, form.value);
    else await api.post('/admin/exam-routines', form.value);
    showModal.value = false; fetch();
  } catch (e) { alert(e.response?.data?.message || 'Error saving'); }
  finally { saving.value = false; }
};
const deleteExamRoutine = async (id) => { if (!confirm('Delete?')) return; try { await api.delete(`/admin/exam-routines/${id}`); fetch(); } catch (e) { alert(e.response?.data?.message || 'Error'); } };
onMounted(() => { fetchClasses(); fetch(); });
</script>
