<script setup>
import { ref, onMounted } from 'vue'
import api from '../../services/api'

const settings = ref([])
const loading = ref(true)
const saving = ref(false)
const activeGroup = ref('general')
const successMessage = ref('')

const groups = [
  { key: 'general', label: 'General' },
  { key: 'appearance', label: 'Appearance' },
  { key: 'sms', label: 'SMS Gateway' },
  { key: 'email', label: 'Email' },
]

const defaultSettings = {
  general: [
    { key: 'school_name', label: 'School Name', type: 'text', value: '' },
    { key: 'school_phone', label: 'School Phone', type: 'text', value: '' },
    { key: 'school_email', label: 'School Email', type: 'text', value: '' },
    { key: 'school_address', label: 'School Address', type: 'textarea', value: '' },
    { key: 'school_website', label: 'School Website', type: 'text', value: '' },
    { key: 'timezone', label: 'Timezone', type: 'text', value: 'Asia/Dhaka' },
    { key: 'currency', label: 'Currency', type: 'text', value: 'BDT' },
  ],
  appearance: [
    { key: 'school_logo', label: 'School Logo URL', type: 'text', value: '' },
    { key: 'primary_color', label: 'Primary Color', type: 'text', value: '#4F46E5' },
    { key: 'footer_text', label: 'Footer Text', type: 'text', value: '' },
  ],
  sms: [
    { key: 'sms_provider', label: 'SMS Provider', type: 'text', value: '' },
    { key: 'sms_api_key', label: 'API Key', type: 'text', value: '' },
    { key: 'sms_sender_id', label: 'Sender ID', type: 'text', value: '' },
    { key: 'sms_enabled', label: 'Enable SMS', type: 'boolean', value: 'false' },
  ],
  email: [
    { key: 'mail_driver', label: 'Mail Driver', type: 'text', value: 'smtp' },
    { key: 'mail_host', label: 'Mail Host', type: 'text', value: '' },
    { key: 'mail_port', label: 'Mail Port', type: 'text', value: '587' },
    { key: 'mail_username', label: 'Mail Username', type: 'text', value: '' },
    { key: 'mail_password', label: 'Mail Password', type: 'text', value: '' },
    { key: 'mail_encryption', label: 'Mail Encryption', type: 'text', value: 'tls' },
  ],
}

const loadSettings = async () => {
  loading.value = true
  try {
    const response = await api.get('/admin/settings', {
      params: { group: activeGroup.value },
    })
    const savedSettings = response.data

    // Merge saved values with defaults
    const defaults = defaultSettings[activeGroup.value] || []
    settings.value = defaults.map(defaultSetting => {
      const saved = savedSettings.find(s => s.key === defaultSetting.key)
      return saved
        ? { ...defaultSetting, value: saved.value, id: saved.id }
        : { ...defaultSetting }
    })
  } catch (error) {
    console.error('Failed to load settings:', error)
  } finally {
    loading.value = false
  }
}

const saveSettings = async () => {
  saving.value = true
  successMessage.value = ''
  try {
    const payload = settings.value.map(s => ({
      key: s.key,
      value: s.value,
      type: s.type,
      group: activeGroup.value,
    }))
    await api.put('/admin/settings', { settings: payload })
    successMessage.value = 'Settings saved successfully!'
    setTimeout(() => { successMessage.value = '' }, 3000)
  } catch (error) {
    console.error('Failed to save settings:', error)
  } finally {
    saving.value = false
  }
}

const switchGroup = (group) => {
  activeGroup.value = group
  loadSettings()
}

onMounted(loadSettings)
</script>

<template>
  <div>
    <div class="flex justify-between items-center mb-6">
      <h1 class="text-2xl font-bold text-gray-900">System Settings</h1>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
      <!-- Group Tabs -->
      <div class="border-b border-gray-200">
        <nav class="flex -mb-px">
          <button
            v-for="group in groups"
            :key="group.key"
            @click="switchGroup(group.key)"
            :class="[
              'px-6 py-3 text-sm font-medium border-b-2 whitespace-nowrap',
              activeGroup === group.key
                ? 'border-indigo-500 text-indigo-600'
                : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
            ]"
          >
            {{ group.label }}
          </button>
        </nav>
      </div>

      <!-- Settings Content -->
      <div class="p-6">
        <div v-if="loading" class="text-center py-12 text-gray-500">Loading...</div>

        <form v-else @submit.prevent="saveSettings" class="space-y-6 max-w-2xl">
          <div v-for="setting in settings" :key="setting.key">
            <label class="block text-sm font-medium text-gray-700 mb-1">
              {{ setting.label }}
            </label>
            <input
              v-if="setting.type === 'text'"
              v-model="setting.value"
              type="text"
              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
            />
            <textarea
              v-else-if="setting.type === 'textarea'"
              v-model="setting.value"
              rows="3"
              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
            />
            <select
              v-else-if="setting.type === 'boolean'"
              v-model="setting.value"
              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
            >
              <option value="true">Enabled</option>
              <option value="false">Disabled</option>
            </select>
            <input
              v-else
              v-model="setting.value"
              type="text"
              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
            />
          </div>

          <div class="flex items-center space-x-4">
            <button
              type="submit"
              :disabled="saving"
              class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-medium hover:bg-indigo-700 disabled:opacity-50"
            >
              {{ saving ? 'Saving...' : 'Save Settings' }}
            </button>
            <span v-if="successMessage" class="text-sm text-green-600">{{ successMessage }}</span>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>
