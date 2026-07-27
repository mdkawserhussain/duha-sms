<template>
  <div>
    <div class="flex items-center justify-between mb-6">
      <h1 class="text-2xl font-bold text-gray-900">Messages</h1>
      <div class="flex gap-3">
        <span v-if="unreadCount > 0" class="px-3 py-1 bg-red-100 text-red-700 rounded-full text-sm font-medium">{{ unreadCount }} unread</span>
        <button @click="openModal()" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 text-sm font-medium">Compose</button>
      </div>
    </div>

    <!-- Tabs -->
    <div class="flex border-b border-gray-200 mb-6">
      <button @click="activeTab = 'inbox'" :class="activeTab === 'inbox' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500'" class="px-4 py-2 border-b-2 text-sm font-medium">Inbox</button>
      <button @click="activeTab = 'sent'" :class="activeTab === 'sent' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500'" class="px-4 py-2 border-b-2 text-sm font-medium">Sent</button>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ activeTab === 'inbox' ? 'From' : 'To' }}</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Subject</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
          <tr v-for="m in messages" :key="m.id" :class="!m.is_read && activeTab === 'inbox' ? 'bg-indigo-50' : ''">
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
              <template v-if="activeTab === 'inbox'">{{ m.sender?.name || 'System' }}</template>
              <template v-else>{{ m.recipient?.name || '-' }}</template>
            </td>
            <td class="px-6 py-4 text-sm text-gray-900 max-w-xs truncate">{{ m.subject }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ new Date(m.created_at).toLocaleDateString() }}</td>
            <td class="px-6 py-4 whitespace-nowrap">
              <span v-if="activeTab === 'inbox'" :class="m.is_read ? 'bg-gray-100 text-gray-800' : 'bg-blue-100 text-blue-800'" class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full">{{ m.is_read ? 'Read' : 'Unread' }}</span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm">
              <button @click="viewMessage(m)" class="text-indigo-600 hover:text-indigo-900 mr-3">View</button>
              <button @click="deleteMessage(m.id)" class="text-red-600 hover:text-red-900">Delete</button>
            </td>
          </tr>
          <tr v-if="!messages.length">
            <td colspan="5" class="px-6 py-12 text-center text-sm text-gray-500">No messages</td>
          </tr>
        </tbody>
      </table>
      <div v-if="meta.last_page > 1" class="bg-white px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6">
        <p class="text-sm text-gray-700">Page {{ meta.current_page }} of {{ meta.last_page }}</p>
        <div class="flex gap-2">
          <button @click="page--; fetchMessages()" :disabled="page <= 1" class="px-3 py-1 border rounded text-sm disabled:opacity-50">Prev</button>
          <button @click="page++; fetchMessages()" :disabled="page >= meta.last_page" class="px-3 py-1 border rounded text-sm disabled:opacity-50">Next</button>
        </div>
      </div>
    </div>

    <!-- Compose Modal -->
    <div v-if="showCompose" class="fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center z-50">
      <div class="bg-white rounded-lg shadow-xl max-w-lg w-full mx-4 p-6">
        <h2 class="text-lg font-semibold mb-4">Compose Message</h2>
        <form @submit.prevent="sendMessage">
          <div class="space-y-4">
            <div><label class="block text-sm font-medium text-gray-700 mb-1">Recipient Role</label><select v-model="form.recipient_role" class="w-full rounded-md border-gray-300 text-sm"><option value="teacher">Teacher</option><option value="guardian">Guardian</option></select></div>
            <div><label class="block text-sm font-medium text-gray-700 mb-1">Recipient ID</label><input type="number" v-model="form.recipient_id" required placeholder="User ID" class="w-full rounded-md border-gray-300 text-sm" /></div>
            <div><label class="block text-sm font-medium text-gray-700 mb-1">Subject</label><input v-model="form.subject" required class="w-full rounded-md border-gray-300 text-sm" /></div>
            <div><label class="block text-sm font-medium text-gray-700 mb-1">Message</label><textarea v-model="form.body" rows="4" required class="w-full rounded-md border-gray-300 text-sm"></textarea></div>
          </div>
          <div class="mt-6 flex justify-end gap-3">
            <button type="button" @click="showCompose = false" class="px-4 py-2 border rounded-md text-sm">Cancel</button>
            <button type="submit" :disabled="saving" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 text-sm disabled:opacity-50">{{ saving ? 'Sending...' : 'Send' }}</button>
          </div>
        </form>
      </div>
    </div>

    <!-- View Modal -->
    <div v-if="viewing" class="fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center z-50">
      <div class="bg-white rounded-lg shadow-xl max-w-lg w-full mx-4 p-6">
        <div class="flex items-center justify-between mb-4">
          <h2 class="text-lg font-semibold">{{ viewing.subject }}</h2>
          <button @click="viewing = null" class="text-gray-400 hover:text-gray-600">&times;</button>
        </div>
        <p class="text-sm text-gray-500 mb-2">From: {{ viewing.sender?.name }} | {{ new Date(viewing.created_at).toLocaleString() }}</p>
        <p class="text-gray-700 whitespace-pre-wrap">{{ viewing.body }}</p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue';
import api from '../../services/api';
import { useToast } from '../../composables/useToast';

const toast = useToast();

const activeTab = ref('inbox');
const messages = ref([]);
const meta = ref({ current_page: 1, last_page: 1 });
const page = ref(1);
const unreadCount = ref(0);
const showCompose = ref(false);
const saving = ref(false);
const viewing = ref(null);
const form = ref({ recipient_role: 'teacher', recipient_id: '', subject: '', body: '' });

const fetchMessages = async () => {
  try {
    const params = { page: page.value };
    const r = await api.get('/admin/messages', { params });
    messages.value = r.data.data || [];
    meta.value = r.data.meta || { current_page: 1, last_page: 1 };
  } catch (e) { toast.error('Failed to load messages'); }
};
const fetchUnread = async () => { try { const r = await api.get('/admin/messages/unread-count'); unreadCount.value = r.data.count || 0; } catch (e) { toast.error('Failed to load unread count'); } };
watch(activeTab, () => { page.value = 1; fetchMessages(); });
const openModal = () => { form.value = { recipient_role: 'teacher', recipient_id: '', subject: '', body: '' }; showCompose.value = true; };
const sendMessage = async () => {
  saving.value = true;
  try { await api.post('/admin/messages', form.value); showCompose.value = false; fetchMessages(); fetchUnread(); } catch (e) { toast.error(e.response?.data?.message || 'Error'); }
  finally { saving.value = false; }
};
const viewMessage = async (m) => {
  viewing.value = m;
  if (!m.is_read && activeTab.value === 'inbox') {
    try { await api.post(`/admin/messages/${m.id}/read`); m.is_read = true; fetchUnread(); } catch (e) { toast.error('Failed to mark as read'); }
  }
};
const deleteMessage = async (id) => { if (!confirm('Delete?')) return; try { await api.delete(`/admin/messages/${id}`); fetchMessages(); } catch (e) { toast.error(e.response?.data?.message || 'Error'); } };
onMounted(() => { fetchMessages(); fetchUnread(); });
</script>
