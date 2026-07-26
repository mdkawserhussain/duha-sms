<template>
  <div>
    <div class="flex justify-between items-center mb-6">
      <h1 class="text-2xl font-bold text-gray-900">Guardians</h1>
      <button
        @click="showAddModal = true"
        class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700"
      >
        Add Guardian
      </button>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
      <div class="p-4 border-b border-gray-200">
        <div class="flex space-x-4">
          <input
            v-model="search"
            type="text"
            placeholder="Search by name or phone..."
            class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
          />
          <select
            v-model="statusFilter"
            class="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
          >
            <option value="">All Status</option>
            <option value="pending">Pending</option>
            <option value="approved">Approved</option>
            <option value="rejected">Rejected</option>
          </select>
        </div>
      </div>

      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Phone</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
          <tr v-for="guardian in guardians" :key="guardian.id">
            <td class="px-6 py-4 whitespace-nowrap">{{ guardian.name }}</td>
            <td class="px-6 py-4 whitespace-nowrap">{{ guardian.phone }}</td>
            <td class="px-6 py-4 whitespace-nowrap">{{ guardian.email || '-' }}</td>
            <td class="px-6 py-4 whitespace-nowrap">
              <span
                :class="[
                  'px-2 py-1 text-xs font-medium rounded-full',
                  guardian.status === 'approved' ? 'bg-green-100 text-green-800' :
                  guardian.status === 'pending' ? 'bg-yellow-100 text-yellow-800' :
                  'bg-red-100 text-red-800'
                ]"
              >
                {{ guardian.status }}
              </span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
              <button
                @click="editGuardian(guardian)"
                class="text-indigo-600 hover:text-indigo-900 mr-3"
              >
                Edit
              </button>
              <button
                @click="deleteGuardian(guardian.id)"
                class="text-red-600 hover:text-red-900"
              >
                Delete
              </button>
            </td>
          </tr>
        </tbody>
      </table>

      <div class="p-4 border-t border-gray-200">
        <div class="flex justify-between items-center">
          <span class="text-sm text-gray-700">
            Showing {{ guardians.length }} of {{ total }} guardians
          </span>
          <div class="flex space-x-2">
            <button
              @click="page > 1 && page--"
              :disabled="page === 1"
              class="px-3 py-1 border border-gray-300 rounded-lg text-sm disabled:opacity-50"
            >
              Previous
            </button>
            <button
              @click="page < totalPages && page++"
              :disabled="page === totalPages"
              class="px-3 py-1 border border-gray-300 rounded-lg text-sm disabled:opacity-50"
            >
              Next
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Add/Edit Modal -->
    <div
      v-if="showAddModal"
      class="fixed inset-0 z-50 overflow-y-auto"
      aria-labelledby="modal-title"
      role="dialog"
      aria-modal="true"
    >
      <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div
          class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"
          @click="showAddModal = false"
        />

        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
          <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
            <h3 class="text-lg font-medium text-gray-900 mb-4">
              {{ editingGuardian ? 'Edit Guardian' : 'Add Guardian' }}
            </h3>
            <form @submit.prevent="saveGuardian">
              <div class="space-y-4">
                <div>
                  <label class="block text-sm font-medium text-gray-700">Name</label>
                  <input
                    v-model="form.name"
                    type="text"
                    required
                    class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                  />
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700">Phone</label>
                  <input
                    v-model="form.phone"
                    type="tel"
                    required
                    class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                  />
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700">Email</label>
                  <input
                    v-model="form.email"
                    type="email"
                    class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                  />
                </div>
              </div>
              <div class="mt-4 flex justify-end space-x-3">
                <button
                  type="button"
                  @click="showAddModal = false"
                  class="px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50"
                >
                  Cancel
                </button>
                <button
                  type="submit"
                  class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-medium hover:bg-indigo-700"
                >
                  {{ editingGuardian ? 'Update' : 'Create' }}
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue';
import api from '../../services/api';

const guardians = ref([]);
const total = ref(0);
const page = ref(1);
const totalPages = ref(1);
const search = ref('');
const statusFilter = ref('');
const showAddModal = ref(false);
const editingGuardian = ref(null);
const form = ref({
  name: '',
  phone: '',
  email: '',
});

const loadGuardians = async () => {
  try {
    const response = await api.get('/admin/guardians', {
      params: {
        page: page.value,
        search: search.value,
        status: statusFilter.value,
      },
    });
    guardians.value = response.data.data;
    total.value = response.data.total;
    totalPages.value = response.data.last_page;
  } catch (error) {
    console.error('Failed to load guardians:', error);
  }
};

const editGuardian = (guardian) => {
  editingGuardian.value = guardian;
  form.value = { ...guardian };
  showAddModal.value = true;
};

const deleteGuardian = async (id) => {
  if (confirm('Are you sure you want to delete this guardian?')) {
    try {
      await api.delete(`/admin/guardians/${id}`);
      loadGuardians();
    } catch (error) {
      console.error('Failed to delete guardian:', error);
    }
  }
};

const saveGuardian = async () => {
  try {
    if (editingGuardian.value) {
      await api.put(`/admin/guardians/${editingGuardian.value.id}`, form.value);
    } else {
      await api.post('/admin/guardians', form.value);
    }
    showAddModal.value = false;
    editingGuardian.value = null;
    form.value = { name: '', phone: '', email: '' };
    loadGuardians();
  } catch (error) {
    console.error('Failed to save guardian:', error);
  }
};

watch([page, search, statusFilter], loadGuardians);

onMounted(loadGuardians);
</script>
