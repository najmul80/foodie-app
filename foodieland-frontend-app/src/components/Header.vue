<template>
  <header class="bg-white border-b border-gray-200 sticky top-0 z-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex justify-between items-center h-16">
        <RouterLink to="/" class="flex-shrink-0 flex items-center">
          <span class="text-2xl font-bold text-gray-900">Foodieland</span>
        </RouterLink>

        <nav class="hidden md:flex items-center space-x-8">
          <RouterLink to="/" class="nav-link group">Home<span class="nav-link-underline"></span></RouterLink>
          <RouterLink to="/recipes" class="nav-link group">Recipes<span class="nav-link-underline"></span></RouterLink>
          <RouterLink to="/blog" class="nav-link group">Blog<span class="nav-link-underline"></span></RouterLink>
          <RouterLink to="/contact" class="nav-link group">Contact<span class="nav-link-underline"></span></RouterLink>
          <RouterLink to="/about" class="nav-link group">About us<span class="nav-link-underline"></span></RouterLink>
        </nav>

        <div class="hidden md:flex items-center space-x-4">
          <template v-if="authStore.isAuthenticated && authStore.currentUser">
            <div class="flex items-center gap-4 relative">
              <RouterLink to="/create-recipe" class="px-4 py-2 bg-teal-600 text-white rounded-lg text-sm font-semibold hover:bg-teal-700 transition">
                Add Recipe
              </RouterLink>
              <div class="flex items-center gap-2 cursor-pointer" @click="toggleUserMenu">
                <img :src="authStore.currentUser.profile_image_url || defaultAvatar" alt="User" class="w-9 h-9 rounded-full object-cover border-2 border-transparent hover:border-teal-500 transition">
                <span class="text-sm font-medium text-gray-700">Hi, {{ authStore.currentUser.name.split(' ')[0] }}</span>
              </div>
              <transition name="fade">
                <div v-if="userMenuOpen" class="absolute top-12 right-0 w-48 bg-white rounded-md shadow-lg border py-1">
                  <RouterLink to="/profile" @click="closeUserMenu" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">My Profile</RouterLink>
                  <a href="#" @click.prevent="handleLogout" class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50">Logout</a>
                </div>
              </transition>
            </div>
          </template>
          <template v-else>
            <RouterLink to="/login" class="text-sm font-medium text-gray-700 hover:text-teal-600">Login</RouterLink>
            <RouterLink to="/register" class="px-4 py-2 bg-gray-900 text-white rounded-lg text-sm font-semibold hover:bg-gray-800 transition">Sign Up</RouterLink>
          </template>
          
          <div class="h-6 w-px bg-gray-200"></div>

          <div class="flex items-center space-x-3">
            <a href="#" class="social-icon"><svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z" /></svg></a>
            <a href="#" class="social-icon"><svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M23 3a10.9 10.9 0 01-3.14 1.53 4.48 4.48 0 00-7.86 3v1A10.66 10.66 0 013 4s-4 9 5 13a11.64 11.64 0 01-7 2s9 5 20 5a9.5 9.5 0 00-9-5.5c4.75 2.25 7-7 7-7s-1.1 2-3.12 3.5c2.88-1.35 5-3.5 5-5.5 0-.83-.07-1.65-.2-2.45A10.9 10.9 0 0123 3z" /></svg></a>
            <a href="#" class="social-icon"><svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><rect x="2" y="2" width="20" height="20" rx="5" ry="5" fill="none" stroke="currentColor" stroke-width="2" /><path d="M16 11.37A4 4 0 1112.63 8" stroke="currentColor" stroke-width="2" fill="none" /><circle cx="17.5" cy="6.5" r="1.5" fill="currentColor" /></svg></a>
          </div>
        </div>

        <div class="md:hidden flex items-center">
          <button @click="toggleMobileMenu" class="text-gray-700 hover:text-teal-600 focus:outline-none">
            <svg v-if="!mobileMenuOpen" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" /></svg>
            <svg v-else class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
          </button>
        </div>
      </div>
    </div>

    <transition name="fade">
      <div v-if="mobileMenuOpen" class="md:hidden bg-white border-t border-gray-200">
        <div class="px-2 pt-2 pb-3 space-y-1">
          <RouterLink to="/" @click="closeMobileMenu" class="mobile-nav-link">Home</RouterLink>
          <RouterLink to="/recipes" @click="closeMobileMenu" class="mobile-nav-link">Recipes</RouterLink>
          <RouterLink to="/blog" @click="closeMobileMenu" class="mobile-nav-link">Blog</RouterLink>
          <RouterLink to="/contact" @click="closeMobileMenu" class="mobile-nav-link">Contact</RouterLink>
          <RouterLink to="/about" @click="closeMobileMenu" class="mobile-nav-link">About us</RouterLink>
        </div>
        <div class="pt-4 pb-3 border-t border-gray-200">
          <div class="px-5">
            <template v-if="authStore.isAuthenticated && authStore.currentUser">
              <div class="flex items-center gap-3 mb-3">
                <img :src="authStore.currentUser.profile_image_url || defaultAvatar" alt="User" class="w-10 h-10 rounded-full object-cover">
                <div>
                  <p class="text-base font-medium text-gray-800">{{ authStore.currentUser.name }}</p>
                  <p class="text-sm font-medium text-gray-500">{{ authStore.currentUser.email }}</p>
                </div>
              </div>
              <RouterLink to="/profile" @click="closeMobileMenu" class="mobile-nav-link">My Profile</RouterLink>
              <RouterLink to="/create-recipe" @click="closeMobileMenu" class="mobile-nav-link">Add Recipe</RouterLink>
              <button @click="handleLogout" class="w-full text-left mobile-nav-link text-red-600">Logout</button>
            </template>
            <template v-else>
              <RouterLink to="/login" @click="closeMobileMenu" class="mobile-nav-link">Login</RouterLink>
              <RouterLink to="/register" @click="closeMobileMenu" class="mobile-nav-link">Sign Up</RouterLink>
            </template>
          </div>
        </div>
      </div>
    </transition>
  </header>
</template>

<script setup>
import { ref } from 'vue';
import { RouterLink, useRouter } from 'vue-router';
import { useAuthStore } from '@/store/authStore';

const authStore = useAuthStore();
const router = useRouter();

const mobileMenuOpen = ref(false);
const userMenuOpen = ref(false);
const defaultAvatar = '/No_Image.jpg';

const toggleMobileMenu = () => { mobileMenuOpen.value = !mobileMenuOpen.value; userMenuOpen.value = false; };
const toggleUserMenu = () => { userMenuOpen.value = !userMenuOpen.value; };
const closeMobileMenu = () => { mobileMenuOpen.value = false; };
const closeUserMenu = () => { userMenuOpen.value = false; };

const handleLogout = async () => {
  await authStore.logout();
  closeMobileMenu();
  closeUserMenu();
  router.push('/');
};
</script>

<style scoped>
.nav-link { @apply text-sm font-medium text-gray-700 hover:text-teal-600 transition-colors relative; }
.nav-link-underline { @apply absolute -bottom-1 left-0 w-0 h-0.5 bg-teal-600 group-hover:w-full transition-all duration-300; }
.mobile-nav-link { @apply block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-gray-900 hover:bg-gray-50; }
.social-icon { @apply text-gray-700 hover:text-teal-600 transition-colors; }
.fade-enter-active, .fade-leave-active { transition: opacity 0.2s ease; }
.fade-enter-from, .fade-leave-to { opacity: 0; }
</style>