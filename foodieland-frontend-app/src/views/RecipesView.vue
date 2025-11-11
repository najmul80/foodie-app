<template>
  <Layout>
    <!-- Header Section -->
    <section class="py-12 md:py-20 bg-white border-b border-gray-200">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h1 class="text-4xl md:text-5xl font-bold text-gray-900 mb-4">All Recipes</h1>
        <p class="text-gray-600 max-w-2xl mx-auto">Find and share everyday cooking inspiration on Foodieland. Discover recipes, cooks, videos, and how-tos based on the food you love.</p>
        
        <form @submit.prevent="performSearch" class="flex gap-4 max-w-2xl mx-auto mt-8">
          <input 
            v-model="searchQuery"
            type="text" 
            placeholder="Search for any recipes..." 
            class="flex-1 px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500"
          />
          <button type="submit" class="px-8 py-3 bg-teal-600 text-white rounded-lg hover:bg-teal-700 transition font-semibold">
            Search
          </button>
        </form>
      </div>
    </section>

    <!-- Categories Filter -->
    <section class="py-8 bg-gray-50 border-b border-gray-200">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div v-if="categoryStore.loading" class="text-center text-gray-500">Loading categories...</div>
        <div v-else class="flex items-center justify-center flex-wrap gap-4">
          <button @click="filterByCategory(null)" :class="['category-button', { 'bg-teal-600 text-white': activeCategory === null }]">
            All Recipes
          </button>
          <button v-for="category in categoryStore.categories" :key="category.id" @click="filterByCategory(category.slug)" :class="['category-button', { 'bg-teal-600 text-white': activeCategory === category.slug }]">
            {{ category.name }}
          </button>
        </div>
      </div>
    </section>

    <!-- Recipes Grid -->
    <section class="py-12 md:py-20">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Skeleton Loader Grid -->
        <div v-if="recipeStore.loading" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
          <RecipeCardSkeleton v-for="n in 8" :key="`skeleton-${n}`" />
        </div>
        
        <!-- Error Message -->
        <div v-else-if="recipeStore.error" class="text-red-500 text-center py-10">{{ recipeStore.error }}</div>
        
        <!-- No Results Message -->
        <div v-else-if="recipeStore.recipes.length === 0" class="text-center py-10 text-gray-500">
          <p>No recipes found. Please try a different search or category.</p>
        </div>

        <!-- Real Data Grid -->
        <div v-else class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
          <RecipeCard 
            v-for="recipe in recipeStore.recipes"
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

        <!-- Pagination -->
        <div v-if="!recipeStore.loading && recipeStore.meta && recipeStore.meta.last_page > 1" class="flex justify-center items-center gap-2 mt-12">
          <button @click="changePage(recipeStore.meta.current_page - 1)" :disabled="recipeStore.meta.current_page === 1" class="pagination-button">&lt;</button>
          <button v-for="page in recipeStore.meta.last_page" :key="page" @click="changePage(page)" :class="['pagination-button', { 'bg-teal-600 text-white': page === recipeStore.meta.current_page }]">{{ page }}</button>
          <button @click="changePage(recipeStore.meta.current_page + 1)" :disabled="recipeStore.meta.current_page === recipeStore.meta.last_page" class="pagination-button">&gt;</button>
        </div>
      </div>
    </section>
  </Layout>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue';
import { useRoute } from 'vue-router';
import Layout from '@/components/Layout.vue';
import RecipeCard from '@/components/RecipeCard.vue';
import RecipeCardSkeleton from '@/components/RecipeCardSkeleton.vue';
import { useRecipeStore } from '@/store/recipeStore';
import { useCategoryStore } from '@/store/categoryStore';

const route = useRoute();
const recipeStore = useRecipeStore();
const categoryStore = useCategoryStore();

const searchQuery = ref('');
const activeCategory = ref(route.query.category || null);
const defaultPlaceholderImage = '/No_Image.jpg';

onMounted(() => {
  // Use the category from the URL query on initial load
  recipeStore.fetchRecipes({ categorySlug: activeCategory.value });
  categoryStore.fetchCategories();
});

// Watch for changes in the URL query to re-filter
watch(() => route.query.category, (newCategorySlug) => {
    activeCategory.value = newCategorySlug || null;
    recipeStore.fetchRecipes({ page: 1, categorySlug: activeCategory.value });
});

const changePage = (page) => {
  if (page > 0 && page <= recipeStore.meta.last_page && page !== recipeStore.meta.current_page) {
    recipeStore.fetchRecipes({ page: page, search: searchQuery.value, categorySlug: activeCategory.value });
    window.scrollTo(0, 0); 
  }
};

const performSearch = () => {
  activeCategory.value = null;
  recipeStore.fetchRecipes({ page: 1, search: searchQuery.value });
};

const filterByCategory = (slug) => {
    searchQuery.value = '';
    activeCategory.value = slug;
    recipeStore.fetchRecipes({ page: 1, categorySlug: slug });
};
</script>

<style scoped>
.pagination-button {
  @apply w-10 h-10 border border-gray-300 text-gray-900 rounded-md font-semibold hover:bg-gray-100 disabled:opacity-50 disabled:cursor-not-allowed transition-colors;
}
.category-button {
    @apply px-4 py-2 text-sm font-semibold text-gray-700 bg-white border border-gray-300 rounded-full hover:bg-gray-100 transition-colors;
}
</style>