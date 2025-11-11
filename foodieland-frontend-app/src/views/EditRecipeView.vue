<template>
  <Layout>
    <div class="max-w-4xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
      <div v-if="loadingInitial" class="text-center py-20">Loading recipe data...</div>
      <div v-else-if="!recipeStore.recipe" class="text-center py-20 text-red-500">Could not load recipe data.</div>
      <div v-else class="bg-white rounded-xl shadow-lg p-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-8">Edit Your Recipe</h1>

        <form @submit.prevent="handleUpdateRecipe" class="space-y-6">
          <div>
            <label for="title" class="form-label">Recipe Title</label>
            <input v-model="form.title" type="text" id="title" class="form-input" required />
          </div>

          <div>
            <label for="description" class="form-label">Description</label>
            <textarea v-model="form.description" id="description" rows="4" class="form-input" required></textarea>
          </div>

          <div>
            <label class="form-label">Recipe Image</label>
            <div class="mt-2 flex items-center gap-4">
              <img :src="previewImage || recipeStore.recipe.image_url || defaultPlaceholderImage" alt="Image preview"
                class="w-32 h-32 object-cover rounded-lg bg-gray-100">
              <label for="image"
                class="cursor-pointer bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50">
                <span>Change Image</span>
                <input id="image" @change="onFileChange" type="file" class="sr-only">
              </label>
            </div>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div>
              <label for="prep_time" class="form-label">Prep Time (minutes)</label>
              <input v-model.number="form.prep_time" type="number" id="prep_time" class="form-input" required />
            </div>
            <div>
              <label for="cook_time" class="form-label">Cook Time (minutes)</label>
              <input v-model.number="form.cook_time" type="number" id="cook_time" class="form-input" required />
            </div>
            <div>
              <label for="difficulty" class="form-label">Difficulty</label>
              <select v-model="form.difficulty" id="difficulty" class="form-input" required>
                <option>Easy</option>
                <option>Medium</option>
                <option>Hard</option>
              </select>
            </div>
          </div>

          <div>
            <label class="form-label">Categories</label>
            <div class="mt-2 grid grid-cols-2 sm:grid-cols-4 gap-4">
              <div v-for="category in categoryStore.categories" :key="category.id" class="flex items-center">
                <input :id="`category-${category.id}`" :value="category.id" v-model="form.category_ids" type="checkbox"
                  class="h-4 w-4 text-teal-600 border-gray-300 rounded focus:ring-teal-500">
                <label :for="`category-${category.id}`" class="ml-3 text-sm text-gray-700">{{ category.name }}</label>
              </div>
            </div>
          </div>

          <div>
            <label class="form-label">Ingredients (one per line)</label>
            <textarea v-model="ingredientsText" rows="6" class="form-input"></textarea>
          </div>

          <div>
            <label class="form-label">Instructions (one per line)</label>
            <textarea v-model="instructionsText" rows="8" class="form-input"></textarea>
          </div>

          <div>
            <label for="tags" class="form-label">Tags (comma separated)</label>
            <input v-model="tagsText" type="text" id="tags" class="form-input" />
          </div>

          <div class="pt-4 flex gap-4">
            <button type="submit" :disabled="isLoading"
              class="flex-1 justify-center py-3 px-4 rounded-md shadow-sm text-sm font-medium text-white bg-teal-600 hover:bg-teal-700 disabled:bg-gray-400">
              {{ isLoading ? 'Saving...' : 'Save Changes' }}
            </button>
            <router-link :to="`/recipe/${route.params.slug}`"
              class="flex-1 text-center py-3 px-4 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200">
              Cancel
            </router-link>
          </div>
        </form>
      </div>
    </div>
  </Layout>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import Layout from '@/components/Layout.vue';
import { useRecipeStore } from '@/store/recipeStore';
import { useCategoryStore } from '@/store/categoryStore';
import { useToast } from 'vue-toastification';
import ApiService from '@/services/ApiService';

const route = useRoute();
const router = useRouter();
const recipeStore = useRecipeStore();
const categoryStore = useCategoryStore();
const toast = useToast();

const loadingInitial = ref(true);
const defaultPlaceholderImage = '/No_Image.jpg';
const previewImage = ref(null);
const isLoading = ref(false);

const form = ref({
  title: '',
  description: '',
  prep_time: 0,
  cook_time: 0,
  difficulty: 'Easy',
  category_ids: [],
  image: null,
});

const ingredientsText = ref('');
const instructionsText = ref('');
const tagsText = ref('');

// Populate form
const populateForm = (recipe) => {
  if (!recipe) return;
  form.value.title = recipe.title;
  form.value.description = recipe.description;
  form.value.prep_time = recipe.prep_time;
  form.value.cook_time = recipe.cook_time;
  form.value.difficulty = recipe.difficulty;
  form.value.category_ids = recipe.categories.map(cat => cat.id);

  // Ingredients
  if (recipe.ingredients && Array.isArray(recipe.ingredients)) {
    ingredientsText.value = recipe.ingredients.map(ing => ing.description ?? ing).join('\n');
  } else {
    ingredientsText.value = '';
  }

  // Instructions
  if (recipe.instructions && Array.isArray(recipe.instructions)) {
    instructionsText.value = recipe.instructions.map(inst => inst.description ?? inst).join('\n');
  } else {
    instructionsText.value = '';
  }

  // Tags
  tagsText.value = recipe.tags.map(tag => tag.name).join(', ');
};

// On mount
onMounted(async () => {
  await categoryStore.fetchCategories();
  await recipeStore.fetchRecipeBySlug(route.params.slug);
  populateForm(recipeStore.recipe);
  loadingInitial.value = false;
});

// File change
const onFileChange = (e) => {
  const file = e.target.files[0];
  if (file) {
    form.value.image = file;
    previewImage.value = URL.createObjectURL(file);
  }
};

// Update recipe
const handleUpdateRecipe = async () => {
  isLoading.value = true;
  const formData = new FormData();
  formData.append('_method', 'PUT');

  // Add form fields
  Object.entries(form.value).forEach(([key, value]) => {
    if (key === 'category_ids') {
      value.forEach(id => formData.append('category_ids[]', id));
    } else if (value) {
      formData.append(key, value);
    }
  });

  // Ingredients
  ingredientsText.value.split('\n').forEach(ing => {
    if (ing.trim()) formData.append('ingredients[]', ing.trim());
  });

  // Instructions
  instructionsText.value.split('\n').forEach(inst => {
    if (inst.trim()) formData.append('instructions[]', inst.trim());
  });

  // Tags
  tagsText.value.split(',').forEach(tag => {
    if (tag.trim()) formData.append('tags[]', tag.trim());
  });

  try {
    const response = await ApiService.post(`/recipes/${route.params.slug}`, formData, {
      headers: { 'Content-Type': 'multipart/form-data' },
    });
    toast.success('Recipe updated successfully!');
    router.push(`/recipe/${response.data.data.slug}`);
  } catch (error) {
    toast.error(error.response?.data?.message || 'Failed to update recipe.');
  } finally {
    isLoading.value = false;
  }
};
</script>

<style scoped>
.form-label {
  @apply block text-sm font-medium text-gray-800 mb-1;
}

.form-input {
  @apply block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-teal-500 focus:border-teal-500 sm:text-sm;
}
</style>
