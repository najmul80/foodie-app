<template>
  <Layout>
    <div v-if="recipeStore.loading" class="flex items-center justify-center min-h-[60vh]">
      <p class="text-gray-500">Loading Recipe...</p>
    </div>
    <div v-if="recipeStore.error" class="flex items-center justify-center min-h-[60vh]">
      <p class="text-red-500">{{ recipeStore.error }}</p>
    </div>

    <article v-if="recipeStore.recipe" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 md:py-12">
      <!-- Recipe Header -->
      <div class="mb-8">
        <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-4 mb-6">
          <h1 class="text-3xl md:text-4xl font-bold text-gray-900">{{ recipeStore.recipe.title }}</h1>
          <div class="flex items-center gap-4">
            <router-link v-if="authStore.isAuthenticated && authStore.currentUser?.id === recipeStore.recipe.author?.id" :to="`/recipe/${recipeStore.recipe.slug}/edit`" class="px-4 py-2 bg-blue-600 text-white text-sm font-semibold rounded-lg hover:bg-blue-700 transition">Edit Recipe</router-link>
          </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-4 md:p-6">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
              <div v-if="recipeStore.recipe.author" class="flex items-center gap-4">
                <img :src="recipeStore.recipe.author.profile_image_url || defaultAvatar" :alt="recipeStore.recipe.author.name" class="w-12 h-12 rounded-full object-cover border-2 border-gray-100" />
                <div>
                  <router-link :to="`/author/${recipeStore.recipe.author.id}`" class="font-semibold text-gray-900 hover:text-teal-600">{{ recipeStore.recipe.author.name }}</router-link>
                  <p class="text-sm text-gray-500">{{ recipeStore.recipe.created_at }}</p>
                </div>
              </div>
              <div class="flex flex-wrap items-center gap-4 md:gap-6 text-sm">
                <div class="flex items-center gap-2 bg-gray-50 px-3 py-2 rounded-lg"><span class="font-medium">Prep: {{ recipeStore.recipe.prep_time }} min</span></div>
                <div class="flex items-center gap-2 bg-gray-50 px-3 py-2 rounded-lg"><span class="font-medium">Cook: {{ recipeStore.recipe.cook_time }} min</span></div>
                <div class="flex items-center gap-2 bg-gray-50 px-3 py-2 rounded-lg"><span class="font-medium">{{ recipeStore.recipe.difficulty }}</span></div>
              </div>
            </div>
        </div>
      </div>

      <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Main Content -->
        <div class="lg:col-span-2">
          <div v-if="recipeStore.recipe.image_url" class="mb-8 relative rounded-xl overflow-hidden shadow-md"><img :src="recipeStore.recipe.image_url" :alt="recipeStore.recipe.title" class="w-full h-64 md:h-96 object-cover" /></div>
          <div class="mb-8 bg-white rounded-xl p-6 shadow-sm"><h2 class="text-xl font-bold text-gray-900 mb-4">Description</h2><p class="text-gray-700 leading-relaxed whitespace-pre-line">{{ recipeStore.recipe.description }}</p></div>
          <div v-if="recipeStore.recipe.ingredients && recipeStore.recipe.ingredients.length > 0" class="mb-8 bg-white rounded-xl p-6 shadow-sm"><h2 class="text-xl font-bold text-gray-900 mb-6">Ingredients</h2><ul class="space-y-3"><li v-for="(ingredient, index) in recipeStore.recipe.ingredients" :key="index" class="flex items-start gap-3"><svg class="w-5 h-5 text-teal-600 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" /></svg><span class="text-gray-700">{{ ingredient }}</span></li></ul></div>
          <div v-if="recipeStore.recipe.instructions && recipeStore.recipe.instructions.length > 0" class="mb-8 bg-white rounded-xl p-6 shadow-sm"><h2 class="text-xl font-bold text-gray-900 mb-6">Directions</h2><div class="space-y-8"><div v-for="instruction in recipeStore.recipe.instructions" :key="instruction.step" class="relative pl-12"><div class="absolute left-0 top-0 w-8 h-8 bg-teal-600 text-white rounded-full flex items-center justify-center text-sm font-bold">{{ instruction.step }}</div><p class="text-gray-700 mb-4">{{ instruction.description }}</p><img v-if="instruction.image" :src="instruction.image" :alt="`Step ${instruction.step}`" class="w-full h-auto max-h-64 object-cover rounded-lg mt-4 shadow" /></div></div></div>
          
          <!-- ========================================================= -->
          <!-- COMMENT SECTION IS NOW INCLUDED HERE -->
          <!-- ========================================================= -->
          <CommentSection 
            v-if="recipeStore.recipe.comments"
            :comments="recipeStore.recipe.comments"
            :post-slug="recipeStore.recipe.slug"
            :store="recipeStore"
          />
        </div>

        <!-- Sidebar -->
        <aside class="lg:col-span-1"><div class="sticky top-24 space-y-8"><div v-if="recipeStore.recipe.nutrition_facts" class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl p-6 shadow-sm"><h3 class="font-bold text-gray-900 mb-4">Nutrition Information</h3><div class="space-y-3 text-sm"><div v-for="(value, key) in recipeStore.recipe.nutrition_facts" :key="key" class="flex justify-between items-center"><span class="text-gray-600 capitalize">{{ key.replace('_', ' ') }}</span><span class="font-semibold bg-white px-2 py-1 rounded">{{ value }}</span></div></div></div></div></aside>
      </div>
    </article>
  </Layout>
</template>

<script setup>
import { watch } from 'vue';
import { useRoute } from 'vue-router';
import Layout from '@/components/Layout.vue';
import CommentSection from '@/components/CommentSection.vue'; 
import { useRecipeStore } from '@/store/recipeStore';
import { useAuthStore } from '@/store/authStore';

const route = useRoute();
const recipeStore = useRecipeStore();
const authStore = useAuthStore();
const defaultAvatar = '/No_Image.jpg';

watch(() => route.params.slug, (newSlug) => {
    if (newSlug) {
      window.scrollTo(0, 0);
      recipeStore.fetchRecipeBySlug(String(newSlug));
    }
  },
  { immediate: true }
);
</script>