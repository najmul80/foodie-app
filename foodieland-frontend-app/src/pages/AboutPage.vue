<template>
  <Layout>
    <!-- Hero Section -->
    <section class="relative bg-gradient-to-br from-teal-50 to-blue-50 py-16 md:py-24">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center">
          <h1 class="text-4xl md:text-5xl font-bold text-gray-900 mb-6">About Foodielland</h1>
          <p class="text-xl text-gray-600 max-w-3xl mx-auto">
            Your trusted companion in the kitchen, bringing delicious recipes and culinary inspiration to food lovers worldwide.
          </p>
        </div>
      </div>
    </section>

    <!-- Our Story Section -->
    <section class="py-16 md:py-20">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
          <div>
            <h2 class="text-3xl font-bold text-gray-900 mb-6">Our Story</h2>
            <p class="text-gray-700 mb-4 leading-relaxed">
              Foodielland was born from a simple passion: sharing the joy of cooking with everyone. What started as a small blog in 2015 has grown into a vibrant community of food enthusiasts, home cooks, and professional chefs.
            </p>
            <p class="text-gray-700 mb-4 leading-relaxed">
              We believe that cooking should be accessible, enjoyable, and inspiring. Whether you're a beginner looking to master the basics or an experienced cook seeking new challenges, our platform offers something for everyone.
            </p>
            <p class="text-gray-700 mb-6 leading-relaxed">
              Today, Foodielland features thousands of tested recipes, cooking tips, and techniques from around the world, all curated to help you create memorable meals in your own kitchen.
            </p>
            <div v-if="settings" class="flex flex-wrap gap-8">
              <div class="text-center">
                <p class="text-3xl font-bold text-teal-600">{{ settings.recipe_count }}</p>
                <p class="text-sm text-gray-600">Recipes</p>
              </div>
              <div class="text-center">
                <p class="text-3xl font-bold text-teal-600">{{ settings.community_count }}</p>
                <p class="text-sm text-gray-600">Community Members</p>
              </div>
              <div class="text-center">
                <p class="text-3xl font-bold text-teal-600">{{ settings.contributor_count }}</p>
                <p class="text-sm text-gray-600">Expert Contributors</p>
              </div>
            </div>
          </div>
          <div class="relative rounded-2xl overflow-hidden shadow-xl">
            <img src="https://picsum.photos/seed/our-story/600/400" alt="Our Story" class="w-full h-96 object-cover" />
          </div>
        </div>
      </div>
    </section>

    <!-- Mission & Values -->
    <section class="py-16 md:py-20 bg-gray-50">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
          <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">Our Mission & Values</h2>
          <p class="text-gray-600 max-w-2xl mx-auto">What drives us every day to bring you the best cooking experience</p>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
          <!-- Mission cards are static as their content is less likely to change -->
          <div class="bg-white rounded-xl p-6 shadow-sm hover:shadow-md transition-shadow">
            <h3 class="text-xl font-bold text-gray-900 mb-3">Quality Content</h3>
            <p class="text-gray-600">Every recipe is tested and perfected by our team of experts to ensure delicious results every time.</p>
          </div>
          <div class="bg-white rounded-xl p-6 shadow-sm hover:shadow-md transition-shadow">
            <h3 class="text-xl font-bold text-gray-900 mb-3">Community First</h3>
            <p class="text-gray-600">We're building a supportive community where food lovers can share, learn, and grow together.</p>
          </div>
          <div class="bg-white rounded-xl p-6 shadow-sm hover:shadow-md transition-shadow">
            <h3 class="text-xl font-bold text-gray-900 mb-3">Innovation</h3>
            <p class="text-gray-600">Constantly evolving and bringing new ideas to make your cooking experience better and more enjoyable.</p>
          </div>
        </div>
      </div>
    </section>

    <!-- Team Section -->
    <section v-if="settings && settings.team_members" class="py-16 md:py-20">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
          <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">Meet Our Team</h2>
          <p class="text-gray-600">The passionate people behind Foodielland</p>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-8">
          <div v-for="(member, i) in settings.team_members" :key="i" class="text-center group">
            <div class="relative mb-4 mx-auto w-32 h-32">
              <img :src="member.image" :alt="member.name" class="w-full h-full rounded-full object-cover shadow-lg group-hover:scale-105 transition-transform duration-300" />
            </div>
            <h3 class="font-bold text-gray-900 mb-1">{{ member.name }}</h3>
            <p class="text-sm text-gray-600 mb-3">{{ member.role }}</p>
          </div>
        </div>
      </div>
    </section>

    <!-- Newsletter Section -->
    <section class="py-16 md:py-20 bg-gradient-to-r from-teal-600 to-blue-600">
      <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h2 class="text-3xl font-bold text-white mb-4">Join Our Food Community</h2>
        <p class="text-teal-100 mb-8">Get exclusive recipes, cooking tips, and be the first to know about new features</p>
        <form class="flex flex-col sm:flex-row gap-4 max-w-md mx-auto">
          <input type="email" placeholder="Your email address..." class="flex-1 px-6 py-3 rounded-lg focus:outline-none focus:ring-4 focus:ring-white focus:ring-opacity-30" />
          <button type="submit" class="px-8 py-3 bg-white text-teal-600 rounded-lg hover:bg-gray-100 transition font-semibold shadow-lg">
            Subscribe
          </button>
        </form>
      </div>
    </section>
  </Layout>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import Layout from '@/components/Layout.vue'
import ApiService from '@/services/ApiService';

const settings = ref(null);
const loading = ref(true);

onMounted(async () => {
  try {
    const response = await ApiService.get('/settings/about_page');
    settings.value = response.data.data;
  } catch (error) {
    console.error('Failed to load about page settings:', error);
  } finally {
    loading.value = false;
  }
});
</script>