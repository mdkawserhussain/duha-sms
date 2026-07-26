<template>
  <div>
    <div class="flex items-center justify-between mb-6">
      <h1 class="text-2xl font-bold text-gray-900">Report Cards</h1>
      <select v-model="studentId" @change="fetchReportCards" class="rounded-md border-gray-300 text-sm">
        <option value="">Select Child</option>
        <option v-for="child in children" :key="child.id" :value="child.id">{{ child.first_name }} {{ child.last_name }}</option>
      </select>
    </div>

    <div v-if="studentId" class="space-y-4">
      <div v-for="rc in reportCards" :key="rc.id" class="bg-white rounded-lg shadow overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 bg-indigo-50 flex items-center justify-between">
          <div>
            <h3 class="font-semibold text-gray-900">{{ rc.term }} - {{ rc.academic_year || '2024' }}</h3>
            <p class="text-sm text-gray-600">Class: {{ rc.class?.name || '-' }}</p>
          </div>
          <div class="text-right">
            <p class="text-2xl font-bold text-indigo-600">{{ rc.average || '-' }}</p>
            <p class="text-xs text-gray-500">Average</p>
          </div>
        </div>
        <div class="p-6">
          <div v-if="rc.marks?.length" class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
              <thead class="bg-gray-50">
                <tr>
                  <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Subject</th>
                  <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Marks</th>
                  <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Grade</th>
                  <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Comments</th>
                </tr>
              </thead>
              <tbody class="bg-white divide-y divide-gray-200">
                <tr v-for="m in rc.marks" :key="m.id">
                  <td class="px-4 py-2 text-sm font-medium text-gray-900">{{ m.subject?.name || m.subject }}</td>
                  <td class="px-4 py-2 text-sm text-gray-500">{{ m.marks }}</td>
                  <td class="px-4 py-2 text-sm text-gray-500">{{ m.grade || '-' }}</td>
                  <td class="px-4 py-2 text-sm text-gray-500">{{ m.comments || '-' }}</td>
                </tr>
              </tbody>
            </table>
          </div>
          <div v-if="rc.comments" class="mt-4 p-3 bg-gray-50 rounded">
            <p class="text-sm font-medium text-gray-700">Overall Comments:</p>
            <p class="text-sm text-gray-600">{{ rc.comments }}</p>
          </div>
        </div>
      </div>

      <div v-if="!reportCards.length" class="bg-white rounded-lg shadow p-12 text-center">
        <p class="text-gray-500">No report cards found.</p>
      </div>
    </div>

    <div v-else class="bg-white rounded-lg shadow p-12 text-center">
      <p class="text-gray-500">Select a child to view report cards.</p>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import api from '../../services/api';
import { useRoute } from 'vue-router';

const route = useRoute();
const children = ref([]);
const reportCards = ref([]);
const studentId = ref('');

const fetchReportCards = async () => {
  if (!studentId.value) return;
  try {
    const r = await api.get(`/guardian/report-cards/${studentId.value}`);
    reportCards.value = r.data.data || r.data.report_cards || [];
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
      fetchReportCards();
    }
  } catch (e) {
    console.error(e);
  }
});
</script>
