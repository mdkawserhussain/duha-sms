<template>
  <div>
    <div class="flex items-center justify-between mb-6">
      <h1 class="text-2xl font-bold text-gray-900">Evaluations</h1>
      <select v-model="studentId" @change="fetchEvaluations" class="rounded-md border-gray-300 text-sm">
        <option value="">Select Child</option>
        <option v-for="child in children" :key="child.id" :value="child.id">{{ child.name }}</option>
      </select>
    </div>

    <div v-if="studentId">
      <!-- Summary -->
      <div class="grid grid-cols-3 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow p-4 text-center">
          <p class="text-2xl font-bold text-indigo-600">{{ summary.average || '-' }}</p>
          <p class="text-sm text-gray-500">Average Marks</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4 text-center">
          <p class="text-2xl font-bold text-green-600">{{ summary.highest || '-' }}</p>
          <p class="text-sm text-gray-500">Highest</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4 text-center">
          <p class="text-2xl font-bold text-red-600">{{ summary.lowest || '-' }}</p>
          <p class="text-sm text-gray-500">Lowest</p>
        </div>
      </div>

      <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Subject</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Term</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Marks</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Grade</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Comments</th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            <tr v-for="e in evaluations" :key="e.id">
              <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ e.subject?.name || e.subject }}</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ e.term }}</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ e.marks }}</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ e.grade || '-' }}</td>
              <td class="px-6 py-4 text-sm text-gray-500 max-w-xs truncate">{{ e.comments || '-' }}</td>
            </tr>
            <tr v-if="!evaluations.length">
              <td colspan="5" class="px-6 py-12 text-center text-sm text-gray-500">No evaluations found</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <div v-else class="bg-white rounded-lg shadow p-12 text-center">
      <p class="text-gray-500">Select a child to view evaluations.</p>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import api from '../../services/api';
import { useRoute } from 'vue-router';
import { useToast } from '../../composables/useToast';

const toast = useToast();

const route = useRoute();
const children = ref([]);
const evaluations = ref([]);
const summary = ref({});
const studentId = ref('');

const fetchEvaluations = async () => {
  if (!studentId.value) return;
  try {
    const r = await api.get(`/guardian/evaluations/${studentId.value}`);
    evaluations.value = r.data.data || r.data.evaluations || [];
    summary.value = r.data.summary || {};
  } catch (e) {
    toast.error('Failed to load evaluations');
  }
};

const fetchChildren = async () => {
  try {
    const r = await api.get('/guardian/children');
    children.value = r.data.data || r.data || [];
    if (route.params.studentId) {
      studentId.value = route.params.studentId;
      fetchEvaluations();
    }
  } catch (e) {
    toast.error('Failed to load children');
  }
};

onMounted(() => { fetchChildren(); });
</script>
