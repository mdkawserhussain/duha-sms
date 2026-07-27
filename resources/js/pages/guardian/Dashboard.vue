<template>
  <div>
    <h1 class="text-2xl font-bold text-gray-900 mb-6">Guardian Dashboard</h1>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
      <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-indigo-100 text-indigo-600">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
          </div>
          <div class="ml-4">
            <p class="text-sm font-medium text-gray-500">My Children</p>
            <p class="text-2xl font-semibold text-gray-900">{{ stats.total_children || 0 }}</p>
          </div>
        </div>
      </div>
      <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-green-100 text-green-600">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
          </div>
          <div class="ml-4">
            <p class="text-sm font-medium text-gray-500">Present Today</p>
            <p class="text-2xl font-semibold text-gray-900">{{ stats.present_today || 0 }}</p>
          </div>
        </div>
      </div>
      <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-yellow-100 text-yellow-600">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
          </div>
          <div class="ml-4">
            <p class="text-sm font-medium text-gray-500">Unread Messages</p>
            <p class="text-2xl font-semibold text-gray-900">{{ stats.unread_messages || 0 }}</p>
          </div>
        </div>
      </div>
      <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-red-100 text-red-600">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
          </div>
          <div class="ml-4">
            <p class="text-sm font-medium text-gray-500">Pending Fees</p>
            <p class="text-2xl font-semibold text-gray-900">{{ stats.pending_fees || 0 }}</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Children Overview -->
    <div class="bg-white rounded-lg shadow mb-8">
      <div class="px-6 py-4 border-b border-gray-200">
        <h2 class="text-lg font-semibold text-gray-900">My Children</h2>
      </div>
      <div class="p-6">
        <div v-if="children.length" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
          <div v-for="child in children" :key="child.id" class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
            <div class="flex items-center mb-3">
              <div class="w-10 h-10 rounded-full bg-indigo-100 flex items-center justify-center">
                <span class="text-indigo-600 font-semibold">{{ child.name?.charAt(0) }}</span>
              </div>
              <div class="ml-3">
                <p class="font-medium text-gray-900">{{ child.name }}</p>
                <p class="text-sm text-gray-500">{{ child.class?.name || 'No Class' }}</p>
              </div>
            </div>
            <div class="flex gap-2">
              <router-link :to="`/guardian/attendance/${child.id}`" class="text-xs bg-blue-50 text-blue-600 px-2 py-1 rounded">Attendance</router-link>
              <router-link :to="`/guardian/diary/${child.id}`" class="text-xs bg-green-50 text-green-600 px-2 py-1 rounded">Diary</router-link>
              <router-link :to="`/guardian/evaluations/${child.id}`" class="text-xs bg-purple-50 text-purple-600 px-2 py-1 rounded">Evaluations</router-link>
              <router-link :to="`/guardian/report-cards/${child.id}`" class="text-xs bg-orange-50 text-orange-600 px-2 py-1 rounded">Report</router-link>
            </div>
          </div>
        </div>
        <p v-else class="text-gray-500 text-sm">No children found.</p>
      </div>
    </div>

    <!-- Recent Activity -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
      <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200">
          <h2 class="text-lg font-semibold text-gray-900">Recent Announcements</h2>
        </div>
        <div class="p-6">
          <div v-if="announcements.length" class="space-y-3">
            <div v-for="a in announcements" :key="a.id" class="border-l-4 border-indigo-400 pl-3">
              <p class="font-medium text-sm text-gray-900">{{ a.title }}</p>
              <p class="text-xs text-gray-500">{{ formatDate(a.created_at) }}</p>
            </div>
          </div>
          <p v-else class="text-gray-500 text-sm">No recent announcements.</p>
        </div>
      </div>
      <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200">
          <h2 class="text-lg font-semibold text-gray-900">Upcoming Events</h2>
        </div>
        <div class="p-6">
          <div v-if="events.length" class="space-y-3">
            <div v-for="e in events" :key="e.id" class="border-l-4 border-green-400 pl-3">
              <p class="font-medium text-sm text-gray-900">{{ e.title }}</p>
              <p class="text-xs text-gray-500">{{ formatDate(e.event_date) }}</p>
            </div>
          </div>
          <p v-else class="text-gray-500 text-sm">No upcoming events.</p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import api from '../../services/api';
import { useToast } from '../../composables/useToast';

const toast = useToast();

const stats = ref({});
const children = ref([]);
const announcements = ref([]);
const events = ref([]);

const formatDate = (date) => {
  return new Date(date).toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' });
};

onMounted(async () => {
  try {
    const r = await api.get('/guardian/dashboard');
    stats.value = r.data.stats || r.data || {};
    children.value = r.data.children || [];
    announcements.value = r.data.announcements || [];
    events.value = r.data.events || [];
  } catch (e) {
    toast.error('Failed to load dashboard');
  }
});
</script>
