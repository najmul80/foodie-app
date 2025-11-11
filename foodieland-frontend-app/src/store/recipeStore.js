// src/store/recipeStore.js
import { defineStore } from 'pinia'
import ApiService from '../services/ApiService'
import { useToast } from 'vue-toastification'

export const useRecipeStore = defineStore('recipe', {
  state: () => ({
    recipes: [],
    meta: {},
    recipe: null,
    tastyRecipes: [],
    loading: false,
    error: null,
  }),

  actions: {
    async fetchRecipes({ page = 1, search = '', categorySlug = null, limit = 10 } = {}) {
      this.loading = true;
      this.error = null;
      try {
        let url;
        if (categorySlug) {
          url = `/categories/${categorySlug}/recipes?page=${page}`;
        } else {
          url = `/recipes?page=${page}&search=${search}&limit=${limit}`;
        }
        const response = await ApiService.get(url);
        this.recipes = response.data.data;
        this.meta = response.data.meta;
      } catch (err) {
        this.error = 'Failed to fetch recipes.';
        console.error(err);
      } finally {
        this.loading = false;
      }
    },

    async fetchRecipeBySlug(slug) {
      this.loading = true
      this.error = null
      this.recipe = null
      try {
        const response = await ApiService.get(`/recipes/${slug}`)
        this.recipe = response.data.data
      } catch (err) {
        this.error = 'Failed to fetch the recipe.'
        console.error(err)
      } finally {
        this.loading = false
      }
    },

    async fetchTastyRecipes() {
      if (this.tastyRecipes.length > 0) return;
      try {
        const response = await ApiService.get('/recipes?limit=4')
        this.tastyRecipes = response.data.data
      } catch (error) {
        console.error('Failed to fetch tasty recipes', error)
      }
    },

    // --- ADD THESE NEW ACTIONS ---

    async addComment(slug, commentData) {
      const toast = useToast();
      try {
        const response = await ApiService.post(`/recipes/${slug}/comments`, commentData);
        if (this.recipe && this.recipe.slug === slug) {
          this.recipe.comments.unshift(response.data.data);
        }
        toast.success(response.data.message || 'Comment posted successfully!');
      } catch (error) {
        toast.error(error.response?.data?.message || 'Failed to post comment.');
        console.error("Failed to add comment:", error);
        throw error;
      }
    },

    async deleteComment(commentId) {
      const toast = useToast();
      try {
        const response = await ApiService.delete(`/comments/${commentId}`);
        if (this.recipe) {
          const index = this.recipe.comments.findIndex(c => c.id === commentId);
          if (index !== -1) {
            this.recipe.comments.splice(index, 1);
          }
        }
        toast.success(response.data.message || 'Comment deleted.');
      } catch (error) {
        toast.error(error.response?.data?.message || 'Failed to delete comment.');
        console.error("Failed to delete comment:", error);
        throw error;
      }
    },
  },
})