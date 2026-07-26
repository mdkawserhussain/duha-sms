<script setup>
import { ref, onMounted, watch } from 'vue'
import api from '../../services/api'

const students = ref([])
const classes = ref([])
const guardians = ref([])
const loading = ref(true)
const total = ref(0)
const page = ref(1)
const totalPages = ref(1)
const search = ref('')
const classFilter = ref('')
const statusFilter = ref('')
const showAddModal = ref(false)
const editingStudent = ref(null)
const formErrors = ref({})
const saving = ref(false)

const form = ref({
  name: '',
  gender: 'male',
  dob: '',
  class_id: '',
  guardian_id: '',
  admission_date: new Date().toISOString().split('T')[0],
})

const loadStudents = async () => {
  loading.value = true
  try {
    const response = await api.get('/admin/students', {
      params: {
        page: page.value,
        search: search.value,
        class_id: classFilter.value,
        status: statusFilter.value,
      },
    })
    students.value = response.data.data
    total.value = response.data.total
    totalPages.value = response.data.last_page
  } catch (error) {
    console.error('Failed to load students:', error)
  } finally {
    loading.value = false
  }
}

const loadClasses = async () => {
  try {
    const response = await api.get('/admin/classes')
    classes.value = response.data.data
  } catch (error) {
    console.error('Failed to load classes:', error)
  }
}

const loadGuardians = async () => {
  try {
    const response = await api.get('/admin/guardians')
    guardians.value = response.data.data
  } catch (error) {
    console.error('Failed to load guardians:', error)
  }
}

const openAddModal = () => {
  editingStudent.value = null
  form.value = {
    name: '',
    gender: 'male',
    dob: '',
    class_id: '',
    guardian_id: '',
    admission_date: new Date().toISOString().split('T')[0],
  }
  formErrors.value = {}
  showAddModal.value = true
}

const editStudent = (student) => {
  editingStudent.value = student
  form.value = {
    name: student.name,
    gender: student.gender === 'm' ? 'male' : student.gender === 'f' ? 'female' : student.gender,
    dob: student.dob?.split('T')[0] || '',
    class_id: student.class_id,
    guardian_id: student.guardian_id,
    admission_date: student.admission_date?.split('T')[0] || '',
  }
  formErrors.value = {}
  showAddModal.value = true
}

const deleteStudent = async (id) => {
  if (!confirm('Are you sure you want to delete this student?')) return
  try {
    await api.delete(`/admin/students/${id}`)
    loadStudents()
  } catch (error) {
    console.error('Failed to delete student:', error)
  }
}

const toggleStatus = async (student) => {
  try {
    await api.post(`/admin/students/${student.id}/toggle-status`)
    loadStudents()
  } catch (error) {
    console.error('Failed to toggle status:', error)
  }
}

const saveStudent = async () => {
  saving.value = true
  formErrors.value = {}
  try {
    if (editingStudent.value) {
      await api.put(`/admin/students/${editingStudent.value.id}`, form.value)
    } else {
      await api.post('/admin/students', form.value)
    }
    showAddModal.value = false
    loadStudents()
  } catch (error) {
    if (error.response?.status === 422) {
      formErrors.value = error.response.data.errors
    } else {
      console.error('Failed to save student:', error)
    }
  } finally {
    saving.value = false
  }
}

const formatDate = (date) => {
  if (!date) return '-'
  return new Date(date).toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' })
}

watch([page, search, classFilter, statusFilter], loadStudents)

onMounted(() => {
  loadStudents()
  loadClasses()
  loadGuardians()
})
</script>

