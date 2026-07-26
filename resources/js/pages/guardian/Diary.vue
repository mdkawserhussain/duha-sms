<template>
  <div>
    <div class="flex items-center justify-between mb-6">
      <h1 class="text-2xl font-bold text-gray-900">Diary</h1>
      <select v-model="studentId" @change="fetchDiary" class="rounded-md border-gray-300 text-sm">
        <option value="">Select Child</option>
        <option v-for="child in children" :key="child.id" :value="child.id">{{ child.first_name }} {{ child.last_name }}</option>
      </select>
    </div>

    <div v-if="studentId" class="space-y-4">
      <div v-for="entry in diaryEntries" :key="entry.id" class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center justify-between mb-3">
          <h3 class="font-semibold text-gray-900">{{ entry.title }}</h3>
          <span class="text-xs text-gray-500">{{ formatDate(entry.date) }}</span>
        </div>
        <p class="text-sm text-gray-700 mb-3">{{ entry.content }}</p>
        <div v-if="entry.subject" class="mb-3">
          <span class="px-2 py-1 bg-indigo-100 text-indigo-700 rounded text-xs">{{ entry.subject?.name || entry.subject }}</span>
        </div>

        <!-- Comments -->
        <div v-if="entry.comments?.length" class="border-t border-gray-100 pt-3 mt-3">
          <p class="text-xs font-medium text-gray-500 mb-2">Comments:</p>
          <div v-for="c in entry.comments" :key="c.id" class="ml-4 mb-2">
            <p class="text-sm text-gray-700">{{ c.content }}</p>
            <p class="text-xs text-gray-400">- {{ c.user?.name || 'Unknown' }} on {{ formatDate(c.created_at) }}</p>
          </div>
        </div>

        <!-- Add Comment -->
        <div class="border-t border-gray-100 pt-3 mt-3">
          <form @submit.prevent="addComment(entry.id)" class="flex gap-2">
            <input v-model="commentText[entry.id]" placeholder="Add a comment..." class="flex-1 rounded-md border-gray-300 text-sm" />
            <button type="submit" class="px-3 py-1 bg-indigo-600 text-white rounded-md text-sm hover:bg-indigo-700">Send</button>
          </form>
        </div>
      </div>

      <div v-if="!diaryEntries.length" class="bg-white rounded-lg shadow p-12 text-center">
        <p class="text-gray-500">No diary entries found.</p>
      </div>
    </div>

    <div v-else class="bg-white rounded-lg shadow p-12 text-center">
      <p class="text-gray-500">Select a child to view diary entries.</p>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue';
import api from '../../services/api';
import { useRoute } from 'vue-router';

const route = useRoute();
const children = ref([]);
const diaryEntries = ref([]);
const studentId = ref('');
const commentText = reactive({});

const formatDate = (date) => {
  return new Date(date).toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' });
};

const fetchDiary = async () => {
  if (!studentId.value) return;
  try {
    const r = await api.get(`/guardian/diary/${studentId.value}`);
    diaryEntries.value = r.data.data || r.data || [];
  } catch (e) {
    console.error(e);
  }
};

const addComment = async (diaryId) => {
  const text = commentText[diaryId];
  if (!text?.trim()) return;
  try {
    await api.post(`/guardian/diary/${diaryId}/comment`, { content: text });
    commentText[diaryId] = '';
    fetchDiary();
  } catch (e) {
    alert(e.response?.data?.message || 'Error adding comment');
  }
};

onMounted(async () => {
  try {
    const r = await api.get('/guardian/children');
    children.value = r.data.data || r.data || [];
    if (route.params.studentId) {
      studentId.value = route.params.studentId;
      fetchDiary();
    }
  } catch (e) {
    console.error(e);
  }
});
</script>
