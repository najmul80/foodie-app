<template>
  <Layout>
    <div class="max-w-2xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
      <div class="bg-white rounded-xl shadow-lg p-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-8 text-center">My Profile</h1>

        <form @submit.prevent="updateProfile">
          <div class="flex flex-col items-center gap-4 mb-8">
            <img :src="previewImage || authStore.currentUser?.profile_image_url || defaultAvatar" class="w-32 h-32 rounded-full object-cover border-4 border-gray-100 shadow-sm">
            <div>
              <label for="profile_image" class="cursor-pointer bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50">
                <span>Change Photo</span>
                <input id="profile_image" @change="onFileChange" type="file" class="sr-only" accept="image/*">
              </label>
            </div>
          </div>

          <div class="space-y-6">
            <div>
              <label for="name" class="form-label">Name</label>
              <input v-model="form.name" type="text" id="name" class="form-input">
            </div>
            <div>
              <label for="email" class="form-label">Email</label>
              <input v-model="form.email" type="email" id="email" class="form-input">
            </div>
            <div>
              <label for="bio" class="form-label">Bio</label>
              <textarea v-model="form.bio" id="bio" rows="4" class="form-input" placeholder="Tell us a little about yourself..."></textarea>
            </div>
          </div>

          <div class="pt-8">
            <button type="submit" :disabled="isLoading" class="w-full flex justify-center py-3 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-teal-600 hover:bg-teal-700 disabled:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500">
              {{ isLoading ? 'Saving...' : 'Save Changes' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </Layout>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import Layout from '@/components/Layout.vue';
import { useAuthStore } from '@/store/authStore';
import { useToast } from 'vue-toastification';

const authStore = useAuthStore();
const toast = useToast();
const router = useRouter();

const defaultAvatar = '/No_Image.jpg';

const form = ref({
  name: '',
  email: '',
  bio: '',
  profile_image: null,
});

const previewImage = ref(null);
const isLoading = ref(false);

onMounted(() => {
  if (authStore.currentUser) {
    form.value.name = authStore.currentUser.name;
    form.value.email = authStore.currentUser.email;
    form.value.bio = authStore.currentUser.bio || '';
  }
});

const onFileChange = (e) => {
  const file = e.target.files[0];
  if (file) {
    form.value.profile_image = file;
    const reader = new FileReader();
    reader.onload = (e) => {
      previewImage.value = e.target.result;
    };
    reader.readAsDataURL(file);
  }
};

const updateProfile = async () => {
  if (!authStore.currentUser) {
    toast.error("You are not logged in.");
    return;
  }
  
  isLoading.value = true;
  const formData = new FormData();

  const currentUserBio = authStore.currentUser.bio || '';
  const formBio = form.value.bio || '';

  if (form.value.name !== authStore.currentUser.name) formData.append('name', form.value.name);
  if (form.value.email !== authStore.currentUser.email) formData.append('email', form.value.email);
  if (formBio !== currentUserBio) formData.append('bio', formBio);
  if (form.value.profile_image) formData.append('profile_image', form.value.profile_image);

  if ([...formData.entries()].length === 0) {
    toast.info("No changes to save.");
    isLoading.value = false;
    return;
  }

  try {
    await authStore.updateProfile(formData);
    await authStore.fetchUser();
    toast.success('Profile updated successfully!');
    // After successful update, clear the preview image so the new URL from the store is used
    previewImage.value = null; 
  } catch (error) {
    toast.error(error.response?.data?.message || 'Failed to update profile.');
  } finally {
    isLoading.value = false;
    form.value.profile_image = null;
  }
};
</script>

<style scoped>
.form-label { @apply block text-sm font-medium text-gray-800 mb-1; }
.form-input { @apply block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-teal-500 focus:border-teal-500 sm:text-sm; }
</style>