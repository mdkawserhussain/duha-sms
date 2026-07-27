<script setup>
import { ref, onMounted, watch } from 'vue'
import api from '../../services/api'
import { useToast } from '../../composables/useToast'

const toast = useToast()

const examRoutines = ref([])
const classes = ref([])
const subjects = ref([])
const loading = ref(true)
const total = ref(0)
const page = ref(1)
const totalPages = ref(1)
const classFilter = ref('')
const dateFilter = ref('')
const showAddModal = ref(false)
const editingRoutine = ref(null)
const formErrors = ref({})
const saving = ref(false)

const form = ref({
  class_id: '',
  subject_id: '',
  exam_name: '',
  exam_date: '',
  start_time: '',
  end_time: '',
  room: '',
})

const loadExamRoutines = async () => {
  loading.value = true
  try {
    const response = await api.get('/teacher/exam-routines', {
      params: {
        page: page.value,
        class_id: classFilter.value,
        exam_date: dateFilter.value,
      },
    })
    examRoutines.value = response.data.data
    total.value = response.data.total
    totalPages.value = response.data.last_page
  } catch (error) {
    toast.error('Failed to load exam routines')
  } finally {
    loading.value = false
  }
}

const loadClasses = async () => {
  try {
    const response = await api.get('/teacher/classes')
    classes.value = response.data.data
  } catch (error) {
    toast.error('Failed to load classes')
  }
}

const loadSubjects = async () => {
  try {
    const response = await api.get('/admin/subjects')
    subjects.value = response.data.data
  } catch (error) {
    toast.error('Failed to load subjects')
  }
}

const openAddModal = () => {
  editingRoutine.value = null
  form.value = {
    class_id: '',
    subject_id: '',
    exam_name: '',
    exam_date: '',
    start_time: '',
    end_time: '',
    room: '',
  }
  formErrors.value = {}
  showAddModal.value = true
}

const editRoutine = (routine) => {
  editingRoutine.value = routine
  form.value = {
    class_id: routine.class_id,
    subject_id: routine.subject_id,
    exam_name: routine.exam_name || '',
    exam_date: routine.exam_date?.split('T')[0] || '',
    start_time: routine.start_time || '',
    end_time: routine.end_time || '',
    room: routine.room || '',
  }
  formErrors.value = {}
  showAddModal.value = true
}

const deleteRoutine = async (id) => {
  if (!confirm('Are you sure you want to delete this exam routine?')) return
  try {
    await api.delete(`/teacher/exam-routines/${id}`)
    loadExamRoutines()
  } catch (error) {
    toast.error('Failed to delete exam routine')
  }
}

const saveRoutine = async () => {
  saving.value = true
  formErrors.value = {}
  try {
    if (editingRoutine.value) {
      await api.put(`/teacher/exam-routines/${editingRoutine.value.id}`, form.value)
    } else {
      await api.post('/teacher/exam-routines', form.value)
    }
    showAddModal.value = false
    loadExamRoutines()
  } catch (error) {
    if (error.response?.status === 422) {
      formErrors.value = error.response.data.errors
    } else {
      toast.error(error.response?.data?.message || 'Failed to save exam routine')
    }
  } finally {
    saving.value = false
  }
}

const formatDate = (date) => {
  if (!date) return '-'
  return new Date(date).toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' })
}

watch([page, classFilter, dateFilter], loadExamRoutines)

onMounted(() => {
  loadExamRoutines()
  loadClasses()
  loadSubjects()
})
</script>

