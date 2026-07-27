<script setup>
import { ref, onMounted } from 'vue'
import api from '../../services/api'
import { useToast } from '../../composables/useToast'

const toast = useToast()

const classes = ref([])
const sourceStudents = ref([])
const loadingStudents = ref(false)
const processing = ref(false)

const selectedSourceClass = ref('')
const selectedTargetClass = ref('')
const academicYear = ref(new Date().getFullYear().toString())
const remarks = ref('')
const selectedStudents = ref([])

const history = ref([])
const loadingHistory = ref(false)

const loadClasses = async () => {
  try {
    const response = await api.get('/admin/classes')
    classes.value = response.data.data
  } catch (error) {
    toast.error('Failed to load classes:')
  }
}

const loadStudents = async () => {
  if (!selectedSourceClass.value) {
    sourceStudents.value = []
    return
  }
  loadingStudents.value = true
  selectedStudents.value = []
  try {
    const response = await api.get('/admin/promotions/by-class', {
      params: { class_id: selectedSourceClass.value },
    })
    sourceStudents.value = response.data
  } catch (error) {
    toast.error('Failed to load students:')
  } finally {
    loadingStudents.value = false
  }
}

const loadHistory = async () => {
  loadingHistory.value = true
  try {
    const response = await api.get('/admin/promotions', { params: { academic_year: academicYear.value } })
    history.value = response.data.data
  } catch (error) {
    toast.error('Failed to load history:')
  } finally {
    loadingHistory.value = false
  }
}

const toggleSelectAll = () => {
  if (selectedStudents.value.length === sourceStudents.value.length) {
    selectedStudents.value = []
  } else {
    selectedStudents.value = sourceStudents.value.map(s => s.id)
  }
}

const toggleStudent = (id) => {
  const idx = selectedStudents.value.indexOf(id)
  if (idx >= 0) {
    selectedStudents.value.splice(idx, 1)
  } else {
    selectedStudents.value.push(id)
  }
}

const promote = async (action) => {
  if (selectedStudents.value.length === 0) {
    toast.error('Please select students first')
    return
  }
  if (action === 'promoted' && !selectedTargetClass.value) {
    toast.error('Please select a target class for promotion')
    return
  }

  const label = action === 'promoted' ? 'promote' : action === 'retained' ? 'retain' : 'withdraw'
  if (!confirm(`Are you sure you want to ${label} ${selectedStudents.value.length} student(s)?`)) return

  processing.value = true
  try {
    const response = await api.post('/admin/promotions', {
      student_ids: selectedStudents.value,
      action,
      to_class_id: action === 'promoted' ? selectedTargetClass.value : null,
      academic_year: academicYear.value,
      remarks: remarks.value,
    })
    toast.success(response.data.message)
    selectedStudents.value = []
    loadStudents()
    loadHistory()
  } catch (error) {
    toast.error(error.response?.data?.message || 'Operation failed')
  } finally {
    processing.value = false
  }
}

const getClassName = (id) => {
  const cls = classes.value.find(c => c.id === id)
  return cls ? `${cls.name} - ${cls.section}` : '-'
}

const getStudentName = (id) => {
  const s = sourceStudents.value.find(st => st.id === id)
  return s ? s.name : `Student #${id}`
}

const actionLabel = (action) => {
  return { promoted: 'Promoted', retained: 'Retained', withdrawn: 'Withdrawn' }[action] || action
}

const actionColor = (action) => {
  return {
    promoted: 'bg-green-100 text-green-800',
    retained: 'bg-yellow-100 text-yellow-800',
    withdrawn: 'bg-red-100 text-red-800',
  }[action] || 'bg-gray-100 text-gray-800'
}

onMounted(() => {
  loadClasses()
  loadHistory()
})
</script>

