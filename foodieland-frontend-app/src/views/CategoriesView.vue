<template>
  <Layout>
    <!-- Header Section -->
    <section class="py-12 md:py-20 bg-white border-b border-gray-200">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h1 class="text-4xl md:text-5xl font-bold text-gray-900 mb-4">All Categories</h1>
        <p class="text-gray-600">Explore recipes based on your favorite categories.</p>
      </div>
    </section>

    <!-- Categories Grid -->
    <section class="py-12 md:py-20">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Skeleton Loader -->
        <div v-if="categoryStore.loading" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-6 gap-6">
          <div v-for="n in 12" :key="n" class="text-center animate-pulse">
            <div class="w-24 h-24 bg-gray-300 rounded-xl mx-auto mb-3"></div>
            <div class="h-5 bg-gray-300 rounded-md w-3/4 mx-auto"></div>
          </div>
        </div>

        <!-- Error State -->
        <div v-else-if="categoryStore.error" class="text-center py-10 text-red-500">
          <p>{{ categoryStore.error }}</p>
        </div>

        <!-- No Results State -->
        <div v-else-if="categoryStore.categories.length === 0" class="text-center py-10 text-gray-500">
          <p>No categories found.</p>
        </div>
        
        <!-- Real Data -->
        <div v-else class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-6 gap-6">
          <router-link v-for="category in categoryStore.categories" :key="category.id" :to="`/recipes?category=${category.slug}`" class="text-center group">
            <div class="w-24 h-24 bg-gray-100 rounded-xl mx-auto mb-3 flex items-center justify-center shadow-sm hover:shadow-lg transition-all duration-300 transform hover:-translate-y-2">
              <span class="text-4xl transform transition-transform group-hover:scale-110">🍔</span>
            </div>
            <p class="font-semibold text-gray-900 group-hover:text-teal-600 transition">{{ category.name }}</p>
          </router-link>
        </div>
      </div>
    </section>
  </Layout>
</template>

<script setup>
import { onMounted } from 'vue';
import Layout from '@/components/Layout.vue';
import { useCategoryStore } from '@/store/categoryStore';

const categoryStore = useCategoryStore();

onMounted(() => {
  if (categoryStore.categories.length === 0) {
    categoryStore.fetchCategories();
  }
});
</script>