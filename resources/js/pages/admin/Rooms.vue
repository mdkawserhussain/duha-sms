<template>
  <div>
    <div class="flex items-center justify-between mb-6">
      <h1 class="text-2xl font-bold text-gray-900">Rooms</h1>
      <button @click="openModal()" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 text-sm font-medium">Add Room</button>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow p-4 mb-6">
      <div class="flex flex-col sm:flex-row gap-3">
        <div class="flex-1">
          <input v-model="filters.search" @input="debouncedFetch" placeholder="Search rooms..." class="w-full rounded-md border-gray-300 text-sm" />
        </div>
        <div class="w-full sm:w-48">
          <select v-model="filters.status" @change="fetchRooms" class="w-full rounded-md border-gray-300 text-sm">
            <option value="">All Statuses</option>
            <option value="available">Available</option>
            <option value="maintenance">Maintenance</option>
            <option value="unavailable">Unavailable</option>
          </select>
        </div>
      </div>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Building</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Floor</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Capacity</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
          <tr v-for="room in rooms" :key="room.id">
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ room.name }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ room.building || '-' }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ room.floor || '-' }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ room.capacity }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm">
              <span :class="statusClass(room.status)" class="px-2 py-1 text-xs font-medium rounded-full">{{ room.status }}</span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm">
              <button @click="openModal(room)" class="text-indigo-600 hover:text-indigo-900 mr-3">Edit</button>
              <button @click="deleteRoom(room.id)" class="text-red-600 hover:text-red-900">Delete</button>
            </td>
          </tr>
          <tr v-if="!rooms.length">
            <td colspan="6" class="px-6 py-12 text-center text-sm text-gray-500">No rooms found</td>
          </tr>
        </tbody>
      </table>
    </div>

    <div v-if="showModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center z-50">
      <div class="bg-white rounded-lg shadow-xl max-w-lg w-full mx-4 p-6 relative z-10">
        <h2 class="text-lg font-semibold mb-4">{{ editing ? 'Edit' : 'Add' }} Room</h2>
        <form @submit.prevent="save">
          <div class="space-y-4">
            <div><label class="block text-sm font-medium text-gray-700 mb-1">Name</label><input v-model="form.name" required class="w-full rounded-md border-gray-300 text-sm" placeholder="e.g. Room 101" /></div>
            <div class="grid grid-cols-2 gap-4">
              <div><label class="block text-sm font-medium text-gray-700 mb-1">Building</label><input v-model="form.building" class="w-full rounded-md border-gray-300 text-sm" /></div>
              <div><label class="block text-sm font-medium text-gray-700 mb-1">Floor</label><input v-model="form.floor" class="w-full rounded-md border-gray-300 text-sm" /></div>
            </div>
            <div class="grid grid-cols-2 gap-4">
              <div><label class="block text-sm font-medium text-gray-700 mb-1">Capacity</label><input type="number" v-model.number="form.capacity" required min="0" class="w-full rounded-md border-gray-300 text-sm" /></div>
              <div><label class="block text-sm font-medium text-gray-700 mb-1">Status</label><select v-model="form.status" class="w-full rounded-md border-gray-300 text-sm"><option value="available">Available</option><option value="maintenance">Maintenance</option><option value="unavailable">Unavailable</option></select></div>
            </div>
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
import { useToast } from '../../composables/useToast';

const toast = useToast();

const rooms = ref([]);
const showModal = ref(false);
const editing = ref(null);
const saving = ref(false);
const filters = ref({ search: '', status: '' });
const form = ref({ name: '', building: '', floor: '', capacity: 0, status: 'available' });

let debounceTimer = null;
const debouncedFetch = () => {
  clearTimeout(debounceTimer);
  debounceTimer = setTimeout(fetchRooms, 300);
};

const statusClass = (status) => {
  if (status === 'available') return 'bg-green-100 text-green-800';
  if (status === 'maintenance') return 'bg-yellow-100 text-yellow-800';
  return 'bg-red-100 text-red-800';
};

const fetchRooms = async () => {
  try {
    const params = {};
    if (filters.value.search) params.search = filters.value.search;
    if (filters.value.status) params.status = filters.value.status;
    const r = await api.get('/admin/rooms', { params });
    rooms.value = r.data.data || r.data;
  } catch (e) { toast.error('Failed to load rooms'); }
};
const openModal = (room = null) => {
  if (room) { editing.value = room.id; form.value = { name: room.name, building: room.building || '', floor: room.floor || '', capacity: room.capacity, status: room.status }; }
  else { editing.value = null; form.value = { name: '', building: '', floor: '', capacity: 0, status: 'available' }; }
  showModal.value = true;
};
const save = async () => {
  saving.value = true;
  try {
    if (editing.value) await api.put(`/admin/rooms/${editing.value}`, form.value);
    else await api.post('/admin/rooms', form.value);
    showModal.value = false; fetchRooms();
  } catch (e) { toast.error(e.response?.data?.message || 'Error saving'); }
  finally { saving.value = false; }
};
const deleteRoom = async (id) => { if (!confirm('Delete this room?')) return; try { await api.delete(`/admin/rooms/${id}`); fetchRooms(); } catch (e) { toast.error(e.response?.data?.message || 'Error'); } };
onMounted(() => { fetchRooms(); });
</script>
