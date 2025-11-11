<template>
  <Layout>
    <div class="bg-gray-50 min-h-screen">
      <div v-if="loading" class="flex items-center justify-center min-h-[60vh]">
        <p class="text-gray-500">Loading author profile...</p>
      </div>
      <div v-else-if="error" class="flex items-center justify-center min-h-[60vh]">
        <p class="text-red-500">{{ error }}</p>
      </div>

      <div v-else-if="author" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <!-- Author Header -->
        <header class="flex flex-col sm:flex-row items-center gap-6 mb-12 pb-8 border-b border-gray-200">
          <img :src="author.profile_image_url || defaultAvatar" :alt="author.name" class="w-32 h-32 rounded-full object-cover shadow-lg flex-shrink-0">
          <div>
            <h1 class="text-4xl font-bold text-gray-900">{{ author.name }}</h1>
            <p class="text-gray-600 mt-2 max-w-2xl">{{ author.bio || 'This author has not provided a bio yet.' }}</p>
          </div>
        </header>

        <!-- Tabs for Recipes and Blogs -->
        <div class="flex border-b border-gray-200 mb-8">
          <button @click="activeTab = 'recipes'" :class="['px-6 py-3 font-semibold', activeTab === 'recipes' ? 'border-b-2 border-teal-600 text-teal-600' : 'text-gray-500 hover:text-gray-700']">
            Recipes ({{ recipes.length }})
          </button>
          <button @click="activeTab = 'blogs'" :class="['px-6 py-3 font-semibold', activeTab === 'blogs' ? 'border-b-2 border-teal-600 text-teal-600' : 'text-gray-500 hover:text-gray-700']">
            Blog Posts ({{ blogs.length }})
          </button>
        </div>

        <!-- Content based on active tab -->
        <div>
          <!-- Recipes Tab -->
          <div v-if="activeTab === 'recipes'">
            <div v-if="recipes.length > 0" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
              <RecipeCard 
                v-for="recipe in recipes" 
                :key="recipe.id" 
                :id="recipe.id"
                :title="recipe.title"
                :image="recipe.image_url || defaultPlaceholderImage"
                :time="String(recipe.cook_time + recipe.prep_time)" 
                servings="N/A" 
                :slug="recipe.slug"
                :author="recipe.author?.name"
                :authorId="recipe.author?.id"
              />
            </div>
            <div v-else class="text-center py-10 text-gray-500">This author has not published any recipes yet.</div>
          </div>

          <!-- Blogs Tab -->
          <div v-if="activeTab === 'blogs'">
            <div v-if="blogs.length > 0" class="space-y-8">
              <BlogPostCard 
                v-for="post in blogs" 
                :key="post.id" 
                :title="post.title" 
                :excerpt="post.excerpt"
                :author="post.author?.name"
                :authorId="post.author?.id" 
                :date="post.created_at"
                :image="post.image_url || defaultPlaceholderImage" 
                :slug="post.slug" 
              />
            </div>
            <div v-else class="text-center py-10 text-gray-500">This author has not published any blog posts yet.</div>
          </div>

          <!-- Pagination could be implemented here if the API paginates the results -->
        </div>
      </div>
    </div>
  </Layout>
</template>

<script setup>
import { ref, watch } from 'vue';
import { useRoute } from 'vue-router';
import ApiService from '@/services/ApiService';
import Layout from '@/components/Layout.vue';
import RecipeCard from '@/components/RecipeCard.vue';
import BlogPostCard from '@/components/BlogPostCard.vue';

const route = useRoute();
const author = ref(null);
const recipes = ref([]);
const blogs = ref([]);
const loading = ref(true);
const error = ref(null);
const activeTab = ref('recipes');
const defaultAvatar = '/No_Image.jpg';
const defaultPlaceholderImage = '/No_Image.jpg';

const fetchAuthorData = async (authorId) => {
    loading.value = true;
    error.value = null;
    try {
        const [authorRes, recipesRes, blogsRes] = await Promise.all([
            ApiService.get(`/authors/${authorId}`),
            ApiService.get(`/authors/${authorId}/recipes`),
            ApiService.get(`/authors/${authorId}/blogs`),
        ]);

        author.value = authorRes.data.data;
        recipes.value = recipesRes.data.data;
        blogs.value = blogsRes.data.data;

    } catch (err) {
        error.value = 'Failed to load author profile.';
        console.error(err);
    } finally {
        loading.value = false;
    }
};

watch(
    () => route.params.id,
    (newId) => {
        if (newId) {
            window.scrollTo(0, 0);
            fetchAuthorData(newId);
        }
    },
    { immediate: true }
);
</script>