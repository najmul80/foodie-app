<template>
  <Layout>
    <div class="max-w-4xl mx-auto py-12 px-4">
      <div v-if="blogStore.loading || !blogStore.post" class="text-center py-20">Loading post data...</div>
      <div v-else class="bg-white rounded-xl shadow-lg p-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-8">Edit Blog Post</h1>
        <BlogForm :post="blogStore.post" @submit="handleUpdate" :is-loading="isLoading" />
      </div>
    </div>
  </Layout>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRouter, useRoute } from 'vue-router';
import { useToast } from 'vue-toastification';
import { useBlogStore } from '@/store/blogStore';
import Layout from '@/components/Layout.vue';
import BlogForm from '@/components/BlogForm.vue';
import ApiService from '@/services/ApiService';

const router = useRouter();
const route = useRoute();
const toast = useToast();
const blogStore = useBlogStore();
const isLoading = ref(false);

onMounted(() => {
  blogStore.fetchPostBySlug(route.params.slug);
});

const handleUpdate = async (formData) => {
  isLoading.value = true;
  formData.append('_method', 'PUT');
  try {
    const response = await ApiService.post(`/blog/${route.params.slug}`, formData, {
      headers: { 'Content-Type': 'multipart/form-data' }
    });
    toast.success('Blog post updated successfully!');
    // Update the store's copy of the post
    blogStore.post = response.data.data;
    router.push(`/blog/${response.data.data.slug}`);
  } catch (error) {
    toast.error(error.response?.data?.message || 'Failed to update post.');
  } finally {
    isLoading.value = false;
  }
};
</script>