<template>
  <div>
    <div class="flex justify-between items-center mb-6">
      <h1 class="text-2xl font-bold text-gray-900">Exam Routines</h1>
      <button
        @click="openAddModal"
        class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700"
      >
        Add Exam Routine
      </button>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
      <div class="p-4 border-b border-gray-200">
        <div class="flex flex-col sm:flex-row gap-3">
          <select
            v-model="classFilter"
            class="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
          >
            <option value="">All Classes</option>
            <option v-for="cls in classes" :key="cls.id" :value="cls.id">
              {{ cls.name }} - {{ cls.section }}
            </option>
          </select>
          <input
            v-model="dateFilter"
            type="date"
            class="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
          />
        </div>
      </div>

      <div class="overflow-x-auto">
        <table class="w-full min-w-[800px] divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Time</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Class</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Subject</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Exam Name</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Room</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            <tr v-if="loading">
              <td colspan="7" class="px-6 py-12 text-center text-gray-500">Loading...</td>
            </tr>
            <tr v-else-if="examRoutines.length === 0">
              <td colspan="7" class="px-6 py-12 text-center text-gray-500">No exam routines found</td>
            </tr>
            <tr v-for="routine in examRoutines" :key="routine.id" class="hover:bg-gray-50">
              <td class="px-6 py-4 whitespace-nowrap text-gray-600">{{ formatDate(routine.exam_date) }}</td>
              <td class="px-6 py-4 whitespace-nowrap text-gray-600">{{ routine.start_time }} - {{ routine.end_time }}</td>
              <td class="px-6 py-4 whitespace-nowrap font-medium text-gray-900">{{ routine.class?.name }} - {{ routine.class?.section }}</td>
              <td class="px-6 py-4 whitespace-nowrap text-gray-600">{{ routine.subject?.name }}</td>
              <td class="px-6 py-4 whitespace-nowrap text-gray-600">{{ routine.exam_name || '-' }}</td>
              <td class="px-6 py-4 whitespace-nowrap text-gray-600">{{ routine.room || '-' }}</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                <button
                  @click="editRoutine(routine)"
                  class="text-indigo-600 hover:text-indigo-900 mr-3"
                >
                  Edit
                </button>
                <button
                  @click="deleteRoutine(routine.id)"
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
            Showing {{ examRoutines.length }} of {{ total }} exam routines
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
    <!-- Add/Edit Modal -->
    <div
      v-if="showAddModal"
      class="fixed inset-0 z-50 overflow-y-auto"
      aria-labelledby="modal-title"
      role="dialog"
      aria-modal="true"
    >
      <div class="relative z-10 inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
          <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
            <h3 class="text-lg font-medium text-gray-900 mb-4">
              {{ editingRoutine ? 'Edit Exam Routine' : 'Add Exam Routine' }}
            </h3>
            <form @submit.prevent="saveRoutine">
              <div class="space-y-4">
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
                  <label class="block text-sm font-medium text-gray-700">Subject *</label>
                  <select
                    v-model="form.subject_id"
                    required
                    class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                    :class="{ 'border-red-500': formErrors.subject_id }"
                  >
                    <option value="">Select Subject</option>
                    <option v-for="subject in subjects" :key="subject.id" :value="subject.id">
                      {{ subject.name }} ({{ subject.code }})
                    </option>
                  </select>
                  <p v-if="formErrors.subject_id" class="mt-1 text-sm text-red-600">{{ formErrors.subject_id[0] }}</p>
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700">Exam Name</label>
                  <input
                    v-model="form.exam_name"
                    type="text"
                    class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                    placeholder="e.g. Mid-term, Final"
                  />
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700">Exam Date *</label>
                  <input
                    v-model="form.exam_date"
                    type="date"
                    required
                    class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                    :class="{ 'border-red-500': formErrors.exam_date }"
                  />
                  <p v-if="formErrors.exam_date" class="mt-1 text-sm text-red-600">{{ formErrors.exam_date[0] }}</p>
                </div>
                <div class="grid grid-cols-2 gap-4">
                  <div>
                    <label class="block text-sm font-medium text-gray-700">Start Time *</label>
                    <input
                      v-model="form.start_time"
                      type="time"
                      required
                      class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                      :class="{ 'border-red-500': formErrors.start_time }"
                    />
                    <p v-if="formErrors.start_time" class="mt-1 text-sm text-red-600">{{ formErrors.start_time[0] }}</p>
                  </div>
                  <div>
                    <label class="block text-sm font-medium text-gray-700">End Time *</label>
                    <input
                      v-model="form.end_time"
                      type="time"
                      required
                      class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                      :class="{ 'border-red-500': formErrors.end_time }"
                    />
                    <p v-if="formErrors.end_time" class="mt-1 text-sm text-red-600">{{ formErrors.end_time[0] }}</p>
                  </div>
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700">Room</label>
                  <input
                    v-model="form.room"
                    type="text"
                    class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                    placeholder="Optional room assignment"
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
                  {{ saving ? 'Saving...' : (editingRoutine ? 'Update' : 'Create') }}
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
