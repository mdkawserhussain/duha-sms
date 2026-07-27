<template>
  <div>
    <div class="flex items-center justify-between mb-6">
      <h1 class="text-2xl font-bold text-gray-900">Student Applications</h1>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Applicant</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Guardian</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Class Applied</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
          <tr v-for="app in applications" :key="app.id">
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ app.name }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ app.guardian?.user?.name || '-' }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ app.class?.name || '-' }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ new Date(app.created_at).toLocaleDateString() }}</td>
            <td class="px-6 py-4 whitespace-nowrap">
              <span :class="statusClass(app.status)" class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full">{{ app.status }}</span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm">
              <template v-if="app.status === 'pending'">
                <button @click="approve(app.id)" class="text-green-600 hover:text-green-900 mr-3">Approve</button>
                <button @click="reject(app.id)" class="text-red-600 hover:text-red-900">Reject</button>
              </template>
              <span v-else class="text-gray-400">-</span>
            </td>
          </tr>
          <tr v-if="!applications.length">
            <td colspan="6" class="px-6 py-12 text-center text-sm text-gray-500">No applications found</td>
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

const applications = ref([]);

const statusClass = (s) => ({
  'bg-yellow-100 text-yellow-800': s === 'pending',
  'bg-green-100 text-green-800': s === 'approved',
  'bg-red-100 text-red-800': s === 'rejected',
});

const fetch = async () => { try { const r = await api.get('/admin/applications'); applications.value = r.data.data || r.data; } catch (e) { toast.error('Failed to load applications'); } };
const approve = async (id) => { if (!confirm('Approve this application?')) return; try { await api.post(`/admin/applications/${id}/approve`); fetch(); } catch (e) { toast.error(e.response?.data?.message || 'Error'); } };
const reject = async (id) => { if (!confirm('Reject this application?')) return; try { await api.post(`/admin/applications/${id}/reject`); fetch(); } catch (e) { toast.error(e.response?.data?.message || 'Error'); } };
onMounted(fetch);
</script>
