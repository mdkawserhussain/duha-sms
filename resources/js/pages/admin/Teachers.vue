<script setup>
import { ref, onMounted, watch } from 'vue'
import api from '../../services/api'
import { useToast } from '../../composables/useToast'

const toast = useToast()

const teachers = ref([])
const loading = ref(true)
const total = ref(0)
const page = ref(1)
const totalPages = ref(1)
const search = ref('')
const statusFilter = ref('')
const showAddModal = ref(false)
const editingTeacher = ref(null)
const formErrors = ref({})
const saving = ref(false)

const form = ref({
  name: '',
  email: '',
  phone: '',
  password: '',
  password_confirmation: '',
})

const loadTeachers = async () => {
  loading.value = true
  try {
    const response = await api.get('/admin/teachers', {
      params: {
        page: page.value,
        search: search.value,
        status: statusFilter.value,
      },
    })
    teachers.value = response.data.data
    total.value = response.data.total
    totalPages.value = response.data.last_page
  } catch (error) {
    toast.error('Failed to load teachers')
  } finally {
    loading.value = false
  }
}

const openAddModal = () => {
  editingTeacher.value = null
  form.value = {
    name: '',
    email: '',
    phone: '',
    password: '',
    password_confirmation: '',
  }
  formErrors.value = {}
  showAddModal.value = true
}

const editTeacher = (teacher) => {
  editingTeacher.value = teacher
  form.value = {
    name: teacher.name,
    email: teacher.email,
    phone: teacher.phone,
    password: '',
    password_confirmation: '',
  }
  formErrors.value = {}
  showAddModal.value = true
}

const deleteTeacher = async (id) => {
  if (!confirm('Are you sure you want to delete this teacher?')) return
  try {
    await api.delete(`/admin/teachers/${id}`)
    loadTeachers()
  } catch (error) {
    toast.error('Failed to delete teacher')
  }
}

const toggleStatus = async (teacher) => {
  try {
    await api.post(`/admin/teachers/${teacher.id}/toggle-status`)
    loadTeachers()
  } catch (error) {
    toast.error('Failed to toggle status')
  }
}

const saveTeacher = async () => {
  saving.value = true
  formErrors.value = {}
  try {
    if (editingTeacher.value) {
      const data = { ...form.value }
      if (!data.password) {
        delete data.password
        delete data.password_confirmation
      }
      await api.put(`/admin/teachers/${editingTeacher.value.id}`, data)
    } else {
      await api.post('/admin/teachers', form.value)
    }
    showAddModal.value = false
    loadTeachers()
  } catch (error) {
    if (error.response?.status === 422) {
      formErrors.value = error.response.data.errors
    } else {
      toast.error('Failed to save teacher')
    }
  } finally {
    saving.value = false
  }
}

watch([page, search, statusFilter], loadTeachers)

onMounted(loadTeachers)
</script>

