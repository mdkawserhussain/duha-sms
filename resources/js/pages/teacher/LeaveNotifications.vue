<template>
  <div>
    <h1 class="text-2xl font-bold text-gray-900 mb-6">Leave Notifications</h1>

    <div class="bg-white rounded-lg shadow overflow-hidden">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Student</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Class</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Leave Date</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Reason</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
          <tr v-for="leave in leaves" :key="leave.id">
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ leave.student?.name }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ leave.class?.name || '-' }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ leave.leave_date }}</td>
            <td class="px-6 py-4 text-sm text-gray-500 max-w-xs truncate">{{ leave.reason || '-' }}</td>
            <td class="px-6 py-4 whitespace-nowrap">
              <span :class="statusClass(leave.status)" class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full capitalize">{{ leave.status }}</span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm">
              <template v-if="leave.status === 'pending'">
                <button @click="approve(leave.id)" class="text-green-600 hover:text-green-900 mr-3">Approve</button>
                <button @click="reject(leave.id)" class="text-red-600 hover:text-red-900">Reject</button>
              </template>
              <span v-else class="text-gray-400">-</span>
            </td>
          </tr>
          <tr v-if="!leaves.length">
            <td colspan="6" class="px-6 py-12 text-center text-sm text-gray-500">No leave notifications</td>
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
const leaves = ref([]);

const statusClass = (status) => {
  const classes = {
    'pending': 'bg-yellow-100 text-yellow-800',
    'approved': 'bg-green-100 text-green-800',
    'rejected': 'bg-red-100 text-red-800'
  };
  return classes[status] || 'bg-gray-100 text-gray-800';
};

const fetchLeaves = async () => {
  try {
    const r = await api.get('/teacher/leave-notifications');
    leaves.value = r.data.data || r.data || [];
  } catch (e) {
    toast.error('Failed to load leave notifications');
  }
};

const approve = async (id) => {
  if (!confirm('Approve this leave request?')) return;
  try {
    await api.post(`/teacher/leave-notifications/${id}/approve`);
    fetchLeaves();
  } catch (e) {
    toast.error(e.response?.data?.message || 'Error approving leave');
  }
};

const reject = async (id) => {
  if (!confirm('Reject this leave request?')) return;
  try {
    await api.post(`/teacher/leave-notifications/${id}/reject`);
    fetchLeaves();
  } catch (e) {
    toast.error(e.response?.data?.message || 'Error rejecting leave');
  }
};

onMounted(fetchLeaves);
</script>
