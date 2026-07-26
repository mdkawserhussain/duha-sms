<template>
  <div>
    <div class="flex items-center justify-between mb-6">
      <h1 class="text-2xl font-bold text-gray-900">Activity Log</h1>
      <div class="flex gap-3">
        <select v-model="filters.type" @change="fetchLog" class="rounded-md border-gray-300 text-sm">
          <option value="">All Types</option>
          <option value="login">Login</option>
          <option value="create">Create</option>
          <option value="update">Update</option>
          <option value="delete">Delete</option>
        </select>
      </div>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">User</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Description</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">IP Address</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Time</th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
          <tr v-for="log in logs" :key="log.id">
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ log.user?.name || '-' }}</td>
            <td class="px-6 py-4 whitespace-nowrap">
              <span :class="typeClass(log.type)" class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full">{{ log.type }}</span>
            </td>
            <td class="px-6 py-4 text-sm text-gray-500 max-w-md truncate">{{ log.description }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ log.ip_address || '-' }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ new Date(log.created_at).toLocaleString() }}</td>
          </tr>
          <tr v-if="!logs.length">
            <td colspan="5" class="px-6 py-12 text-center text-sm text-gray-500">No activity logs found</td>
          </tr>
        </tbody>
      </table>
      <div v-if="meta.last_page > 1" class="bg-white px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6">
        <p class="text-sm text-gray-700">Page {{ meta.current_page }} of {{ meta.last_page }}</p>
        <div class="flex gap-2">
          <button @click="page--; fetchLog()" :disabled="page <= 1" class="px-3 py-1 border rounded text-sm disabled:opacity-50">Prev</button>
          <button @click="page++; fetchLog()" :disabled="page >= meta.last_page" class="px-3 py-1 border rounded text-sm disabled:opacity-50">Next</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import api from '../../services/api';

const logs = ref([]);
const meta = ref({ current_page: 1, last_page: 1 });
const page = ref(1);
const filters = ref({ type: '' });

const typeClass = (t) => ({
  'bg-blue-100 text-blue-800': t === 'login',
  'bg-green-100 text-green-800': t === 'create',
  'bg-yellow-100 text-yellow-800': t === 'update',
  'bg-red-100 text-red-800': t === 'delete',
});

const fetchLog = async () => {
  try {
    const params = { page: page.value };
    if (filters.value.type) params.type = filters.value.type;
    const r = await api.get('/admin/activity-log', { params });
    logs.value = r.data.data || [];
    meta.value = r.data.meta || { current_page: 1, last_page: 1 };
  } catch (e) { console.error(e); }
};
onMounted(fetchLog);
</script>
