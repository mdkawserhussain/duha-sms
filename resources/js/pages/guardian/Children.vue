<template>
  <div>
    <h1 class="text-2xl font-bold text-gray-900 mb-6">My Children</h1>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
      <div v-for="child in children" :key="child.id" class="bg-white rounded-lg shadow overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 bg-indigo-50">
          <div class="flex items-center">
            <div class="w-12 h-12 rounded-full bg-indigo-200 flex items-center justify-center">
              <span class="text-indigo-700 font-bold text-lg">{{ child.first_name?.charAt(0) }}{{ child.last_name?.charAt(0) }}</span>
            </div>
            <div class="ml-4">
              <h3 class="font-semibold text-gray-900">{{ child.first_name }} {{ child.last_name }}</h3>
              <p class="text-sm text-gray-600">Class: {{ child.class?.name || 'N/A' }}</p>
            </div>
          </div>
        </div>
        <div class="p-6">
          <div class="space-y-2 text-sm">
            <div class="flex justify-between">
              <span class="text-gray-500">Date of Birth:</span>
              <span class="text-gray-900">{{ child.date_of_birth || 'N/A' }}</span>
            </div>
            <div class="flex justify-between">
              <span class="text-gray-500">Gender:</span>
              <span class="text-gray-900 capitalize">{{ child.gender || 'N/A' }}</span>
            </div>
            <div class="flex justify-between">
              <span class="text-gray-500">Status:</span>
              <span :class="child.status === 'active' ? 'text-green-600' : 'text-red-600'" class="font-medium capitalize">{{ child.status }}</span>
            </div>
          </div>
          <div class="mt-4 flex gap-2 flex-wrap">
            <router-link :to="`/guardian/attendance/${child.id}`" class="px-3 py-1 bg-blue-100 text-blue-700 rounded-full text-xs font-medium hover:bg-blue-200">Attendance</router-link>
            <router-link :to="`/guardian/diary/${child.id}`" class="px-3 py-1 bg-green-100 text-green-700 rounded-full text-xs font-medium hover:bg-green-200">Diary</router-link>
            <router-link :to="`/guardian/evaluations/${child.id}`" class="px-3 py-1 bg-purple-100 text-purple-700 rounded-full text-xs font-medium hover:bg-purple-200">Evaluations</router-link>
            <router-link :to="`/guardian/report-cards/${child.id}`" class="px-3 py-1 bg-orange-100 text-orange-700 rounded-full text-xs font-medium hover:bg-orange-200">Report Cards</router-link>
            <router-link :to="`/guardian/fees/${child.id}`" class="px-3 py-1 bg-red-100 text-red-700 rounded-full text-xs font-medium hover:bg-red-200">Fees</router-link>
          </div>
        </div>
      </div>
    </div>

    <div v-if="!children.length" class="bg-white rounded-lg shadow p-12 text-center">
      <p class="text-gray-500">No children found.</p>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import api from '../../services/api';

const children = ref([]);

onMounted(async () => {
  try {
    const r = await api.get('/guardian/children');
    children.value = r.data.data || r.data || [];
  } catch (e) {
    console.error(e);
  }
});
</script>