<template>
  <div>
    <h1 class="text-2xl font-bold text-gray-900 mb-6">Student Promotion / Retention</h1>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
      <!-- Left: Promotion Form -->
      <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Promote / Retain Students</h2>

        <div class="space-y-4">
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Academic Year</label>
              <input
                v-model="academicYear"
                type="text"
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
              />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Source Class *</label>
              <select
                v-model="selectedSourceClass"
                @change="loadStudents"
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
              >
                <option value="">Select Class</option>
                <option v-for="cls in classes" :key="cls.id" :value="cls.id">
                  {{ cls.name }} - {{ cls.section }}
                </option>
              </select>
            </div>
          </div>

          <div v-if="selectedSourceClass">
            <label class="block text-sm font-medium text-gray-700 mb-1">
              Target Class (for promotion)
            </label>
            <select
              v-model="selectedTargetClass"
              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
            >
              <option value="">Select Target Class</option>
              <option v-for="cls in classes" :key="cls.id" :value="cls.id">
                {{ cls.name }} - {{ cls.section }} ({{ cls.capacity }} capacity)
              </option>
            </select>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Remarks</label>
            <textarea
              v-model="remarks"
              rows="2"
              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
              placeholder="Optional remarks"
            />
          </div>
        </div>

        <!-- Student Selection -->
        <div v-if="selectedSourceClass" class="mt-4">
          <div class="flex items-center justify-between mb-2">
            <h3 class="text-sm font-medium text-gray-700">
              Students ({{ selectedStudents.length }}/{{ sourceStudents.length }} selected)
            </h3>
            <button
              @click="toggleSelectAll"
              class="text-sm text-indigo-600 hover:text-indigo-800"
            >
              {{ selectedStudents.length === sourceStudents.length ? 'Deselect All' : 'Select All' }}
            </button>
          </div>

          <div v-if="loadingStudents" class="text-center py-4 text-gray-500">Loading students...</div>
          <div v-else-if="sourceStudents.length === 0" class="text-center py-4 text-gray-500">No active students in this class</div>
          <div v-else class="max-h-64 overflow-y-auto border border-gray-200 rounded-lg">
            <label
              v-for="student in sourceStudents"
              :key="student.id"
              class="flex items-center px-3 py-2 hover:bg-gray-50 cursor-pointer border-b border-gray-100 last:border-0"
            >
              <input
                type="checkbox"
                :checked="selectedStudents.includes(student.id)"
                @change="toggleStudent(student.id)"
                class="h-4 w-4 text-indigo-600 rounded"
              />
              <span class="ml-2 text-sm text-gray-700">{{ student.name }}</span>
              <span v-if="student.guardian" class="ml-auto text-xs text-gray-500">{{ student.guardian.name }}</span>
            </label>
          </div>
        </div>

        <!-- Action Buttons -->
        <div v-if="selectedStudents.length > 0" class="mt-4 flex flex-wrap gap-2">
          <button
            @click="promote('promoted')"
            :disabled="processing"
            class="px-4 py-2 bg-green-600 text-white rounded-lg text-sm font-medium hover:bg-green-700 disabled:opacity-50"
          >
            {{ processing ? 'Processing...' : 'Promote Selected' }}
          </button>
          <button
            @click="promote('retained')"
            :disabled="processing"
            class="px-4 py-2 bg-yellow-600 text-white rounded-lg text-sm font-medium hover:bg-yellow-700 disabled:opacity-50"
          >
            {{ processing ? 'Processing...' : 'Retain Selected' }}
          </button>
          <button
            @click="promote('withdrawn')"
            :disabled="processing"
            class="px-4 py-2 bg-red-600 text-white rounded-lg text-sm font-medium hover:bg-red-700 disabled:opacity-50"
          >
            {{ processing ? 'Processing...' : 'Withdraw Selected' }}
          </button>
        </div>
      </div>

      <!-- Right: History -->
      <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center justify-between mb-4">
          <h2 class="text-lg font-semibold text-gray-900">Promotion History</h2>
          <button @click="loadHistory" class="text-sm text-indigo-600 hover:text-indigo-800">Refresh</button>
        </div>

        <div v-if="loadingHistory" class="text-center py-8 text-gray-500">Loading...</div>
        <div v-else-if="history.length === 0" class="text-center py-8 text-gray-500">No promotion records found</div>
        <div v-else class="space-y-3 max-h-[500px] overflow-y-auto">
          <div
            v-for="record in history"
            :key="record.id"
            class="border border-gray-200 rounded-lg p-3"
          >
            <div class="flex items-center justify-between">
              <span class="font-medium text-gray-900 text-sm">{{ record.student?.name }}</span>
              <span :class="['px-2 py-1 text-xs font-medium rounded-full', actionColor(record.action)]">
                {{ actionLabel(record.action) }}
              </span>
            </div>
            <div class="mt-1 text-xs text-gray-500">
              {{ getClassName(record.from_class_id) }}
              <span v-if="record.to_class_id"> → {{ getClassName(record.to_class_id) }}</span>
            </div>
            <div v-if="record.remarks" class="mt-1 text-xs text-gray-400 italic">{{ record.remarks }}</div>
            <div class="mt-1 text-xs text-gray-400">
              {{ record.processed_by_user?.name }} · {{ record.created_at?.split('T')[0] }}
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
