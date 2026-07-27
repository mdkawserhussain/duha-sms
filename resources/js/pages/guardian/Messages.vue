<template>
  <div>
    <div class="flex items-center justify-between mb-6">
      <h1 class="text-2xl font-bold text-gray-900">Messages</h1>
      <button @click="openCompose" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 text-sm font-medium">Compose</button>
    </div>

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
          <tr v-for="m in messages" :key="m.id" :class="!m.read_at ? 'bg-indigo-50' : ''">
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ activeTab === 'inbox' ? (m.sender?.name || 'System') : (m.recipient?.name || '-') }}</td>
            <td class="px-6 py-4 text-sm text-gray-900 max-w-xs truncate">{{ m.subject }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ formatDate(m.created_at) }}</td>
            <td class="px-6 py-4 whitespace-nowrap">
              <span :class="m.read_at ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800'" class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full">{{ m.read_at ? 'Read' : 'Unread' }}</span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm">
              <button @click="viewMessage(m)" class="text-indigo-600 hover:text-indigo-900 mr-3">View</button>
              <button v-if="activeTab === 'inbox' && !m.read_at" @click="markRead(m.id)" class="text-green-600 hover:text-green-900 mr-3">Mark Read</button>
              <button @click="deleteMessage(m.id)" class="text-red-600 hover:text-red-900">Delete</button>
            </td>
          </tr>
          <tr v-if="!messages.length">
            <td colspan="5" class="px-6 py-12 text-center text-sm text-gray-500">No messages</td>
          </tr>
        </tbody>
      </table>
      <div v-if="lastPage > 1" class="bg-white px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6">
        <p class="text-sm text-gray-700">Page <span class="font-medium">{{ page }}</span> of <span class="font-medium">{{ lastPage }}</span></p>
        <div class="flex gap-2">
          <button @click="page > 1 && (page--, fetchMessages())" :disabled="page <= 1" class="px-3 py-1 border rounded text-sm disabled:opacity-50">Prev</button>
          <button @click="page < lastPage && (page++, fetchMessages())" :disabled="page >= lastPage" class="px-3 py-1 border rounded text-sm disabled:opacity-50">Next</button>
        </div>
      </div>
    </div>

    <!-- Compose Modal -->
    <div v-if="showCompose" class="fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center z-50">
      <div class="bg-white rounded-lg shadow-xl max-w-lg w-full mx-4">
        <div class="px-6 py-4 border-b border-gray-200">
          <h2 class="text-lg font-semibold text-gray-900">New Message</h2>
        </div>
        <form @submit.prevent="sendMessage">
          <div class="p-6 space-y-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Recipient Role</label>
              <select v-model="form.recipient_role" class="w-full rounded-md border-gray-300 text-sm">
                <option value="admin">Admin</option>
                <option value="teacher">Teacher</option>
              </select>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Recipient ID</label>
              <input v-model="form.recipient_id" type="number" required class="w-full rounded-md border-gray-300 text-sm" placeholder="Enter recipient user ID">
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Subject</label>
              <input v-model="form.subject" required class="w-full rounded-md border-gray-300 text-sm">
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Message</label>
              <textarea v-model="form.body" rows="4" required class="w-full rounded-md border-gray-300 text-sm"></textarea>
            </div>
          </div>
          <div class="px-6 py-4 border-t border-gray-200 flex justify-end gap-3">
            <button type="button" @click="showCompose = false" class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">Cancel</button>
            <button type="submit" :disabled="saving" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 text-sm font-medium disabled:opacity-50">{{ saving ? 'Sending...' : 'Send' }}</button>
          </div>
        </form>
      </div>
    </div>

    <!-- View Modal -->
    <div v-if="viewing" class="fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center z-50">
      <div class="bg-white rounded-lg shadow-xl max-w-lg w-full mx-4">
        <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
          <h2 class="text-lg font-semibold text-gray-900">{{ viewing.subject }}</h2>
          <button @click="viewing = null" class="text-gray-400 hover:text-gray-600">&times;</button>
        </div>
        <div class="p-6">
          <div class="text-sm text-gray-500 mb-4">
            <p>From: <span class="font-medium text-gray-900">{{ viewing.sender?.name || 'System' }}</span></p>
            <p>Date: <span class="font-medium text-gray-900">{{ formatDate(viewing.created_at) }}</span></p>
          </div>
          <div class="text-sm text-gray-700 whitespace-pre-wrap">{{ viewing.body }}</div>
        </div>
        <div class="px-6 py-4 border-t border-gray-200 flex justify-end">
          <button @click="viewing = null" class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">Close</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue';
import api from '../../services/api';
import { useToast } from '../../composables/useToast';

const toast = useToast();

const messages = ref([]);
const page = ref(1);
const lastPage = ref(1);
const activeTab = ref('inbox');
const showCompose = ref(false);
const viewing = ref(null);
const saving = ref(false);
const form = ref({ recipient_role: 'admin', recipient_id: '', subject: '', body: '' });

const formatDate = (date) => {
  return new Date(date).toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit' });
};

const fetchMessages = async () => {
  try {
    const r = await api.get('/guardian/messages', { params: { page: page.value, type: activeTab.value } });
    messages.value = r.data.data || [];
    lastPage.value = r.data.last_page || 1;
  } catch (e) {
    toast.error('Failed to load messages');
  }
};

const openCompose = () => {
  form.value = { recipient_role: 'admin', recipient_id: '', subject: '', body: '' };
  showCompose.value = true;
};

const sendMessage = async () => {
  saving.value = true;
  try {
    await api.post('/guardian/messages', form.value);
    showCompose.value = false;
    fetchMessages();
  } catch (e) {
    toast.error(e.response?.data?.message || 'Error sending message');
  } finally {
    saving.value = false;
  }
};

const viewMessage = async (m) => {
  viewing.value = m;
  if (!m.read_at && activeTab.value === 'inbox') {
    try {
      await api.post(`/guardian/messages/${m.id}/read`);
      m.read_at = new Date().toISOString();
    } catch (e) {
      toast.error('Failed to mark as read');
    }
  }
};

const markRead = async (id) => {
  try {
    await api.post(`/guardian/messages/${id}/read`);
    const m = messages.value.find(m => m.id === id);
    if (m) m.read_at = new Date().toISOString();
  } catch (e) {
    toast.error(e.response?.data?.message || 'Error');
  }
};

const deleteMessage = async (id) => {
  if (!confirm('Are you sure you want to delete this message?')) return;
  try {
    await api.delete(`/guardian/messages/${id}`);
    fetchMessages();
  } catch (e) {
    toast.error(e.response?.data?.message || 'Error deleting message');
  }
};

watch(activeTab, () => {
  page.value = 1;
  fetchMessages();
});

onMounted(fetchMessages);
</script>
