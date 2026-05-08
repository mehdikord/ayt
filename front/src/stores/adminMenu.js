import { defineStore } from 'pinia'
import { computed, ref } from 'vue'

let _nextId = 200

function allocId() {
  return _nextId++
}

export const useAdminMenuStore = defineStore('adminMenu', () => {
  const categories = ref([
    { id: 1, name: 'نوشیدنی گرم', slug: 'hot-drinks', sort_order: 1, is_active: true },
    { id: 2, name: 'قهوه سرد', slug: 'cold-coffee', sort_order: 2, is_active: true },
    { id: 3, name: 'دسر', slug: 'dessert', sort_order: 3, is_active: false }
  ])

  const items = ref([
    { id: 11, category_id: 1, name: 'اسپرسو', slug: 'espresso', is_active: true },
    { id: 12, category_id: 1, name: 'آمریکانو', slug: 'americano', is_active: true },
    { id: 13, category_id: 2, name: 'فراپه', slug: 'frappe', is_active: true }
  ])

  const variants = ref([
    { id: 21, menu_item_id: 11, name: '100% ربوستا', price: 120000, discount_price: 0, is_active: true },
    { id: 22, menu_item_id: 11, name: '50/50', price: 150000, discount_price: 130000, is_active: true },
    { id: 23, menu_item_id: 11, name: '100% عربیکا', price: 200000, discount_price: 0, is_active: true }
  ])

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

  function addCategory(payload) {
    const id = allocId()
    categories.value.unshift({ id, ...payload })
    return id
  }

  function updateCategory(id, payload) {
    const row = categories.value.find((c) => c.id === id)
    if (row) Object.assign(row, payload)
  }

  function removeCategory(id) {
    const itemIds = items.value.filter((i) => i.category_id === id).map((i) => i.id)
    variants.value = variants.value.filter((v) => !itemIds.includes(v.menu_item_id))
    items.value = items.value.filter((i) => i.category_id !== id)
    categories.value = categories.value.filter((c) => c.id !== id)
  }

  function toggleCategoryActive(id) {
    const row = categories.value.find((c) => c.id === id)
    if (row) row.is_active = !row.is_active
  }

  function addItem(payload) {
    const id = allocId()
    items.value.unshift({ id, ...payload })
    return id
  }

  function updateItem(id, payload) {
    const row = items.value.find((i) => i.id === id)
    if (row) Object.assign(row, payload)
  }

  function removeItem(id) {
    variants.value = variants.value.filter((v) => v.menu_item_id !== id)
    items.value = items.value.filter((i) => i.id !== id)
  }

  function toggleItemActive(id) {
    const row = items.value.find((i) => i.id === id)
    if (row) row.is_active = !row.is_active
  }

  function addVariant(payload) {
    const id = allocId()
    variants.value.unshift({ id, ...payload })
    return id
  }

  function updateVariant(id, payload) {
    const row = variants.value.find((v) => v.id === id)
    if (row) Object.assign(row, payload)
  }

  function removeVariant(id) {
    variants.value = variants.value.filter((v) => v.id !== id)
  }

  function toggleVariantActive(id) {
    const row = variants.value.find((v) => v.id === id)
    if (row) row.is_active = !row.is_active
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
    addVariant,
    updateVariant,
    removeVariant,
    toggleVariantActive,
    allocId
  }
})
