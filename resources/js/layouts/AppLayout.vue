<template>
  <div class="min-h-screen bg-gray-50">
    <!-- Sidebar -->
    <aside
      :class="[
        'fixed inset-y-0 left-0 z-50 w-64 bg-white border-r border-gray-200 transform transition-transform duration-300 ease-in-out lg:translate-x-0',
        sidebarOpen ? 'translate-x-0' : '-translate-x-full'
      ]"
    >
      <div class="flex items-center justify-center h-16 border-b border-gray-200">
        <h1 class="text-xl font-bold text-indigo-600">KG-SMS</h1>
      </div>

      <nav class="mt-6 px-4">
        <div v-if="authStore.isAdmin" class="space-y-1">
          <p class="px-4 py-1 text-xs font-semibold text-gray-400 uppercase tracking-wider">Management</p>
          <router-link to="/admin/guardians" class="flex items-center px-4 py-2 text-sm font-medium text-gray-700 rounded-lg hover:bg-gray-100">Guardians</router-link>
          <router-link to="/admin/students" class="flex items-center px-4 py-2 text-sm font-medium text-gray-700 rounded-lg hover:bg-gray-100">Students</router-link>
          <router-link to="/admin/promotions" class="flex items-center px-4 py-2 text-sm font-medium text-gray-700 rounded-lg hover:bg-gray-100">Promotions</router-link>
          <router-link to="/admin/teachers" class="flex items-center px-4 py-2 text-sm font-medium text-gray-700 rounded-lg hover:bg-gray-100">Teachers</router-link>
          <router-link to="/admin/classes" class="flex items-center px-4 py-2 text-sm font-medium text-gray-700 rounded-lg hover:bg-gray-100">Classes</router-link>
          <router-link to="/admin/subjects" class="flex items-center px-4 py-2 text-sm font-medium text-gray-700 rounded-lg hover:bg-gray-100">Subjects</router-link>
          <router-link to="/admin/rooms" class="flex items-center px-4 py-2 text-sm font-medium text-gray-700 rounded-lg hover:bg-gray-100">Rooms</router-link>
          <router-link to="/admin/academic-years" class="flex items-center px-4 py-2 text-sm font-medium text-gray-700 rounded-lg hover:bg-gray-100">Academic Years</router-link>
          <router-link to="/admin/terms" class="flex items-center px-4 py-2 text-sm font-medium text-gray-700 rounded-lg hover:bg-gray-100">Terms</router-link>

          <p class="px-4 py-1 mt-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Academics</p>
          <router-link to="/admin/attendance" class="flex items-center px-4 py-2 text-sm font-medium text-gray-700 rounded-lg hover:bg-gray-100">Attendance</router-link>
          <router-link to="/admin/evaluations" class="flex items-center px-4 py-2 text-sm font-medium text-gray-700 rounded-lg hover:bg-gray-100">Evaluations</router-link>
          <router-link to="/admin/report-cards" class="flex items-center px-4 py-2 text-sm font-medium text-gray-700 rounded-lg hover:bg-gray-100">Report Cards</router-link>
          <router-link to="/admin/routines" class="flex items-center px-4 py-2 text-sm font-medium text-gray-700 rounded-lg hover:bg-gray-100">Routines</router-link>
          <router-link to="/admin/exam-routines" class="flex items-center px-4 py-2 text-sm font-medium text-gray-700 rounded-lg hover:bg-gray-100">Exam Routines</router-link>

          <p class="px-4 py-1 mt-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Finance</p>
          <router-link to="/admin/fees" class="flex items-center px-4 py-2 text-sm font-medium text-gray-700 rounded-lg hover:bg-gray-100">Fees</router-link>

          <p class="px-4 py-1 mt-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Communication</p>
          <router-link to="/admin/announcements" class="flex items-center px-4 py-2 text-sm font-medium text-gray-700 rounded-lg hover:bg-gray-100">Announcements</router-link>
          <router-link to="/admin/events" class="flex items-center px-4 py-2 text-sm font-medium text-gray-700 rounded-lg hover:bg-gray-100">Events</router-link>
          <router-link to="/admin/messages" class="flex items-center px-4 py-2 text-sm font-medium text-gray-700 rounded-lg hover:bg-gray-100">Messages</router-link>

          <p class="px-4 py-1 mt-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Administration</p>
          <router-link to="/admin/applications" class="flex items-center px-4 py-2 text-sm font-medium text-gray-700 rounded-lg hover:bg-gray-100">Applications</router-link>
          <router-link to="/admin/profile-change-requests" class="flex items-center px-4 py-2 text-sm font-medium text-gray-700 rounded-lg hover:bg-gray-100">Profile Changes</router-link>
          <router-link to="/admin/activity-log" class="flex items-center px-4 py-2 text-sm font-medium text-gray-700 rounded-lg hover:bg-gray-100">Activity Log</router-link>
          <router-link to="/admin/settings" class="flex items-center px-4 py-2 text-sm font-medium text-gray-700 rounded-lg hover:bg-gray-100">Settings</router-link>
        </div>

        <div v-else-if="authStore.isTeacher" class="space-y-1">
          <p class="px-4 py-1 text-xs font-semibold text-gray-400 uppercase tracking-wider">Overview</p>
          <router-link to="/teacher" class="flex items-center px-4 py-2 text-sm font-medium text-gray-700 rounded-lg hover:bg-gray-100">Dashboard</router-link>

          <p class="px-4 py-1 mt-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Teaching</p>
          <router-link to="/teacher/classes" class="flex items-center px-4 py-2 text-sm font-medium text-gray-700 rounded-lg hover:bg-gray-100">My Classes</router-link>
          <router-link to="/teacher/attendance" class="flex items-center px-4 py-2 text-sm font-medium text-gray-700 rounded-lg hover:bg-gray-100">Attendance</router-link>
          <router-link to="/teacher/diary" class="flex items-center px-4 py-2 text-sm font-medium text-gray-700 rounded-lg hover:bg-gray-100">Diary</router-link>
          <router-link to="/teacher/evaluations" class="flex items-center px-4 py-2 text-sm font-medium text-gray-700 rounded-lg hover:bg-gray-100">Evaluations</router-link>

          <p class="px-4 py-1 mt-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Schedule</p>
          <router-link to="/teacher/routine" class="flex items-center px-4 py-2 text-sm font-medium text-gray-700 rounded-lg hover:bg-gray-100">Routine</router-link>
          <router-link to="/teacher/exam-routines" class="flex items-center px-4 py-2 text-sm font-medium text-gray-700 rounded-lg hover:bg-gray-100">Exam Routines</router-link>

          <p class="px-4 py-1 mt-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Communication</p>
          <router-link to="/teacher/messages" class="flex items-center px-4 py-2 text-sm font-medium text-gray-700 rounded-lg hover:bg-gray-100">Messages</router-link>
          <router-link to="/teacher/leave-notifications" class="flex items-center px-4 py-2 text-sm font-medium text-gray-700 rounded-lg hover:bg-gray-100">Leave Notifications</router-link>
        </div>

        <div v-else-if="authStore.isGuardian" class="space-y-1">
          <p class="px-4 py-1 text-xs font-semibold text-gray-400 uppercase tracking-wider">Overview</p>
          <router-link to="/guardian" class="flex items-center px-4 py-2 text-sm font-medium text-gray-700 rounded-lg hover:bg-gray-100">Dashboard</router-link>
          <router-link to="/guardian/children" class="flex items-center px-4 py-2 text-sm font-medium text-gray-700 rounded-lg hover:bg-gray-100">My Children</router-link>

          <p class="px-4 py-1 mt-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Academics</p>
          <router-link to="/guardian/attendance" class="flex items-center px-4 py-2 text-sm font-medium text-gray-700 rounded-lg hover:bg-gray-100">Attendance</router-link>
          <router-link to="/guardian/diary" class="flex items-center px-4 py-2 text-sm font-medium text-gray-700 rounded-lg hover:bg-gray-100">Diary</router-link>
          <router-link to="/guardian/evaluations" class="flex items-center px-4 py-2 text-sm font-medium text-gray-700 rounded-lg hover:bg-gray-100">Evaluations</router-link>
          <router-link to="/guardian/report-cards" class="flex items-center px-4 py-2 text-sm font-medium text-gray-700 rounded-lg hover:bg-gray-100">Report Cards</router-link>

          <p class="px-4 py-1 mt-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Finance</p>
          <router-link to="/guardian/fees" class="flex items-center px-4 py-2 text-sm font-medium text-gray-700 rounded-lg hover:bg-gray-100">Fees</router-link>

          <p class="px-4 py-1 mt-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Communication</p>
          <router-link to="/guardian/messages" class="flex items-center px-4 py-2 text-sm font-medium text-gray-700 rounded-lg hover:bg-gray-100">Messages</router-link>
          <router-link to="/guardian/leave-notifications" class="flex items-center px-4 py-2 text-sm font-medium text-gray-700 rounded-lg hover:bg-gray-100">Leave Notifications</router-link>

          <p class="px-4 py-1 mt-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Account</p>
          <router-link to="/guardian/profile-change-request" class="flex items-center px-4 py-2 text-sm font-medium text-gray-700 rounded-lg hover:bg-gray-100">Profile Change Request</router-link>
        </div>
      </nav>
    </aside>

    <!-- Main Content -->
    <div class="lg:pl-64">
      <!-- Header -->
      <header class="sticky top-0 z-40 h-16 bg-white border-b border-gray-200 flex items-center justify-between px-6">
        <button
          @click="sidebarOpen = !sidebarOpen"
          class="lg:hidden p-2 rounded-md text-gray-600 hover:bg-gray-100"
        >
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
          </svg>
        </button>

        <div class="flex items-center space-x-4">
          <span class="text-sm text-gray-600">{{ authStore.user?.name }}</span>
          <button
            @click="handleLogout"
            class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200"
          >
            Logout
          </button>
        </div>
      </header>

      <!-- Page Content -->
      <main class="p-6">
        <router-view />
      </main>
    </div>

    <!-- Overlay -->
    <div
      v-if="sidebarOpen"
      @click="sidebarOpen = false"
      class="fixed inset-0 z-40 bg-black bg-opacity-50 lg:hidden"
    />
  </div>
</template>

<script setup>
import { ref, watch } from 'vue';
import { useRouter, useRoute } from 'vue-router';
import { useAuthStore } from '../stores/auth';

const authStore = useAuthStore();
const router = useRouter();
const route = useRoute();
const sidebarOpen = ref(false);

watch(() => route.path, () => {
  sidebarOpen.value = false;
});

const handleLogout = async () => {
  await authStore.logout();
  router.push('/login');
};
</script>
