// src/store/categoryStore.js
import { defineStore } from 'pinia'
import ApiService from '../services/ApiService'

export const useCategoryStore = defineStore('category', {
  state: () => ({
    categories: [],
    loading: false,
    error: null,
  }),
  actions: {
    async fetchCategories() {
      
      if (this.categories.length > 0) return;

      this.loading = true
      this.error = null
      try {
        const response = await ApiService.get('/categories')
        this.categories = response.data.data
      } catch (err) {
        this.error = 'Failed to fetch categories.'
        console.error(err)
      } finally {
        this.loading = false
      }
    },
  },
})