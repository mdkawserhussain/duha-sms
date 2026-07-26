<template>
  <div>
    <h1 class="text-2xl font-bold text-gray-900 mb-6">Profile Change Request</h1>

    <div class="max-w-2xl">
      <div class="bg-white rounded-lg shadow p-6 mb-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Submit a Change Request</h2>
        <form @submit.prevent="submitRequest">
          <div class="space-y-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Request Type</label>
              <select v-model="form.type" required class="w-full rounded-md border-gray-300 text-sm">
                <option value="">Select type...</option>
                <option value="phone">Phone Number</option>
                <option value="email">Email Address</option>
                <option value="address">Address</option>
                <option value="emergency_contact">Emergency Contact</option>
                <option value="other">Other</option>
              </select>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Child</label>
              <select v-model="form.student_id" required class="w-full rounded-md border-gray-300 text-sm">
                <option value="">Select child...</option>
                <option v-for="child in children" :key="child.id" :value="child.id">{{ child.first_name }} {{ child.last_name }}</option>
              </select>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Current Value</label>
              <input v-model="form.current_value" required class="w-full rounded-md border-gray-300 text-sm" placeholder="Current value">
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Requested Value</label>
              <input v-model="form.requested_value" required class="w-full rounded-md border-gray-300 text-sm" placeholder="New value">
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Reason</label>
              <textarea v-model="form.reason" rows="3" required class="w-full rounded-md border-gray-300 text-sm" placeholder="Why do you need this change?"></textarea>
            </div>
          </div>
          <div class="mt-6">
            <button type="submit" :disabled="saving" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 text-sm font-medium disabled:opacity-50">{{ saving ? 'Submitting...' : 'Submit Request' }}</button>
          </div>
        </form>
      </div>

      <!-- Previous Requests -->
      <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
          <h2 class="text-lg font-semibold text-gray-900">Previous Requests</h2>
        </div>
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Child</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Change</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            <tr v-for="req in requests" :key="req.id">
              <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 capitalize">{{ req.type?.replace('_', ' ') }}</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ req.student?.first_name }} {{ req.student?.last_name }}</td>
              <td class="px-6 py-4 text-sm text-gray-500">
                <span class="line-through text-red-400">{{ req.current_value }}</span>
                <span class="mx-1">&rarr;</span>
                <span class="text-green-600">{{ req.requested_value }}</span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <span :class="statusClass(req.status)" class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full capitalize">{{ req.status }}</span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ formatDate(req.created_at) }}</td>
            </tr>
            <tr v-if="!requests.length">
              <td colspan="5" class="px-6 py-12 text-center text-sm text-gray-500">No requests yet</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import api from '../../services/api';

const children = ref([]);
const requests = ref([]);
const saving = ref(false);
const form = ref({ type: '', student_id: '', current_value: '', requested_value: '', reason: '' });

const formatDate = (date) => {
  return new Date(date).toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' });
};

const statusClass = (status) => {
  const classes = {
    'pending': 'bg-yellow-100 text-yellow-800',
    'approved': 'bg-green-100 text-green-800',
    'rejected': 'bg-red-100 text-red-800'
  };
  return classes[status] || 'bg-gray-100 text-gray-800';
};

const fetchChildren = async () => {
  try {
    const r = await api.get('/guardian/children');
    children.value = r.data.data || r.data || [];
  } catch (e) {
    console.error(e);
  }
};

const fetchRequests = async () => {
  try {
    const r = await api.get('/guardian/profile-change-request');
    requests.value = r.data.data || r.data || [];
  } catch (e) {
    console.error(e);
  }
};

const submitRequest = async () => {
  saving.value = true;
  try {
    await api.post('/guardian/profile-change-request', form.value);
    form.value = { type: '', student_id: '', current_value: '', requested_value: '', reason: '' };
    fetchRequests();
    alert('Request submitted successfully!');
  } catch (e) {
    alert(e.response?.data?.message || 'Error submitting request');
  } finally {
    saving.value = false;
  }
};

onMounted(() => {
  fetchChildren();
  fetchRequests();
});
</script>
