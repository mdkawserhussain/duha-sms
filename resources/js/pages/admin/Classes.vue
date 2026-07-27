<script setup>
import { ref, onMounted, watch } from 'vue'
import api from '../../services/api'
import { useToast } from '../../composables/useToast'

const toast = useToast()

const classes = ref([])
const teachers = ref([])
const loading = ref(true)
const total = ref(0)
const page = ref(1)
const totalPages = ref(1)
const search = ref('')
const showAddModal = ref(false)
const showAssignModal = ref(false)
const editingClass = ref(null)
const selectedClass = ref(null)
const formErrors = ref({})
const saving = ref(false)

const form = ref({
  name: '',
  section: '',
  capacity: 30,
  academic_year: new Date().getFullYear().toString(),
})

const assignForm = ref({
  teacher_id: '',
  is_primary: false,
})

const loadClasses = async () => {
  loading.value = true
  try {
    const response = await api.get('/admin/classes', {
      params: {
        page: page.value,
        search: search.value,
      },
    })
    classes.value = response.data.data
    total.value = response.data.total
    totalPages.value = response.data.last_page
  } catch (error) {
    toast.error('Failed to load classes')
  } finally {
    loading.value = false
  }
}

const loadTeachers = async () => {
  try {
    const response = await api.get('/admin/teachers', { params: { per_page: 100 } })
    teachers.value = response.data.data
  } catch (error) {
    toast.error('Failed to load teachers')
  }
}

const openAddModal = () => {
  editingClass.value = null
  form.value = {
    name: '',
    section: '',
    capacity: 30,
    academic_year: new Date().getFullYear().toString(),
  }
  formErrors.value = {}
  showAddModal.value = true
}

const editClass = (cls) => {
  editingClass.value = cls
  form.value = {
    name: cls.name,
    section: cls.section,
    capacity: cls.capacity,
    academic_year: cls.academic_year,
  }
  formErrors.value = {}
  showAddModal.value = true
}

const deleteClass = async (id) => {
  if (!confirm('Are you sure you want to delete this class?')) return
  try {
    await api.delete(`/admin/classes/${id}`)
    loadClasses()
  } catch (error) {
    toast.error('Failed to delete class')
  }
}

const saveClass = async () => {
  saving.value = true
  formErrors.value = {}
  try {
    if (editingClass.value) {
      await api.put(`/admin/classes/${editingClass.value.id}`, form.value)
    } else {
      await api.post('/admin/classes', form.value)
    }
    showAddModal.value = false
    loadClasses()
  } catch (error) {
    if (error.response?.status === 422) {
      formErrors.value = error.response.data.errors
    } else {
      toast.error('Failed to save class')
    }
  } finally {
    saving.value = false
  }
}

const openAssignModal = (cls) => {
  selectedClass.value = cls
  assignForm.value = { teacher_id: '', is_primary: false }
  showAssignModal.value = true
}

const assignTeacher = async () => {
  if (!assignForm.value.teacher_id) return
  try {
    await api.post(`/admin/classes/${selectedClass.value.id}/assign-teacher`, assignForm.value)
    showAssignModal.value = false
    loadClasses()
  } catch (error) {
    toast.error('Failed to assign teacher')
  }
}

const removeTeacher = async (classId, teacherId) => {
  if (!confirm('Remove this teacher from the class?')) return
  try {
    await api.delete(`/admin/classes/${classId}/remove-teacher/${teacherId}`)
    loadClasses()
  } catch (error) {
    toast.error('Failed to remove teacher')
  }
}

watch([page, search], loadClasses)

onMounted(() => {
  loadClasses()
  loadTeachers()
})
</script>

