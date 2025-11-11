<template>
  <Layout>
    <!-- Header Section -->
    <section class="py-12 md:py-20 bg-white border-b border-gray-200">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h1 class="text-4xl md:text-5xl font-bold text-gray-900 mb-4 text-center">Blog & Article</h1>
        <p class="text-gray-600 text-center mb-8">Discover the latest articles, stories, and culinary inspiration from our kitchen to yours.</p>

        <form @submit.prevent="performSearch" class="flex gap-4 max-w-2xl mx-auto">
          <input 
            v-model="searchQuery"
            type="text" 
            placeholder="Search article, news or recipe..." 
            class="flex-1 px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900"
          />
          <button type="submit" class="px-8 py-3 bg-gray-900 text-white rounded-lg hover:bg-gray-800 transition font-semibold">
            Search
          </button>
        </form>

        <!-- "Create Post" Button -->
        <div class="text-center mt-8">
          <router-link to="/blog/create" v-if="authStore.isAuthenticated" class="inline-block px-6 py-3 bg-teal-600 text-white rounded-lg hover:bg-teal-700 transition font-semibold shadow hover:shadow-md">
            + Create New Post
          </router-link>
        </div>
      </div>
    </section>

    <!-- Content Section -->
    <section class="py-12 md:py-20">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
          <!-- Blog List -->
          <div class="lg:col-span-2">
            <div v-if="blogStore.loading" class="text-center py-10 text-gray-500">Loading posts...</div>
            <div v-else-if="blogStore.error" class="text-red-500 text-center py-10">{{ blogStore.error }}</div>
            <div v-else-if="!blogStore.loading && blogStore.posts.length === 0" class="text-center py-10 text-gray-500">
              <p>No blog posts found. Try a different search term or check back later!</p>
            </div>
            <div v-else class="space-y-8">
              <BlogPostCard 
                v-for="post in blogStore.posts"
                :key="post.id"
                :title="post.title || 'Untitled Post'"
                :excerpt="post.excerpt || ''"
                :author="post.author?.name || 'Unknown Author'"
                :authorId="post.author?.id"
                :date="post.created_at || ''"
                :image="post.image_url || defaultPlaceholderImage"
                :slug="post.slug"
              />
            </div>

            <!-- Pagination -->
            <div v-if="blogStore.meta && blogStore.meta.last_page > 1" class="flex justify-center items-center gap-2 mt-12">
              <button @click="changePage(blogStore.meta.current_page - 1)" :disabled="blogStore.meta.current_page === 1" class="pagination-button">&lt;</button>
              <button v-for="page in blogStore.meta.last_page" :key="page" @click="changePage(page)" :class="['pagination-button', { 'bg-gray-900 text-white': page === blogStore.meta.current_page }]">{{ page }}</button>
              <button @click="changePage(blogStore.meta.current_page + 1)" :disabled="blogStore.meta.current_page === blogStore.meta.last_page" class="pagination-button">&gt;</button>
            </div>
          </div>

          <!-- Sidebar -->
          <aside class="lg:col-span-1">
            <div class="bg-white rounded-lg sticky top-24">
              <h3 class="text-2xl font-bold text-gray-900 mb-6">Tasty Recipes</h3>
              <div v-if="recipeStore.loading && recipeStore.tastyRecipes.length === 0" class="text-gray-500">Loading...</div>
              <div v-else class="space-y-6">
                <router-link v-for="recipe in recipeStore.tastyRecipes" :key="recipe.id" :to="`/recipe/${recipe.slug}`" class="flex gap-4 group cursor-pointer">
                  <img :src="recipe.image_url || defaultPlaceholderImage" :alt="recipe.title" class="w-20 h-20 object-cover rounded" />
                  <div>
                    <h4 class="font-semibold text-gray-900 mb-1 line-clamp-2 group-hover:text-teal-600 transition">{{ recipe.title }}</h4>
                    <p class="text-sm text-gray-600">By {{ recipe.author?.name || 'Unknown' }}</p>
                  </div>
                </router-link>
              </div>
            </div>
          </aside>
        </div>
      </div>
    </section>
  </Layout>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import Layout from '@/components/Layout.vue';
import BlogPostCard from '@/components/BlogPostCard.vue';
import { useBlogStore } from '@/store/blogStore';
import { useRecipeStore } from '@/store/recipeStore';
import { useAuthStore } from '@/store/authStore';
import { useToast } from 'vue-toastification';
import ApiService from '@/services/ApiService';

const blogStore = useBlogStore();
const recipeStore = useRecipeStore();
const authStore = useAuthStore();
const toast = useToast();

const searchQuery = ref('');
const defaultPlaceholderImage = '/No_Image.jpg';

onMounted(() => {
  blogStore.fetchPosts();
  recipeStore.fetchTastyRecipes();
});

const changePage = (page) => {
  if (page > 0 && page <= blogStore.meta.last_page && page !== blogStore.meta.current_page) {
    blogStore.fetchPosts(page, searchQuery.value);
    window.scrollTo(0, 0); 
  }
};

const performSearch = () => {
  blogStore.fetchPosts(1, searchQuery.value);
};

const handleNewsletterSubscription = async () => {
  if (!newsletterEmail.value) {
    toast.error('Please enter a valid email address.');
    return;
  }
  try {
    // await ApiService.post('/subscribe', { email: newsletterEmail.value });
    toast.success('Thank you for subscribing!');
    newsletterEmail.value = '';
  } catch (error) {
    toast.error('Subscription failed. Please try again.');
  }
};
</script>

<style scoped>
.pagination-button {
  @apply w-10 h-10 border border-gray-300 text-gray-900 rounded font-semibold hover:bg-gray-100 disabled:opacity-50 disabled:cursor-not-allowed;
}
</style>