<template>
  <div>
    <div class="flex justify-between items-center mb-6">
      <h1 class="text-2xl font-bold text-gray-900">Students</h1>
      <button
        @click="openAddModal"
        class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700"
      >
        Add Student
      </button>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
      <div class="p-4 border-b border-gray-200">
        <div class="flex flex-col sm:flex-row gap-3">
          <input
            v-model="search"
            type="text"
            placeholder="Search by name..."
            class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
          />
          <select
            v-model="classFilter"
            class="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
          >
            <option value="">All Classes</option>
            <option v-for="cls in classes" :key="cls.id" :value="cls.id">
              {{ cls.name }} - {{ cls.section }}
            </option>
          </select>
          <select
            v-model="statusFilter"
            class="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
          >
            <option value="">All Status</option>
            <option value="active">Active</option>
            <option value="inactive">Inactive</option>
          </select>
        </div>
      </div>

      <div class="overflow-x-auto">
        <table class="w-full min-w-[700px] divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Class</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Guardian</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">DOB</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            <tr v-if="loading">
              <td colspan="6" class="px-6 py-12 text-center text-gray-500">Loading...</td>
            </tr>
            <tr v-else-if="students.length === 0">
              <td colspan="6" class="px-6 py-12 text-center text-gray-500">No students found</td>
            </tr>
            <tr v-for="student in students" :key="student.id" class="hover:bg-gray-50">
              <td class="px-6 py-4 whitespace-nowrap font-medium text-gray-900">{{ student.name }}</td>
              <td class="px-6 py-4 whitespace-nowrap text-gray-600">{{ student.class?.name }} - {{ student.class?.section }}</td>
              <td class="px-6 py-4 whitespace-nowrap text-gray-600">{{ student.guardian?.name }}</td>
              <td class="px-6 py-4 whitespace-nowrap text-gray-600">{{ formatDate(student.dob) }}</td>
              <td class="px-6 py-4 whitespace-nowrap">
                <button
                  @click="toggleStatus(student)"
                  :class="[
                    'px-2 py-1 text-xs font-medium rounded-full',
                    student.status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'
                  ]"
                >
                  {{ student.status }}
                </button>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                <button
                  @click="editStudent(student)"
                  class="text-indigo-600 hover:text-indigo-900 mr-3"
                >
                  Edit
                </button>
                <button
                  @click="deleteStudent(student.id)"
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
        <div class="flex flex-col sm:flex-row justify-between items-center gap-2">
          <span class="text-sm text-gray-700">
            Showing {{ students.length }} of {{ total }} students
          </span>
          <div class="flex items-center space-x-2">
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

        <div class="relative z-10 inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
          <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
            <h3 class="text-lg font-medium text-gray-900 mb-4">
              {{ editingStudent ? 'Edit Student' : 'Add Student' }}
            </h3>
            <form @submit.prevent="saveStudent">
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
                <div class="grid grid-cols-2 gap-4">
                  <div>
                    <label class="block text-sm font-medium text-gray-700">Gender *</label>
                    <select
                      v-model="form.gender"
                      class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                    >
                      <option value="male">Male</option>
                      <option value="female">Female</option>
                    </select>
                  </div>
                  <div>
                    <label class="block text-sm font-medium text-gray-700">Date of Birth *</label>
                    <input
                      v-model="form.dob"
                      type="date"
                      required
                      class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                      :class="{ 'border-red-500': formErrors.dob }"
                    />
                    <p v-if="formErrors.dob" class="mt-1 text-sm text-red-600">{{ formErrors.dob[0] }}</p>
                  </div>
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700">Class *</label>
                  <select
                    v-model="form.class_id"
                    required
                    class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                    :class="{ 'border-red-500': formErrors.class_id }"
                  >
                    <option value="">Select Class</option>
                    <option v-for="cls in classes" :key="cls.id" :value="cls.id">
                      {{ cls.name }} - {{ cls.section }}
                    </option>
                  </select>
                  <p v-if="formErrors.class_id" class="mt-1 text-sm text-red-600">{{ formErrors.class_id[0] }}</p>
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700">Guardian *</label>
                  <select
                    v-model="form.guardian_id"
                    required
                    class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                    :class="{ 'border-red-500': formErrors.guardian_id }"
                  >
                    <option value="">Select Guardian</option>
                    <option v-for="g in guardians" :key="g.id" :value="g.id">
                      {{ g.name }} ({{ g.phone }})
                    </option>
                  </select>
                  <p v-if="formErrors.guardian_id" class="mt-1 text-sm text-red-600">{{ formErrors.guardian_id[0] }}</p>
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700">Admission Date *</label>
                  <input
                    v-model="form.admission_date"
                    type="date"
                    required
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
                  {{ saving ? 'Saving...' : (editingStudent ? 'Update' : 'Create') }}
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
