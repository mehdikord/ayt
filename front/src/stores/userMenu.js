import { defineStore } from 'pinia'
import { userMenuApi } from '@/services/user/endpoints/menuApi'

export const useUserMenuStore = defineStore('userMenu', {
  state: () => ({
    categories: [],
    itemsByCategory: {},
    /** null = همه دسته‌بندی‌ها */
    selectedCategoryId: null,
    isLoadingCategories: false,
    isLoadingItems: false,
    error: null
  }),
  getters: {
    isContentLoading(state) {
      return state.isLoadingCategories || state.isLoadingItems
    },
    isAllCategoriesMode(state) {
      return state.selectedCategoryId === null
    },
    activeCategoryName(state) {
      const active = state.categories.find((category) => category.id === state.selectedCategoryId)
      return active?.name || ''
    },
    displaySections(state) {
      if (state.selectedCategoryId === null) {
        return state.categories.map((category) => ({
          category,
          items: state.itemsByCategory[category.id] || []
        }))
      }

      const category = state.categories.find((c) => c.id === state.selectedCategoryId)
      if (!category) {
        return []
      }

      return [
        {
          category,
          items: state.itemsByCategory[state.selectedCategoryId] || []
        }
      ]
    }
  },
  actions: {
    async loadCategories() {
      this.isLoadingCategories = true
      this.error = null
      try {
        const categories = await userMenuApi.categories()
        this.categories = categories
        if (this.selectedCategoryId === null) {
          await this.loadAllItems()
        } else if (this.selectedCategoryId) {
          await this.ensureCategoryItems(this.selectedCategoryId)
        }
        return true
      } catch (error) {
        this.error = error
        this.categories = []
        this.itemsByCategory = {}
        return false
      } finally {
        this.isLoadingCategories = false
      }
    },

    async selectAllCategories() {
      this.selectedCategoryId = null
      const hasAllItems = this.categories.every(
        (category) => Array.isArray(this.itemsByCategory[category.id])
      )
      if (!hasAllItems) {
        await this.loadAllItems()
      }
    },

    async selectCategory(categoryId) {
      if (!categoryId) {
        return this.selectAllCategories()
      }

      this.selectedCategoryId = categoryId
      await this.ensureCategoryItems(categoryId)
    },

    async ensureCategoryItems(categoryId) {
      if (Array.isArray(this.itemsByCategory[categoryId])) {
        return
      }

      this.isLoadingItems = true
      this.error = null
      try {
        const items = await userMenuApi.categoryItems(categoryId)
        this.itemsByCategory = {
          ...this.itemsByCategory,
          [categoryId]: items
        }
      } catch (error) {
        this.error = error
        this.itemsByCategory = {
          ...this.itemsByCategory,
          [categoryId]: []
        }
      } finally {
        this.isLoadingItems = false
      }
    },

    async loadAllItems() {
      if (!this.categories.length) {
        this.itemsByCategory = {}
        return
      }

      this.isLoadingItems = true
      this.error = null
      try {
        const results = await Promise.all(
          this.categories.map(async (category) => {
            const items = await userMenuApi.categoryItems(category.id)
            return [category.id, items]
          })
        )
        this.itemsByCategory = Object.fromEntries(results)
      } catch (error) {
        this.error = error
        this.itemsByCategory = {}
      } finally {
        this.isLoadingItems = false
      }
    }
  }
})