<template>
  <div>
    <div class="flex justify-between items-center mb-6">
      <h1 class="text-2xl font-bold text-gray-900">Classes</h1>
      <button
        @click="openAddModal"
        class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700"
      >
        Add Class
      </button>
    </div>

    <div class="mb-4">
      <input
        v-model="search"
        type="text"
        placeholder="Search classes..."
        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
      />
    </div>

    <div v-if="loading" class="text-center py-12">Loading...</div>

    <div v-else-if="classes.length === 0" class="text-center py-12 text-gray-500">
      No classes found
    </div>

    <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
      <div
        v-for="cls in classes"
        :key="cls.id"
        class="bg-white rounded-lg shadow p-6"
      >
        <div class="flex justify-between items-start">
          <div>
            <h3 class="text-lg font-semibold text-gray-900">{{ cls.name }}</h3>
            <p class="text-sm text-gray-500">Section: {{ cls.section }}</p>
          </div>
          <span
            :class="[
              'px-2 py-1 text-xs font-medium rounded-full',
              cls.status ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'
            ]"
          >
            {{ cls.status ? 'Active' : 'Inactive' }}
          </span>
        </div>

        <div class="mt-4 space-y-2">
          <p class="text-sm text-gray-600">
            <span class="font-medium">Capacity:</span> {{ cls.students?.length || 0 }} / {{ cls.capacity }} students
          </p>
          <div class="w-full bg-gray-200 rounded-full h-2">
            <div
              class="bg-indigo-600 h-2 rounded-full"
              :style="{ width: `${Math.min((cls.students?.length || 0) / cls.capacity * 100, 100)}%` }"
            />
          </div>
          <p class="text-sm text-gray-600">
            <span class="font-medium">Academic Year:</span> {{ cls.academic_year }}
          </p>
        </div>

        <div class="mt-4">
          <p class="text-sm font-medium text-gray-700 mb-2">Teachers:</p>
          <div v-if="cls.teachers?.length" class="space-y-1">
            <div
              v-for="teacher in cls.teachers"
              :key="teacher.id"
              class="flex justify-between items-center text-sm"
            >
              <span class="text-gray-600">
                {{ teacher.name }}
                <span v-if="teacher.pivot?.is_primary" class="text-indigo-600">(Primary)</span>
              </span>
              <button
                @click="removeTeacher(cls.id, teacher.id)"
                class="text-red-500 hover:text-red-700 text-xs"
              >
                Remove
              </button>
            </div>
          </div>
          <p v-else class="text-sm text-gray-400">No teachers assigned</p>
          <button
            @click="openAssignModal(cls)"
            class="mt-2 text-sm text-indigo-600 hover:text-indigo-800"
          >
            + Assign Teacher
          </button>
        </div>

        <div class="mt-4 pt-4 border-t border-gray-200 flex space-x-2">
          <button
            @click="editClass(cls)"
            class="text-indigo-600 hover:text-indigo-900 text-sm"
          >
            Edit
          </button>
          <button
            @click="deleteClass(cls.id)"
            class="text-red-600 hover:text-red-900 text-sm"
          >
            Delete
          </button>
        </div>
      </div>
    </div>

    <div class="mt-4 flex justify-between items-center">
      <span class="text-sm text-gray-700">
        Showing {{ classes.length }} of {{ total }} classes
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
              {{ editingClass ? 'Edit Class' : 'Add Class' }}
            </h3>
            <form @submit.prevent="saveClass">
              <div class="space-y-4">
                <div>
                  <label class="block text-sm font-medium text-gray-700">Name *</label>
                  <input
                    v-model="form.name"
                    type="text"
                    required
                    placeholder="e.g., Class 1"
                    class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                    :class="{ 'border-red-500': formErrors.name }"
                  />
                  <p v-if="formErrors.name" class="mt-1 text-sm text-red-600">{{ formErrors.name[0] }}</p>
                </div>
                <div class="grid grid-cols-2 gap-4">
                  <div>
                    <label class="block text-sm font-medium text-gray-700">Section *</label>
                    <input
                      v-model="form.section"
                      type="text"
                      required
                      placeholder="e.g., A"
                      maxlength="10"
                      class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                      :class="{ 'border-red-500': formErrors.section }"
                    />
                    <p v-if="formErrors.section" class="mt-1 text-sm text-red-600">{{ formErrors.section[0] }}</p>
                  </div>
                  <div>
                    <label class="block text-sm font-medium text-gray-700">Capacity *</label>
                    <input
                      v-model.number="form.capacity"
                      type="number"
                      required
                      min="1"
                      class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                      :class="{ 'border-red-500': formErrors.capacity }"
                    />
                    <p v-if="formErrors.capacity" class="mt-1 text-sm text-red-600">{{ formErrors.capacity[0] }}</p>
                  </div>
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700">Academic Year *</label>
                  <input
                    v-model="form.academic_year"
                    type="text"
                    required
                    maxlength="4"
                    placeholder="2024"
                    class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                    :class="{ 'border-red-500': formErrors.academic_year }"
                  />
                  <p v-if="formErrors.academic_year" class="mt-1 text-sm text-red-600">{{ formErrors.academic_year[0] }}</p>
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
                  {{ saving ? 'Saving...' : (editingClass ? 'Update' : 'Create') }}
                </button>
              </div>
            </form>
          </div>
        </div>
    </div>
    <!-- Assign Teacher Modal -->
    <div v-if="showAssignModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center z-50">
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md sm:w-full">
          <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
            <h3 class="text-lg font-medium text-gray-900 mb-4">
              Assign Teacher to {{ selectedClass?.name }}
            </h3>
            <form @submit.prevent="assignTeacher">
              <div class="space-y-4">
                <div>
                  <label class="block text-sm font-medium text-gray-700">Teacher *</label>
                  <select
                    v-model="assignForm.teacher_id"
                    required
                    class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                  >
                    <option value="">Select Teacher</option>
                    <option v-for="teacher in teachers" :key="teacher.id" :value="teacher.id">
                      {{ teacher.name }}
                    </option>
                  </select>
                </div>
                <div class="flex items-center">
                  <input
                    v-model="assignForm.is_primary"
                    type="checkbox"
                    id="is_primary"
                    class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
                  />
                  <label for="is_primary" class="ml-2 block text-sm text-gray-700">
                    Primary teacher
                  </label>
                </div>
              </div>
              <div class="mt-6 flex justify-end space-x-3">
                <button
                  type="button"
                  @click="showAssignModal = false"
                  class="px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50"
                >
                  Cancel
                </button>
                <button
                  type="submit"
                  class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-medium hover:bg-indigo-700"
                >
                  Assign
                </button>
              </div>
            </form>
          </div>
        </div>
    </div>
  </div>
</template>
