<template>
  <div>
    <h1 class="text-2xl font-bold text-gray-900 mb-6">Dashboard</h1>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
      <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center">
          <div class="p-3 bg-indigo-100 rounded-full">
            <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z" />
            </svg>
          </div>
          <div class="ml-4">
            <p class="text-sm font-medium text-gray-500">Total Students</p>
            <p class="text-2xl font-semibold text-gray-900">{{ stats.students }}</p>
          </div>
        </div>
      </div>

      <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center">
          <div class="p-3 bg-green-100 rounded-full">
            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
            </svg>
          </div>
          <div class="ml-4">
            <p class="text-sm font-medium text-gray-500">Total Teachers</p>
            <p class="text-2xl font-semibold text-gray-900">{{ stats.teachers }}</p>
          </div>
        </div>
      </div>

      <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center">
          <div class="p-3 bg-yellow-100 rounded-full">
            <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
            </svg>
          </div>
          <div class="ml-4">
            <p class="text-sm font-medium text-gray-500">Total Classes</p>
            <p class="text-2xl font-semibold text-gray-900">{{ stats.classes }}</p>
          </div>
        </div>
      </div>

      <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center">
          <div class="p-3 bg-purple-100 rounded-full">
            <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
          </div>
          <div class="ml-4">
            <p class="text-sm font-medium text-gray-500">Pending Verifications</p>
            <p class="text-2xl font-semibold text-gray-900">{{ stats.pendingVerifications }}</p>
          </div>
        </div>
      </div>
    </div>

    <div class="mt-8 grid grid-cols-1 lg:grid-cols-2 gap-6">
      <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Recent Activity</h2>
        <div class="space-y-4">
          <div v-for="activity in recentActivity" :key="activity.id" class="flex items-start">
            <div class="flex-shrink-0">
              <div class="w-2 h-2 bg-indigo-500 rounded-full mt-2"></div>
            </div>
            <div class="ml-3">
              <p class="text-sm text-gray-700">{{ activity.description }}</p>
              <p class="text-xs text-gray-500">{{ activity.time }}</p>
            </div>
          </div>
        </div>
      </div>

      <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Quick Actions</h2>
        <div class="grid grid-cols-2 gap-4">
          <router-link
            v-if="authStore.isAdmin"
            to="/admin/students"
            class="flex items-center justify-center p-4 bg-indigo-50 rounded-lg hover:bg-indigo-100"
          >
            <span class="text-sm font-medium text-indigo-700">Add Student</span>
          </router-link>
          <router-link
            v-if="authStore.isAdmin"
            to="/admin/teachers"
            class="flex items-center justify-center p-4 bg-green-50 rounded-lg hover:bg-green-100"
          >
            <span class="text-sm font-medium text-green-700">Add Teacher</span>
          </router-link>
          <router-link
            v-if="authStore.isTeacher"
            to="/teacher/diary"
            class="flex items-center justify-center p-4 bg-yellow-50 rounded-lg hover:bg-yellow-100"
          >
            <span class="text-sm font-medium text-yellow-700">Add Diary</span>
          </router-link>
          <router-link
            v-if="authStore.isTeacher"
            to="/teacher/attendance"
            class="flex items-center justify-center p-4 bg-purple-50 rounded-lg hover:bg-purple-100"
          >
            <span class="text-sm font-medium text-purple-700">Mark Attendance</span>
          </router-link>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useAuthStore } from '../stores/auth';
import api from '../services/api';

const authStore = useAuthStore();

const stats = ref({
  students: 0,
  teachers: 0,
  classes: 0,
  pendingVerifications: 0,
});

const recentActivity = ref([]);

onMounted(async () => {
  try {
    let url = '/admin/dashboard';
    if (authStore.isTeacher) url = '/teacher/dashboard';
    else if (authStore.isGuardian) url = '/guardian/dashboard';
    const response = await api.get(url);
    const data = response.data;
    const rawStats = data.stats || {};
    stats.value = {
      students: rawStats.total_students ?? 0,
      teachers: rawStats.total_teachers ?? 0,
      classes: rawStats.total_classes ?? 0,
      pendingVerifications: rawStats.pending_verifications ?? 0,
    };
    recentActivity.value = data.recent_activity || [];
  } catch (error) {
    console.error('Failed to load dashboard:', error);
  }
});
</script>
