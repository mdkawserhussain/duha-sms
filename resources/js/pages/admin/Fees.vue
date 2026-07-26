<template>
  <div>
    <div class="flex items-center justify-between mb-6">
      <h1 class="text-2xl font-bold text-gray-900">Fee Management</h1>
      <button @click="openStructureModal()" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 text-sm font-medium">Add Fee Structure</button>
    </div>

    <!-- Tabs -->
    <div class="flex border-b border-gray-200 mb-6">
      <button @click="activeTab = 'structures'" :class="activeTab === 'structures' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500'" class="px-4 py-2 border-b-2 text-sm font-medium">Fee Structures</button>
      <button @click="activeTab = 'records'" :class="activeTab === 'records' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500'" class="px-4 py-2 border-b-2 text-sm font-medium">Payment Records</button>
    </div>

    <!-- Fee Structures -->
    <div v-if="activeTab === 'structures'" class="bg-white rounded-lg shadow overflow-hidden">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Class</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Amount</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Term</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Due Date</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
          <tr v-for="fs in structures" :key="fs.id">
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ fs.name }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ fs.class?.name || 'All Classes' }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ formatCurrency(fs.amount) }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ fs.term }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ fs.due_date }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm">
              <button @click="openStructureModal(fs)" class="text-indigo-600 hover:text-indigo-900 mr-3">Edit</button>
              <button @click="deleteStructure(fs.id)" class="text-red-600 hover:text-red-900">Delete</button>
            </td>
          </tr>
          <tr v-if="!structures.length">
            <td colspan="6" class="px-6 py-12 text-center text-sm text-gray-500">No fee structures found</td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Payment Records -->
    <div v-if="activeTab === 'records'">
      <div class="bg-white rounded-lg shadow p-4 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Student</label>
            <input v-model="searchStudent" @input="searchStudentFees" placeholder="Search student..." class="w-full rounded-md border-gray-300 text-sm" />
          </div>
        </div>
      </div>
      <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Student</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fee</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Amount</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Paid</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            <tr v-for="rec in records" :key="rec.id">
              <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ rec.student?.first_name }} {{ rec.student?.last_name }}</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ rec.fee_structure?.name }}</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ formatCurrency(rec.amount) }}</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ formatCurrency(rec.amount_paid) }}</td>
              <td class="px-6 py-4 whitespace-nowrap">
                <span :class="rec.status === 'paid' ? 'bg-green-100 text-green-800' : rec.status === 'partial' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800'" class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full">{{ rec.status }}</span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm">
                <button v-if="rec.status !== 'paid'" @click="markPaid(rec)" class="text-green-600 hover:text-green-900">Mark Paid</button>
              </td>
            </tr>
            <tr v-if="!records.length">
              <td colspan="6" class="px-6 py-12 text-center text-sm text-gray-500">No payment records found</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Fee Structure Modal -->
    <div v-if="showModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center z-50">
      <div class="bg-white rounded-lg shadow-xl max-w-lg w-full mx-4 p-6">
        <h2 class="text-lg font-semibold mb-4">{{ editing ? 'Edit' : 'Add' }} Fee Structure</h2>
        <form @submit.prevent="saveStructure">
          <div class="space-y-4">
            <div><label class="block text-sm font-medium text-gray-700 mb-1">Name</label><input v-model="form.name" required class="w-full rounded-md border-gray-300 text-sm" /></div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Class (optional)</label>
              <select v-model="form.class_id" class="w-full rounded-md border-gray-300 text-sm">
                <option value="">All Classes</option>
                <option v-for="cls in classes" :key="cls.id" :value="cls.id">{{ cls.name }}</option>
              </select>
            </div>
            <div class="grid grid-cols-2 gap-4">
              <div><label class="block text-sm font-medium text-gray-700 mb-1">Amount</label><input type="number" v-model="form.amount" required min="0" step="0.01" class="w-full rounded-md border-gray-300 text-sm" /></div>
              <div><label class="block text-sm font-medium text-gray-700 mb-1">Term</label><select v-model="form.term" required class="w-full rounded-md border-gray-300 text-sm"><option value="1st Term">1st Term</option><option value="2nd Term">2nd Term</option><option value="3rd Term">3rd Term</option></select></div>
            </div>
            <div><label class="block text-sm font-medium text-gray-700 mb-1">Due Date</label><input type="date" v-model="form.due_date" required class="w-full rounded-md border-gray-300 text-sm" /></div>
          </div>
          <div class="mt-6 flex justify-end gap-3">
            <button type="button" @click="showModal = false" class="px-4 py-2 border rounded-md text-sm">Cancel</button>
            <button type="submit" :disabled="saving" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 text-sm disabled:opacity-50">{{ saving ? 'Saving...' : 'Save' }}</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import api from '../../services/api';

const activeTab = ref('structures');
const structures = ref([]);
const records = ref([]);
const classes = ref([]);
const showModal = ref(false);
const editing = ref(null);
const saving = ref(false);
const searchStudent = ref('');
const form = ref({ name: '', class_id: '', amount: '', term: '1st Term', due_date: '' });

const formatCurrency = (v) => v != null ? new Intl.NumberFormat('en-US', { style: 'currency', currency: 'NPR' }).format(v) : '-';

const fetchClasses = async () => { try { const r = await api.get('/admin/classes', { params: { per_page: 100 } }); classes.value = r.data.data || r.data; } catch (e) { console.error(e); } };
const fetchStructures = async () => { try { const r = await api.get('/admin/fee-structures'); structures.value = r.data.data || r.data; } catch (e) { console.error(e); } };
const searchStudentFees = async () => {
  if (!searchStudent.value) { records.value = []; return; }
  try { const r = await api.get(`/admin/fees/student/${searchStudent.value}`); records.value = r.data.data || r.data || []; } catch (e) { records.value = []; }
};
const openStructureModal = (fs = null) => {
  if (fs) { editing.value = fs.id; form.value = { name: fs.name, class_id: fs.class_id || '', amount: fs.amount, term: fs.term, due_date: fs.due_date }; }
  else { editing.value = null; form.value = { name: '', class_id: '', amount: '', term: '1st Term', due_date: '' }; }
  showModal.value = true;
};
const saveStructure = async () => {
  saving.value = true;
  try {
    if (editing.value) await api.put(`/admin/fee-structures/${editing.value}`, form.value);
    else await api.post('/admin/fee-structures', form.value);
    showModal.value = false; fetchStructures();
  } catch (e) { alert(e.response?.data?.message || 'Error saving'); }
  finally { saving.value = false; }
};
const deleteStructure = async (id) => { if (!confirm('Delete?')) return; try { await api.delete(`/admin/fee-structures/${id}`); fetchStructures(); } catch (e) { alert(e.response?.data?.message || 'Error'); } };
const markPaid = async (rec) => {
  if (!confirm('Mark this fee as paid?')) return;
  try { await api.post(`/admin/fees/${rec.id}/pay`, { amount: rec.amount - rec.amount_paid }); searchStudentFees(); } catch (e) { alert(e.response?.data?.message || 'Error'); }
};
onMounted(() => { fetchClasses(); fetchStructures(); });
</script>
