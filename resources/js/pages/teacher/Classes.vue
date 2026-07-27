<template>
  <div>
    <h1 class="text-2xl font-bold text-gray-900 mb-6">My Classes</h1>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
      <div v-for="cls in classes" :key="cls.id" class="bg-white rounded-lg shadow p-6 cursor-pointer hover:shadow-md transition-shadow" @click="selectedClass = cls; fetchStudents(cls.id)">
        <h3 class="text-lg font-semibold text-gray-900">{{ cls.name }}</h3>
        <p class="text-sm text-gray-500 mt-1">{{ cls.section || 'No section' }}</p>
        <p class="text-sm text-gray-500">{{ cls.students_count || 0 }} students</p>
      </div>
      <div v-if="!classes.length" class="col-span-full text-center py-12 text-gray-500">No classes assigned</div>
    </div>

    <!-- Students Modal -->
    <div v-if="selectedClass" class="fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center z-50">
      <div class="bg-white rounded-lg shadow-xl max-w-3xl w-full mx-4 p-6 max-h-[80vh] overflow-y-auto">
        <div class="flex items-center justify-between mb-4">
          <h2 class="text-lg font-semibold">{{ selectedClass.name }} - Students</h2>
          <button @click="selectedClass = null" class="text-gray-400 hover:text-gray-600">&times;</button>
        </div>
        <table class="min-w-full divide-y divide-gray-200">
          <thead><tr><th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Name</th><th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Email</th><th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Gender</th></tr></thead>
          <tbody class="divide-y divide-gray-200">
            <tr v-for="s in students" :key="s.id">
              <td class="px-4 py-3 text-sm font-medium text-gray-900">{{ s.name }}</td>
              <td class="px-4 py-3 text-sm text-gray-500">{{ s.user?.email || '-' }}</td>
              <td class="px-4 py-3 text-sm text-gray-500 capitalize">{{ s.gender || '-' }}</td>
            </tr>
            <tr v-if="!students.length"><td colspan="3" class="px-4 py-8 text-center text-sm text-gray-500">No students</td></tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import api from '../../services/api';
import { useToast } from '../../composables/useToast';

const toast = useToast();
const classes = ref([]);
const selectedClass = ref(null);
const students = ref([]);

const fetchStudents = async (classId) => {
  try { const r = await api.get(`/teacher/classes/${classId}/students`); students.value = r.data.data || r.data || []; } catch (e) { toast.error('Failed to load students'); }
};
onMounted(async () => { try { const r = await api.get('/teacher/classes'); classes.value = r.data.data || r.data || []; } catch (e) { toast.error('Failed to load classes'); } });
</script>
