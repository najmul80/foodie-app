<template>
  <Layout>
    <!-- Main Loading State for the entire page on first load -->
    <div v-if="recipeStore.loading && recipeStore.recipes.length === 0" class="flex items-center justify-center min-h-screen">
      <p class="text-gray-500 text-lg">Loading Foodieland...</p>
    </div>

    <!-- Main Content visible after initial data load -->
    <div v-else>
      <!-- Hero Section - Featured Recipe -->
      <section v-if="recipeStore.recipes.length > 0" class="relative bg-gradient-to-r from-cyan-50 to-white py-16 md:py-24 overflow-hidden">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-12 items-center">
            <div class="relative z-10">
              <div class="inline-flex items-center gap-2 bg-orange-100 rounded-full px-3 py-1 mb-6 shadow-sm"><div class="w-3 h-3 bg-orange-600 rounded-full animate-pulse"></div><span class="text-sm font-semibold text-orange-800">Hot Recipes</span></div>
              <h1 class="text-5xl md:text-6xl font-bold text-gray-900 mb-6 leading-tight">{{ recipeStore.recipes[0].title }}</h1>
              <p class="text-gray-600 text-base mb-6 leading-relaxed max-w-md">{{ recipeStore.recipes[0].description }}</p>
              <div class="flex flex-wrap items-center gap-6 mb-8">
                <div class="flex items-center gap-2 text-gray-700 bg-white px-3 py-2 rounded-lg shadow-sm"><svg class="w-5 h-5 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" /><path d="M12 6v6l4 2.4" /></svg><span class="font-medium">{{ recipeStore.recipes[0].prep_time + recipeStore.recipes[0].cook_time }} Minutes</span></div>
                <div v-if="recipeStore.recipes[0].categories[0]" class="flex items-center gap-2 text-gray-700 bg-white px-3 py-2 rounded-lg shadow-sm"><svg class="w-5 h-5 text-teal-600" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2z"/></svg><span class="font-medium">{{ recipeStore.recipes[0].categories[0].name }}</span></div>
              </div>
              <div v-if="recipeStore.recipes[0].author" class="flex items-center gap-3 mb-8 bg-white p-3 rounded-lg shadow-sm">
                <img :src="recipeStore.recipes[0].author.profile_image_url || defaultAvatar" :alt="recipeStore.recipes[0].author.name" class="w-11 h-11 rounded-full object-cover border-2 border-gray-200" />
                <div>
                  <router-link :to="`/author/${recipeStore.recipes[0].author.id}`" class="font-semibold text-gray-900 text-sm hover:text-teal-600">{{ recipeStore.recipes[0].author.name }}</router-link>
                  <p class="text-xs text-gray-500">{{ recipeStore.recipes[0].created_at }}</p>
                </div>
              </div>
              <router-link :to="`/recipe/${recipeStore.recipes[0].slug}`" class="inline-flex px-8 py-3 bg-gray-900 text-white rounded-full hover:bg-gray-800 transition font-semibold items-center gap-2 shadow-lg hover:shadow-xl transform hover:-translate-y-1">View Recipe<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg></router-link>
            </div>
            <div class="relative h-96 md:h-full flex items-center justify-center">
              <div class="relative w-80 h-80 group">
                <div class="absolute inset-0 rounded-full border-8 border-white shadow-2xl overflow-hidden bg-white flex items-center justify-center transform transition-transform duration-500 group-hover:scale-105">
                  <img :src="recipeStore.recipes[0].image_url || defaultPlaceholderImage" :alt="recipeStore.recipes[0].title" class="w-full h-full object-cover" />
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>

      <!-- Categories Section -->
      <section class="py-16 md:py-24">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div class="flex items-center justify-between mb-12"><h2 class="text-3xl font-bold text-gray-900">Categories</h2><router-link to="/categories" class="text-teal-600 hover:text-teal-700 font-semibold text-sm flex items-center gap-1 group">View All Categories <svg class="w-4 h-4 transform transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg></router-link></div>
          <div v-if="categoryStore.loading" class="text-gray-500">Loading categories...</div>
          <div v-else class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-6 gap-6"><router-link v-for="category in categoryStore.categories.slice(0, 6)" :key="category.id" :to="`/recipes?category=${category.slug}`" class="text-center group"><div class="w-20 h-20 bg-gray-100 rounded-xl mx-auto mb-3 flex items-center justify-center shadow-sm hover:shadow-lg transition-all duration-300 transform hover:-translate-y-2"><span class="text-3xl transform transition-transform group-hover:scale-110">🍔</span></div><p class="font-semibold text-gray-900 group-hover:text-teal-600 transition">{{ category.name }}</p></router-link></div>
        </div>
      </section>

      <!-- Simple and Tasty Recipes Section -->
      <section class="py-16 md:py-24 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div class="text-center mb-12"><h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-3">Simple and tasty recipes</h2><p class="text-gray-600">Explore our curated collection of delicious and easy-to-make recipes.</p></div>
          
          <div v-if="recipeStore.loading && recipeStore.recipes.length === 0" class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <RecipeCardSkeleton v-for="n in 9" :key="`skeleton-simple-${n}`" />
          </div>
          <div v-else-if="recipeStore.error" class="text-red-500 text-center py-10">{{ recipeStore.error }}</div>
          <div v-else-if="recipeStore.recipes.length < 1" class="text-center py-10 text-gray-500"><p>No recipes found.</p></div>
          <div v-else class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <RecipeCard v-for="recipe in recipeStore.recipes.slice(1, 10)" :key="recipe.id" :id="recipe.id" :title="recipe.title" :image="recipe.image_url || defaultPlaceholderImage" :time="String(recipe.cook_time + recipe.prep_time)" servings="N/A" :slug="recipe.slug" :authorId="recipe.author?.id" :author="recipe.author?.name" />
          </div>
        </div>
      </section>

      <!-- Chef Section -->
      <section class="py-16 md:py-24"><div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8"><div class="grid grid-cols-1 md:grid-cols-2 gap-12 items-center"><div><h2 class="text-4xl font-bold text-gray-900 mb-4">Everyone can be a chef in their own kitchen</h2><p class="text-gray-600 mb-6">Simple recipes, pro tips, and a supportive community to guide you on your culinary journey.</p><button class="px-8 py-3 bg-gray-900 text-white rounded-lg hover:bg-gray-800 transition shadow-md hover:shadow-lg transform hover:-translate-y-1">Learn More</button></div><div class="relative h-96 rounded-xl overflow-hidden shadow-xl"><img src="https://picsum.photos/seed/chef/600/600" alt="Chef" class="w-full h-full object-cover transform transition-transform duration-500 hover:scale-105" /></div></div></div></section>

      <!-- Instagram Section -->
      <section class="py-16 md:py-24 bg-gray-50"><div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8"><div class="text-center mb-12"><h2 class="text-3xl font-bold text-gray-900 mb-2">Check out @foodieland on Instagram</h2><p class="text-gray-600 text-sm">Follow us for daily food inspiration and behind-the-scenes content.</p></div><div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8"><a href="#" target="_blank" class="relative group overflow-hidden rounded-lg block"><img src="https://picsum.photos/seed/insta1/400/400" alt="Instagram Post 1" class="w-full h-48 sm:h-64 object-cover transform transition-transform duration-500 group-hover:scale-110" /><div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-40 transition-all duration-300 flex items-center justify-center"><svg class="w-10 h-10 text-white opacity-0 group-hover:opacity-90 transition-opacity duration-300" fill="currentColor" viewBox="0 0 24 24"><rect x="2" y="2" width="20" height="20" rx="5" ry="5" fill="none" stroke="currentColor" stroke-width="2" /><path d="M16 11.37A4 4 0 1112.63 8" stroke="currentColor" stroke-width="2" fill="none" /><circle cx="17.5" cy="6.5" r="1.5" fill="currentColor" /></svg></div></a><a href="#" target="_blank" class="relative group overflow-hidden rounded-lg block"><img src="https://picsum.photos/seed/insta2/400/400" alt="Instagram Post 2" class="w-full h-48 sm:h-64 object-cover transform transition-transform duration-500 group-hover:scale-110" /><div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-40 transition-all duration-300 flex items-center justify-center"><svg class="w-10 h-10 text-white opacity-0 group-hover:opacity-90 transition-opacity duration-300" fill="currentColor" viewBox="0 0 24 24"><rect x="2" y="2" width="20" height="20" rx="5" ry="5" fill="none" stroke="currentColor" stroke-width="2" /><path d="M16 11.37A4 4 0 1112.63 8" stroke="currentColor" stroke-width="2" fill="none" /><circle cx="17.5" cy="6.5" r="1.5" fill="currentColor" /></svg></div></a><a href="#" target="_blank" class="relative group overflow-hidden rounded-lg block"><img src="https://picsum.photos/seed/insta3/400/400" alt="Instagram Post 3" class="w-full h-48 sm:h-64 object-cover transform transition-transform duration-500 group-hover:scale-110" /><div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-40 transition-all duration-300 flex items-center justify-center"><svg class="w-10 h-10 text-white opacity-0 group-hover:opacity-90 transition-opacity duration-300" fill="currentColor" viewBox="0 0 24 24"><rect x="2" y="2" width="20" height="20" rx="5" ry="5" fill="none" stroke="currentColor" stroke-width="2" /><path d="M16 11.37A4 4 0 1112.63 8" stroke="currentColor" stroke-width="2" fill="none" /><circle cx="17.5" cy="6.5" r="1.5" fill="currentColor" /></svg></div></a><a href="#" target="_blank" class="relative group overflow-hidden rounded-lg block"><img src="https://picsum.photos/seed/insta4/400/400" alt="Instagram Post 4" class="w-full h-48 sm:h-64 object-cover transform transition-transform duration-500 group-hover:scale-110" /><div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-40 transition-all duration-300 flex items-center justify-center"><svg class="w-10 h-10 text-white opacity-0 group-hover:opacity-90 transition-opacity duration-300" fill="currentColor" viewBox="0 0 24 24"><rect x="2" y="2" width="20" height="20" rx="5" ry="5" fill="none" stroke="currentColor" stroke-width="2" /><path d="M16 11.37A4 4 0 1112.63 8" stroke="currentColor" stroke-width="2" fill="none" /><circle cx="17.5" cy="6.5" r="1.5" fill="currentColor" /></svg></div></a></div><div class="text-center"><a href="#" target="_blank" class="inline-block px-8 py-3 bg-gray-900 text-white rounded-lg hover:bg-gray-800 transition shadow-md hover:shadow-lg transform hover:-translate-y-1">Visit Instagram</a></div></div></section>

      <!-- More Recipes Section -->
      <section class="py-16 md:py-24">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div class="text-center mb-12"><h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-3">Try this delicious recipe to make your day</h2><p class="text-gray-600">Get inspired with our top picks for the week.</p></div>
          <div v-if="recipeStore.loading" class="grid grid-cols-1 md:grid-cols-4 gap-6"><RecipeCardSkeleton v-for="n in 8" :key="`skeleton-more-${n}`" /></div>
          <div v-else-if="recipeStore.recipes.length < 11" class="text-center text-gray-500">More recipes coming soon!</div>
          <div v-else class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-6">
            <RecipeCard v-for="recipe in recipeStore.recipes.slice(10, 18)" :key="recipe.id" :id="recipe.id" :title="recipe.title" :image="recipe.image_url || defaultPlaceholderImage" :time="String(recipe.cook_time + recipe.prep_time)" servings="N/A" :slug="recipe.slug" :authorId="recipe.author?.id" :author="recipe.author?.name" />
          </div>
        </div>
      </section>

      <!-- Newsletter Section -->
      <section class="py-16 md:py-24 bg-gradient-to-b from-blue-50 to-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-12 items-center">
            <div>
              <h2 class="text-4xl font-bold text-gray-900 mb-4">Deliciousness to your inbox</h2>
              <p class="text-gray-600 mb-6">Get weekly recipes and cooking tips delivered right to your email.</p>
              <div class="flex flex-col sm:flex-row gap-2">
                <input type="email" placeholder="Your email address..." class="flex-1 px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent" />
                <button class="px-8 py-3 bg-gray-900 text-white rounded-lg hover:bg-gray-800 transition shadow-md hover:shadow-lg transform hover:-translate-y-1">Subscribe</button>
              </div>
            </div>
            <div class="flex justify-end">
              <div class="relative rounded-xl overflow-hidden shadow-xl">
                <img src="https://picsum.photos/seed/newsletter/400/300.jpg" alt="Food" class="w-96 h-64 object-cover transform transition-transform duration-500 hover:scale-105" />
              </div>
            </div>
          </div>
        </div>
      </section>
    </div>
  </Layout>
</template>

<script setup>
import { onMounted } from 'vue';
import Layout from '@/components/Layout.vue';
import RecipeCard from '@/components/RecipeCard.vue';
import RecipeCardSkeleton from '@/components/RecipeCardSkeleton.vue';
import { useRecipeStore } from '@/store/recipeStore';
import { useCategoryStore } from '@/store/categoryStore';

const recipeStore = useRecipeStore();
const categoryStore = useCategoryStore();
const defaultAvatar = '/No_Image.jpg';
const defaultPlaceholderImage = '/No_Image.jpg';

onMounted(() => {
  if (recipeStore.recipes.length < 20) {
    recipeStore.fetchRecipes({ limit: 20 });
  }
  if (categoryStore.categories.length === 0) {
    categoryStore.fetchCategories();
  }
});
</script>