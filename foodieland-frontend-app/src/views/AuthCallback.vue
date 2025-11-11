<template>
  <div class="flex items-center justify-center min-h-screen">
    <div>Loading...</div>
  </div>
</template>

<script setup>
import { onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { useAuthStore } from '@/store/authStore';

const route = useRoute();
const router = useRouter();
const authStore = useAuthStore();

onMounted(() => {
  const token = route.query.token;
  const userString = route.query.user;

  if (token && userString) {
    try {
      const user = JSON.parse(decodeURIComponent(userString));
      // Use the setAuth action to save the user and token
      authStore.setAuth(user, token);
      // Redirect to the homepage
      router.push('/');
    } catch (e) {
      // If data is corrupted, go to login page
      router.push('/login?error=auth_failed');
    }
  } else {
    // If no token, go to login page
    router.push('/login?error=auth_failed');
  }
});
</script>