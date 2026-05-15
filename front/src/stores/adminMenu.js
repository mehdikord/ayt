import { defineStore } from 'pinia'
import { computed, ref } from 'vue'
import { adminMenuApi } from '@/services/admin/endpoints/menuApi'

function translateMenuError(error, fallback) {
  if (error?.status === 409) {
    return 'این مورد وابستگی دارد و ابتدا باید وابستگی‌ها حذف شوند.'
  }
  if (error?.status === 422) {
    return error?.message || 'داده های فرم معتبر نیست.'
  }
  return error?.message || fallback
}

export const useAdminMenuStore = defineStore('adminMenu', () => {
  const categories = ref([])
  const items = ref([])
  const variants = ref([])
  const state = ref({
    isLoading: false,
    error: null,
    lastUpdatedAt: null
  })

  const categoryOptions = computed(() =>
    categories.value.map((c) => ({ label: c.name, value: c.id }))
  )

  function itemsCountForCategory(categoryId) {
    return items.value.filter((i) => i.category_id === categoryId).length
  }

  function variantsCountForCategory(categoryId) {
    const itemIds = new Set(
      items.value.filter((i) => i.category_id === categoryId).map((i) => i.id)
    )
    return variants.value.filter((v) => itemIds.has(v.menu_item_id)).length
  }

  const categoriesWithStats = computed(() =>
    categories.value.map((c) => ({
      ...c,
      items_count: itemsCountForCategory(c.id),
      variants_count: variantsCountForCategory(c.id)
    }))
  )

  async function loadCategories({ includeInactive = true } = {}) {
    const data = await adminMenuApi.listCategories({ includeInactive })
    categories.value = Array.isArray(data) ? data : []
  }

  async function loadItems({ categoryId = null, includeInactive = true } = {}) {
    const data = await adminMenuApi.listItems({ categoryId, includeInactive })
    items.value = Array.isArray(data) ? data : []
  }

  async function loadVariantsForItems(itemList = []) {
    const requests = itemList.map((item) => adminMenuApi.listVariants(item.id))
    const responses = await Promise.all(requests)
    variants.value = responses.flatMap((entry) => (Array.isArray(entry) ? entry : []))
  }

  async function bootstrap({ includeInactive = true } = {}) {
    state.value.isLoading = true
    state.value.error = null
    try {
      await loadCategories({ includeInactive })
      await loadItems({ includeInactive })
      await loadVariantsForItems(items.value)
      state.value.lastUpdatedAt = new Date().toISOString()
      return true
    } catch (error) {
      state.value.error = translateMenuError(error, 'خطا در بارگذاری اطلاعات منو.')
      return false
    } finally {
      state.value.isLoading = false
    }
  }

  async function addCategory(payload) {
    try {
      await adminMenuApi.createCategory(payload)
      await bootstrap({ includeInactive: true })
      return true
    } catch (error) {
      throw new Error(translateMenuError(error, 'ایجاد دسته بندی انجام نشد.'))
    }
  }

  async function updateCategory(id, payload) {
    try {
      await adminMenuApi.updateCategory(id, payload)
      await bootstrap({ includeInactive: true })
      return true
    } catch (error) {
      throw new Error(translateMenuError(error, 'ویرایش دسته بندی انجام نشد.'))
    }
  }

  async function removeCategory(id) {
    try {
      await adminMenuApi.deleteCategory(id)
      await bootstrap({ includeInactive: true })
      return true
    } catch (error) {
      throw new Error(translateMenuError(error, 'حذف دسته بندی انجام نشد.'))
    }
  }

  async function toggleCategoryActive(id) {
    const row = categories.value.find((c) => c.id === id)
    if (!row) return false
    return updateCategory(id, { is_active: !row.is_active })
  }

  async function addItem(payload) {
    try {
      await adminMenuApi.createItem(payload)
      await bootstrap({ includeInactive: true })
      return true
    } catch (error) {
      throw new Error(translateMenuError(error, 'ایجاد آیتم انجام نشد.'))
    }
  }

  async function updateItem(id, payload) {
    try {
      await adminMenuApi.updateItem(id, payload)
      await bootstrap({ includeInactive: true })
      return true
    } catch (error) {
      throw new Error(translateMenuError(error, 'ویرایش آیتم انجام نشد.'))
    }
  }

  async function removeItem(id) {
    try {
      await adminMenuApi.deleteItem(id)
      await bootstrap({ includeInactive: true })
      return true
    } catch (error) {
      throw new Error(translateMenuError(error, 'حذف آیتم انجام نشد.'))
    }
  }

  async function toggleItemActive(id) {
    const row = items.value.find((i) => i.id === id)
    if (!row) return false
    return updateItem(id, { is_active: !row.is_active })
  }

  async function uploadItemImage(id, file) {
    try {
      await adminMenuApi.uploadItemImage(id, file)
      await bootstrap({ includeInactive: true })
      return true
    } catch (error) {
      throw new Error(translateMenuError(error, 'آپلود تصویر آیتم انجام نشد.'))
    }
  }

  async function deleteItemImage(id) {
    try {
      await adminMenuApi.deleteItemImage(id)
      await bootstrap({ includeInactive: true })
      return true
    } catch (error) {
      throw new Error(translateMenuError(error, 'حذف تصویر آیتم انجام نشد.'))
    }
  }

  async function addVariant(payload) {
    try {
      await adminMenuApi.createVariant(payload.menu_item_id, {
        name: payload.name,
        price: payload.price,
        discount_price: payload.discount_price || null,
        is_active: payload.is_active
      })
      await bootstrap({ includeInactive: true })
      return true
    } catch (error) {
      throw new Error(translateMenuError(error, 'ایجاد زیرمجموعه انجام نشد.'))
    }
  }

  async function updateVariant(id, payload) {
    try {
      await adminMenuApi.updateVariant(id, {
        name: payload.name,
        price: payload.price,
        discount_price: payload.discount_price || null,
        is_active: payload.is_active
      })
      await bootstrap({ includeInactive: true })
      return true
    } catch (error) {
      throw new Error(translateMenuError(error, 'ویرایش زیرمجموعه انجام نشد.'))
    }
  }

  async function removeVariant(id) {
    try {
      await adminMenuApi.deleteVariant(id)
      await bootstrap({ includeInactive: true })
      return true
    } catch (error) {
      throw new Error(translateMenuError(error, 'حذف زیرمجموعه انجام نشد.'))
    }
  }

  async function toggleVariantActive(id) {
    const row = variants.value.find((v) => v.id === id)
    if (!row) return false
    return updateVariant(id, { ...row, is_active: !row.is_active })
  }

  async function reorderCategories(ids) {
    try {
      await adminMenuApi.reorderCategories(ids)
      await bootstrap({ includeInactive: true })
      return true
    } catch (error) {
      throw new Error(translateMenuError(error, 'به‌روزرسانی ترتیب دسته‌ها انجام نشد.'))
    }
  }

  async function reorderItems(ids) {
    try {
      await adminMenuApi.reorderItems(ids)
      await bootstrap({ includeInactive: true })
      return true
    } catch (error) {
      throw new Error(translateMenuError(error, 'به‌روزرسانی ترتیب آیتم‌ها انجام نشد.'))
    }
  }

  async function reorderVariants(menuItemId, ids) {
    try {
      await adminMenuApi.reorderVariants(menuItemId, ids)
      await bootstrap({ includeInactive: true })
      return true
    } catch (error) {
      throw new Error(translateMenuError(error, 'به‌روزرسانی ترتیب زیرمجموعه‌ها انجام نشد.'))
    }
  }

  const itemOptions = computed(() =>
    items.value.map((i) => ({
      label: i.name,
      value: i.id
    }))
  )

  return {
    categories,
    items,
    variants,
    categoryOptions,
    itemOptions,
    state,
    bootstrap,
    categoriesWithStats,
    itemsCountForCategory,
    variantsCountForCategory,
    addCategory,
    updateCategory,
    removeCategory,
    toggleCategoryActive,
    addItem,
    updateItem,
    removeItem,
    toggleItemActive,
    uploadItemImage,
    deleteItemImage,
    addVariant,
    updateVariant,
    removeVariant,
    toggleVariantActive,
    reorderCategories,
    reorderItems,
    reorderVariants
  }
})
