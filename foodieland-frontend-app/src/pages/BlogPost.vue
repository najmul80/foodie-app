<template>
  <Layout>
    <div v-if="blogStore.loading" class="flex items-center justify-center min-h-[60vh]">
      <p class="text-gray-500">Loading Post...</p>
    </div>
    <div v-else-if="blogStore.error" class="flex items-center justify-center min-h-[60vh]">
      <p class="text-red-500">{{ blogStore.error }}</p>
    </div>

    <article v-else-if="blogStore.post" class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12 md:py-20">
      
      <div v-if="authStore.isAuthenticated && authStore.currentUser?.id === blogStore.post.author?.id" class="mb-6 flex gap-4">
        <router-link :to="`/blog/${blogStore.post.slug}/edit`" class="inline-block px-4 py-2 bg-blue-600 text-white text-sm font-semibold rounded-lg hover:bg-blue-700 transition shadow-sm">
          Edit Post
        </router-link>
        <button @click="handleDelete" class="px-4 py-2 bg-red-600 text-white text-sm font-semibold rounded-lg hover:bg-red-700 transition shadow-sm">
          Delete Post
        </button>
      </div>
      
      <header class="mb-12">
        <h1 class="text-4xl md:text-5xl font-bold text-gray-900 mb-4">{{ blogStore.post.title }}</h1>
        <div class="flex items-center justify-between py-6 border-b border-gray-200">
          <div v-if="blogStore.post.author" class="flex items-center gap-3">
            <img :src="blogStore.post.author.profile_image_url || defaultAvatar" :alt="blogStore.post.author.name" class="w-12 h-12 rounded-full object-cover" />
            <div>
              <router-link :to="`/author/${blogStore.post.author.id}`" class="font-semibold text-gray-900 hover:text-teal-600">{{ blogStore.post.author.name }}</router-link>
              <p class="text-sm text-gray-500">{{ blogStore.post.created_at }}</p>
            </div>
          </div>
          <div class="text-right">
            <p class="text-sm font-semibold text-gray-700 mb-2">SHARE THIS POST</p>
            <div class="flex items-center gap-3">
              <a href="#" class="text-gray-700 hover:text-gray-900"><svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M23 3a10.9 10.9 0 01-3.14 1.53 4.48 4.48 0 00-7.86 3v1A10.66 10.66 0 013 4s-4 9 5 13a11.64 11.64 0 01-7 2s9 5 20 5a9.5 9.5 0 00-9-5.5c4.75 2.25 7-7 7-7s-1.1 2-3.12 3.5c2.88-1.35 5-3.5 5-5.5 0-.83-.07-1.65-.2-2.45A10.9 10.9 0 0123 3z" /></svg></a>
              <a href="#" class="text-gray-700 hover:text-gray-900"><svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><rect x="2" y="2" width="20" height="20" rx="5" ry="5" fill="none" stroke="currentColor" stroke-width="2" /><path d="M16 11.37A4 4 0 1112.63 8" stroke="currentColor" stroke-width="2" fill="none" /><circle cx="17.5" cy="6.5" r="1.5" fill="currentColor" /></svg></a>
            </div>
          </div>
        </div>
      </header>

      <section class="mb-12">
        <p class="text-gray-700 leading-relaxed text-lg">{{ blogStore.post.excerpt }}</p>
      </section>

      <figure v-if="blogStore.post.image_url" class="mb-12">
        <img :src="blogStore.post.image_url" :alt="blogStore.post.title" class="w-full h-auto max-h-[500px] object-cover rounded-lg shadow-lg" />
      </figure>

      <div class="prose max-w-none lg:prose-lg" v-html="blogStore.post.content"></div>
      
      <hr class="my-16 border-gray-200" />

      <CommentSection 
        v-if="blogStore.post.comments"
        :comments="blogStore.post.comments"
        :post-slug="blogStore.post.slug"
        :store="blogStore"
      />

      <hr class="my-16 border-gray-200" />

      <section class="py-12 bg-gradient-to-b from-blue-50 to-white -mx-4 sm:-mx-6 lg:-mx-8 px-4 sm:px-6 lg:px-8 my-12 rounded-xl">
        <div class="max-w-4xl mx-auto">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-12 items-center">
            <div>
              <h2 class="text-3xl font-bold text-gray-900 mb-4">Deliciousness to your inbox</h2>
              <p class="text-gray-600 mb-6">Stay updated with our latest recipes and articles.</p>
              <div class="flex gap-2">
                <input type="email" placeholder="Your email address..." class="flex-1 px-4 py-3 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-gray-900" />
                <button class="px-6 py-3 bg-gray-900 text-white rounded hover:bg-gray-800 transition font-semibold">Subscribe</button>
              </div>
            </div>
            <div class="flex justify-end">
              <img src="https://picsum.photos/seed/blogpost-newsletter/400/300" alt="Food" class="w-80 h-48 object-cover rounded-lg" />
            </div>
          </div>
        </div>
      </section>

      <section>
        <h2 class="text-3xl font-bold text-gray-900 mb-8">Check out other delicious recipes</h2>
        <div v-if="recipeStore.loading">Loading recipes...</div>
        <div v-else class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-6">
          <RecipeCard 
            v-for="recipe in recipeStore.tastyRecipes"
            :key="recipe.id"
            :title="recipe.title" 
            :image="recipe.image_url || defaultPlaceholderImage"
            :time="String(recipe.cook_time + recipe.prep_time)"
            servings="N/A"
            :slug="recipe.slug"
            :author="recipe.author?.name"
            :authorId="recipe.author?.id"
          />
        </div>
      </section>
    </article>
  </Layout>
</template>

<script setup>
import { watch, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import Layout from '@/components/Layout.vue';
import RecipeCard from '@/components/RecipeCard.vue';
import CommentSection from '@/components/CommentSection.vue';
import { useBlogStore } from '@/store/blogStore';
import { useRecipeStore } from '@/store/recipeStore';
import { useAuthStore } from '@/store/authStore';
import { useToast } from 'vue-toastification';
import ApiService from '@/services/ApiService';
import { showConfirmation } from '@/utils/confirmation';

const route = useRoute();
const router = useRouter();
const blogStore = useBlogStore();
const recipeStore = useRecipeStore();
const authStore = useAuthStore();
const toast = useToast();

const defaultAvatar = '/No_Image.jpg';
const defaultPlaceholderImage = '/No_Image.jpg';

watch(() => route.params.slug, (newSlug) => {
    if (newSlug) {
      window.scrollTo(0, 0); 
      blogStore.fetchPostBySlug(String(newSlug));
    }
  }, { immediate: true } 
);

onMounted(() => {
  if (recipeStore.tastyRecipes.length === 0) {
    recipeStore.fetchTastyRecipes();
  }
});

const handleDelete = async () => {
  const result = await showConfirmation('Delete this post?', "This will permanently delete the blog post.");
  if (result.isConfirmed) {
    try {
      await ApiService.delete(`/blog/${route.params.slug}`);
      toast.success('Post deleted successfully.');
      router.push('/blog');
    } catch (error) {
      toast.error(error.response?.data?.message || 'Failed to delete post.');
    }
  }
};
</script>