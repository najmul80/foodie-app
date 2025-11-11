<template>
  <Layout>
    <div class="max-w-4xl mx-auto py-12 px-4">
      <div class="bg-white rounded-xl shadow-lg p-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-8">Create New Blog Post</h1>
        <BlogForm @submit="handleCreate" :is-loading="isLoading" />
      </div>
    </div>
  </Layout>
</template>

<script setup>
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import { useToast } from 'vue-toastification';
import Layout from '@/components/Layout.vue';
import BlogForm from '@/components/BlogForm.vue';
import ApiService from '@/services/ApiService';

const router = useRouter();
const toast = useToast();
const isLoading = ref(false);

const handleCreate = async (formData) => {
  isLoading.value = true;
  try {
    const response = await ApiService.post('/blog', formData, {
      headers: { 'Content-Type': 'multipart/form-data' }
    });
    toast.success('Blog post created successfully!');
    router.push(`/blog/${response.data.data.slug}`);
  } catch (error) {
    toast.error(error.response?.data?.message || 'Failed to create post.');
  } finally {
    isLoading.value = false;
  }
};
</script>