import { defineStore } from 'pinia'
import { userMenuApi } from '@/services/user/endpoints/menuApi'

export const useUserMenuStore = defineStore('userMenu', {
  state: () => ({
    categories: [],
    items: [],
    selectedCategoryId: null,
    isLoadingCategories: false,
    isLoadingItems: false,
    error: null
  }),
  getters: {
    activeCategoryName(state) {
      const active = state.categories.find((category) => category.id === state.selectedCategoryId)
      return active?.name || ''
    }
  },
  actions: {
    async loadCategories() {
      this.isLoadingCategories = true
      this.error = null
      try {
        const categories = await userMenuApi.categories()
        this.categories = categories
        const firstCategoryId = categories[0]?.id ?? null
        if (!this.selectedCategoryId && firstCategoryId) {
          this.selectedCategoryId = firstCategoryId
        }
        if (this.selectedCategoryId) {
          await this.loadItems(this.selectedCategoryId)
        } else {
          this.items = []
        }
        return true
      } catch (error) {
        this.error = error
        this.categories = []
        this.items = []
        return false
      } finally {
        this.isLoadingCategories = false
      }
    },
    async loadItems(categoryId) {
      if (!categoryId) {
        this.items = []
        return
      }

      this.selectedCategoryId = categoryId
      this.isLoadingItems = true
      this.error = null
      try {
        this.items = await userMenuApi.categoryItems(categoryId)
      } catch (error) {
        this.error = error
        this.items = []
      } finally {
        this.isLoadingItems = false
      }
    }
  }
})
