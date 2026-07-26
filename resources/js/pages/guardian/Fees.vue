<template>
  <div>
    <div class="flex items-center justify-between mb-6">
      <h1 class="text-2xl font-bold text-gray-900">Fees</h1>
      <select v-model="studentId" @change="fetchFees" class="rounded-md border-gray-300 text-sm">
        <option value="">Select Child</option>
        <option v-for="child in children" :key="child.id" :value="child.id">{{ child.first_name }} {{ child.last_name }}</option>
      </select>
    </div>

    <div v-if="studentId">
      <!-- Summary -->
      <div class="grid grid-cols-3 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow p-4 text-center">
          <p class="text-2xl font-bold text-indigo-600">{{ formatCurrency(summary.total) }}</p>
          <p class="text-sm text-gray-500">Total Fees</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4 text-center">
          <p class="text-2xl font-bold text-green-600">{{ formatCurrency(summary.paid) }}</p>
          <p class="text-sm text-gray-500">Paid</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4 text-center">
          <p class="text-2xl font-bold text-red-600">{{ formatCurrency(summary.due) }}</p>
          <p class="text-sm text-gray-500">Due</p>
        </div>
      </div>

      <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fee Type</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Amount</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Paid</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Due</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Due Date</th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            <tr v-for="f in fees" :key="f.id">
              <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ f.fee_structure?.name || f.name || 'Fee' }}</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ formatCurrency(f.amount) }}</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-green-600">{{ formatCurrency(f.paid_amount || 0) }}</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-red-600">{{ formatCurrency(f.due_amount || f.amount) }}</td>
              <td class="px-6 py-4 whitespace-nowrap">
                <span :class="f.status === 'paid' ? 'bg-green-100 text-green-800' : f.status === 'partial' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800'" class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full capitalize">{{ f.status }}</span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ f.due_date || '-' }}</td>
            </tr>
            <tr v-if="!fees.length">
              <td colspan="6" class="px-6 py-12 text-center text-sm text-gray-500">No fee records found</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <div v-else class="bg-white rounded-lg shadow p-12 text-center">
      <p class="text-gray-500">Select a child to view fees.</p>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import api from '../../services/api';
import { useRoute } from 'vue-router';

const route = useRoute();
const children = ref([]);
const fees = ref([]);
const summary = ref({});
const studentId = ref('');

const formatCurrency = (val) => {
  return new Intl.NumberFormat('en-US', { style: 'currency', currency: 'USD' }).format(val || 0);
};

const fetchFees = async () => {
  if (!studentId.value) return;
  try {
    const r = await api.get(`/guardian/fees/${studentId.value}`);
    fees.value = r.data.data || r.data.fees || [];
    summary.value = r.data.summary || {};
  } catch (e) {
    console.error(e);
  }
};

onMounted(async () => {
  try {
    const r = await api.get('/guardian/children');
    children.value = r.data.data || r.data || [];
    if (route.params.studentId) {
      studentId.value = route.params.studentId;
      fetchFees();
    }
  } catch (e) {
    console.error(e);
  }
});
</script>
