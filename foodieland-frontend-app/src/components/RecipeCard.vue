<template>
  <router-link :to="`/recipe/${slug}`" class="block bg-white rounded-xl overflow-hidden shadow-md hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1 group">
    <div class="relative overflow-hidden">
      <img :src="image" :alt="title" class="w-full h-48 object-cover transition-transform duration-500 group-hover:scale-110" />
      
      <div class="absolute inset-0 bg-gradient-to-t from-black via-transparent to-transparent opacity-0 group-hover:opacity-50 transition-opacity duration-300"></div>
      
      <button @click.prevent="handleToggleFavorite" class="absolute top-4 right-4 w-10 h-10 bg-white rounded-full flex items-center justify-center opacity-0 group-hover:opacity-100 transition-all duration-300 transform scale-50 group-hover:scale-100 shadow-lg hover:bg-red-50">
        <svg class="w-5 h-5 transition-colors" :class="isFavorited ? 'text-red-500 fill-current' : 'text-gray-600 hover:text-red-500'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
        </svg>
      </button>
    </div>

    <div class="p-5">
      <h3 class="font-bold text-gray-900 mb-3 line-clamp-2 group-hover:text-teal-600 transition-colors duration-300 text-lg h-14">
        {{ title }}
      </h3>
      
      <div class="flex items-center justify-between">
        <div class="flex items-center gap-3 text-sm text-gray-600">
          <div class="flex items-center gap-1">
            <svg class="w-5 h-5 text-gray-400" fill="currentColor" viewBox="0 0 24 24">
              <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm.5-13H11v6l5.2 3.2.8-1.3-4.5-2.7z" />
            </svg>
            <span class="font-medium">{{ time }} min</span>
          </div>
          <router-link v-if="authorId && author" :to="`/author/${authorId}`" @click.stop class="text-sm text-gray-600 hover:text-teal-600 hover:underline">
             By {{ author }}
          </router-link>
        </div>
      </div>
    </div>
  </router-link>
</template>

<script setup>
import { computed } from 'vue';
import { useAuthStore } from '@/store/authStore';

const props = defineProps({
  id: { type: [String, Number], required: true },
  title: { type: String, required: true },
  image: { type: String, required: true },
  time: { type: String, required: true },
  slug: { type: String, required: true },
  author: { type: String },
  authorId: { type: [String, Number] },
});

const authStore = useAuthStore();

const isFavorited = computed(() => {
  return authStore.isFavorited(props.id);
});

const handleToggleFavorite = () => {
  // All logic, including login checks and toast notifications,
  // is now handled by the authStore.
  authStore.toggleFavorite(props.slug, props.id);
};
</script>