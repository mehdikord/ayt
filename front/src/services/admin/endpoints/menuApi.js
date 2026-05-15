import { adminHttpClient } from '@/services/admin/http'

const withIncludeInactive = (includeInactive) => ({
  include_inactive: includeInactive ? 1 : 0
})

export const adminMenuApi = {
  listCategories({ includeInactive = true } = {}) {
    return adminHttpClient.get('/menu/categories', {
      params: withIncludeInactive(includeInactive)
    })
  },
  createCategory(payload) {
    return adminHttpClient.post('/menu/categories', payload)
  },
  updateCategory(id, payload) {
    return adminHttpClient.patch(`/menu/categories/${id}`, payload)
  },
  deleteCategory(id) {
    return adminHttpClient.delete(`/menu/categories/${id}`)
  },
  reorderCategories(ids) {
    return adminHttpClient.post('/menu/categories/reorder', { ids })
  },

  listItems({ categoryId = null, includeInactive = true } = {}) {
    return adminHttpClient.get('/menu/items', {
      params: {
        ...withIncludeInactive(includeInactive),
        ...(categoryId ? { category_id: categoryId } : {})
      }
    })
  },
  createItem(payload) {
    return adminHttpClient.post('/menu/items', payload)
  },
  updateItem(id, payload) {
    return adminHttpClient.patch(`/menu/items/${id}`, payload)
  },
  uploadItemImage(id, file) {
    const formData = new FormData()
    formData.append('image', file)
    return adminHttpClient.post(`/menu/items/${id}/image`, formData)
  },
  deleteItemImage(id) {
    return adminHttpClient.delete(`/menu/items/${id}/image`)
  },
  deleteItem(id) {
    return adminHttpClient.delete(`/menu/items/${id}`)
  },
  reorderItems(ids) {
    return adminHttpClient.post('/menu/items/reorder', { ids })
  },

  listVariants(menuItemId) {
    return adminHttpClient.get(`/menu/items/${menuItemId}/variants`)
  },
  createVariant(menuItemId, payload) {
    return adminHttpClient.post(`/menu/items/${menuItemId}/variants`, payload)
  },
  updateVariant(id, payload) {
    return adminHttpClient.patch(`/menu/variants/${id}`, payload)
  },
  deleteVariant(id) {
    return adminHttpClient.delete(`/menu/variants/${id}`)
  },
  reorderVariants(menuItemId, ids) {
    return adminHttpClient.post(`/menu/items/${menuItemId}/variants/reorder`, { ids })
  }
}
