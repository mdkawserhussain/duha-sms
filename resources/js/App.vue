<template>
  <Toast />
  <router-view />
</template>

<script setup>
import { onMounted } from 'vue';
import { useAuthStore } from './stores/auth';
import Toast from './components/Toast.vue';

const authStore = useAuthStore();

onMounted(async () => {
  if (authStore.token) {
    await authStore.fetchUser();
  }
});
</script>