<template>
  <div>
    <div class="flex justify-between items-center mb-6">
      <h1 class="text-2xl font-bold text-gray-900">Teachers</h1>
      <button
        @click="openAddModal"
        class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700"
      >
        Add Teacher
      </button>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
      <div class="p-4 border-b border-gray-200">
        <div class="flex space-x-4">
          <input
            v-model="search"
            type="text"
            placeholder="Search by name, email, or phone..."
            class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
          />
          <select
            v-model="statusFilter"
            class="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
          >
            <option value="">All Status</option>
            <option value="active">Active</option>
            <option value="suspended">Suspended</option>
          </select>
        </div>
      </div>

      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Phone</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Classes</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            <tr v-if="loading">
              <td colspan="6" class="px-6 py-12 text-center text-gray-500">Loading...</td>
            </tr>
            <tr v-else-if="teachers.length === 0">
              <td colspan="6" class="px-6 py-12 text-center text-gray-500">No teachers found</td>
            </tr>
            <tr v-for="teacher in teachers" :key="teacher.id" class="hover:bg-gray-50">
              <td class="px-6 py-4 whitespace-nowrap font-medium text-gray-900">{{ teacher.name }}</td>
              <td class="px-6 py-4 whitespace-nowrap text-gray-600">{{ teacher.phone }}</td>
              <td class="px-6 py-4 whitespace-nowrap text-gray-600">{{ teacher.email }}</td>
              <td class="px-6 py-4 whitespace-nowrap text-gray-600">{{ teacher.assigned_classes_count || 0 }}</td>
              <td class="px-6 py-4 whitespace-nowrap">
                <button
                  @click="toggleStatus(teacher)"
                  :class="[
                    'px-2 py-1 text-xs font-medium rounded-full',
                    teacher.status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'
                  ]"
                >
                  {{ teacher.status }}
                </button>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                <button
                  @click="editTeacher(teacher)"
                  class="text-indigo-600 hover:text-indigo-900 mr-3"
                >
                  Edit
                </button>
                <button
                  @click="deleteTeacher(teacher.id)"
                  class="text-red-600 hover:text-red-900"
                >
                  Delete
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <div class="p-4 border-t border-gray-200">
        <div class="flex justify-between items-center">
          <span class="text-sm text-gray-700">
            Showing {{ teachers.length }} of {{ total }} teachers
          </span>
          <div class="flex space-x-2">
            <button
              @click="page > 1 && page--"
              :disabled="page === 1"
              class="px-3 py-1 border border-gray-300 rounded-lg text-sm disabled:opacity-50"
            >
              Previous
            </button>
            <span class="px-3 py-1 text-sm">Page {{ page }} of {{ totalPages }}</span>
            <button
              @click="page < totalPages && page++"
              :disabled="page === totalPages"
              class="px-3 py-1 border border-gray-300 rounded-lg text-sm disabled:opacity-50"
            >
              Next
            </button>
      </div>
    </div>
    <!-- Add/Edit Modal -->
    <div v-if="showAddModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center z-50">
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
          <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
            <h3 class="text-lg font-medium text-gray-900 mb-4">
              {{ editingTeacher ? 'Edit Teacher' : 'Add Teacher' }}
            </h3>
            <form @submit.prevent="saveTeacher">
              <div class="space-y-4">
                <div>
                  <label class="block text-sm font-medium text-gray-700">Name *</label>
                  <input
                    v-model="form.name"
                    type="text"
                    required
                    class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                    :class="{ 'border-red-500': formErrors.name }"
                  />
                  <p v-if="formErrors.name" class="mt-1 text-sm text-red-600">{{ formErrors.name[0] }}</p>
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700">Email *</label>
                  <input
                    v-model="form.email"
                    type="email"
                    required
                    class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                    :class="{ 'border-red-500': formErrors.email }"
                  />
                  <p v-if="formErrors.email" class="mt-1 text-sm text-red-600">{{ formErrors.email[0] }}</p>
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700">Phone *</label>
                  <input
                    v-model="form.phone"
                    type="tel"
                    required
                    class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                    :class="{ 'border-red-500': formErrors.phone }"
                  />
                  <p v-if="formErrors.phone" class="mt-1 text-sm text-red-600">{{ formErrors.phone[0] }}</p>
                </div>
                <div v-if="!editingTeacher">
                  <label class="block text-sm font-medium text-gray-700">Password *</label>
                  <input
                    v-model="form.password"
                    type="password"
                    required
                    class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                    :class="{ 'border-red-500': formErrors.password }"
                  />
                  <p v-if="formErrors.password" class="mt-1 text-sm text-red-600">{{ formErrors.password[0] }}</p>
                </div>
                <div v-if="!editingTeacher">
                  <label class="block text-sm font-medium text-gray-700">Confirm Password *</label>
                  <input
                    v-model="form.password_confirmation"
                    type="password"
                    required
                    class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                  />
                </div>
                <div v-if="editingTeacher">
                  <label class="block text-sm font-medium text-gray-700">New Password (leave blank to keep current)</label>
                  <input
                    v-model="form.password"
                    type="password"
                    class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                  />
                </div>
              </div>
              <div class="mt-6 flex justify-end space-x-3">
                <button
                  type="button"
                  @click="showAddModal = false"
                  class="px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50"
                >
                  Cancel
                </button>
                <button
                  type="submit"
                  :disabled="saving"
                  class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-medium hover:bg-indigo-700 disabled:opacity-50"
                >
                  {{ saving ? 'Saving...' : (editingTeacher ? 'Update' : 'Create') }}
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
