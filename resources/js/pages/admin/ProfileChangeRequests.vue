<template>
  <div>
    <div class="flex items-center justify-between mb-6">
      <h1 class="text-2xl font-bold text-gray-900">Profile Change Requests</h1>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Requested By</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Field</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Current</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Requested</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Reason</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
          <tr v-for="req in requests" :key="req.id">
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ req.user?.name }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ req.field_name }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ req.current_value || '-' }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ req.requested_value }}</td>
            <td class="px-6 py-4 text-sm text-gray-500 max-w-xs truncate">{{ req.reason || '-' }}</td>
            <td class="px-6 py-4 whitespace-nowrap">
              <span :class="statusClass(req.status)" class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full">{{ req.status }}</span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm">
              <template v-if="req.status === 'pending'">
                <button @click="approve(req.id)" class="text-green-600 hover:text-green-900 mr-3">Approve</button>
                <button @click="reject(req.id)" class="text-red-600 hover:text-red-900">Reject</button>
              </template>
              <span v-else class="text-gray-400">-</span>
            </td>
          </tr>
          <tr v-if="!requests.length">
            <td colspan="7" class="px-6 py-12 text-center text-sm text-gray-500">No profile change requests</td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import api from '../../services/api';
import { useToast } from '../../composables/useToast';

const toast = useToast();

const requests = ref([]);

const statusClass = (s) => ({
  'bg-yellow-100 text-yellow-800': s === 'pending',
  'bg-green-100 text-green-800': s === 'approved',
  'bg-red-100 text-red-800': s === 'rejected',
});

const fetch = async () => { try { const r = await api.get('/admin/profile-change-requests'); requests.value = r.data.data || r.data; } catch (e) { toast.error('Failed to load requests'); } };
const approve = async (id) => { if (!confirm('Approve this change?')) return; try { await api.post(`/admin/profile-change-requests/${id}/approve`); fetch(); } catch (e) { toast.error(e.response?.data?.message || 'Error'); } };
const reject = async (id) => { if (!confirm('Reject this change?')) return; try { await api.post(`/admin/profile-change-requests/${id}/reject`); fetch(); } catch (e) { toast.error(e.response?.data?.message || 'Error'); } };
onMounted(fetch);
</script>
