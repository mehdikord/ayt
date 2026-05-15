<script setup>
import { computed, onMounted, reactive, ref, watch } from 'vue'
import { storeToRefs } from 'pinia'
import Select from 'primevue/select'
import Tabs from 'primevue/tabs'
import TabList from 'primevue/tablist'
import Tab from 'primevue/tab'
import TabPanels from 'primevue/tabpanels'
import TabPanel from 'primevue/tabpanel'
import Avatar from 'primevue/avatar'
import { useAdminMenuStore } from '@/stores/adminMenu'
import { useAdminFeedback } from '@/composables/useAdminFeedback'

const menuStore = useAdminMenuStore()
const { items, variants, categoryOptions, itemOptions } = storeToRefs(menuStore)
const feedback = useAdminFeedback()

const includeInactive = ref(true)
const errorMessage = ref('')

const categoryFilter = ref(null)
const statusFilter = ref(null)
const statusOptions = [
  { label: 'فعال', value: true },
  { label: 'غیرفعال', value: false }
]

const toNumberOrNull = (value) => {
  if (value === null || value === undefined || value === '') return null
  const n = Number(value)
  return Number.isNaN(n) ? null : n
}

const toBooleanOrNull = (value) => {
  if (value === null || value === undefined || value === '') return null
  if (typeof value === 'boolean') return value
  if (value === 'true' || value === 1 || value === '1') return true
  if (value === 'false' || value === 0 || value === '0') return false
  return null
}

const normalizedCategoryOptions = computed(() =>
  (categoryOptions.value ?? []).map((option) => ({
    label: option.label,
    value: toNumberOrNull(option.value)
  }))
)

const categoryLabel = (categoryId) =>
  menuStore.categories.find((c) => Number(c.id) === Number(categoryId))?.name ?? '—'

const filteredItems = computed(() =>
  items.value.filter((item) => {
    const currentCategory = toNumberOrNull(categoryFilter.value)
    const currentStatus = toBooleanOrNull(statusFilter.value)
    const categoryMatch = currentCategory == null || Number(item.category_id) === currentCategory
    const statusMatch = currentStatus == null || Boolean(item.is_active) === currentStatus
    return categoryMatch && statusMatch
  })
)

const itemIdsInSelectedCategory = computed(() => {
  const currentCategory = toNumberOrNull(categoryFilter.value)
  if (currentCategory == null) return []
  return items.value
    .filter((i) => Number(i.category_id) === currentCategory)
    .sort((a, b) => (a.sort_order ?? 0) - (b.sort_order ?? 0))
    .map((i) => i.id)
})

const itemForm = reactive({
  name: '',
  slug: '',
  category_id: null,
  is_active: true
})

const itemEdit = reactive({
  name: '',
  slug: '',
  category_id: null,
  is_active: true
})

const editingItemId = ref(null)
const showCreateItemModal = ref(false)
const showEditItemModal = ref(false)

const variantForm = reactive({
  menu_item_id: null,
  name: '',
  price: 0,
  discount_price: 0,
  is_active: true
})

const variantEdit = reactive({
  menu_item_id: null,
  name: '',
  price: 0,
  discount_price: 0,
  is_active: true
})

const editingVariantId = ref(null)
const showCreateVariantModal = ref(false)
const showEditVariantModal = ref(false)

const variantError = computed(() => {
  if (!variantForm.discount_price) return ''
  return variantForm.discount_price > variantForm.price
    ? 'قیمت تخفیف خورده نمی تواند بیشتر از قیمت پایه باشد.'
    : ''
})

const variantEditError = computed(() => {
  if (!variantEdit.discount_price) return ''
  return variantEdit.discount_price > variantEdit.price
    ? 'قیمت تخفیف خورده نمی تواند بیشتر از قیمت پایه باشد.'
    : ''
})

const hasMenuData = computed(() => items.value.length > 0 || variants.value.length > 0)
const isLoading = computed(() => menuStore.state.isLoading)
const hasError = computed(() => Boolean(menuStore.state.error))
const showImageModal = ref(false)
const imageUploading = ref(false)
const imageItemId = ref(null)
const imageFileInput = ref(null)

const currentImageItem = computed(() =>
  items.value.find((item) => Number(item.id) === Number(imageItemId.value)) ?? null
)
const currentImageUrl = computed(() => currentImageItem.value?.image_url || '')

onMounted(async () => {
  await menuStore.bootstrap({ includeInactive: includeInactive.value })
})

const openCreateItemModal = () => {
  Object.assign(itemForm, {
    name: '',
    slug: '',
    category_id: normalizedCategoryOptions.value[0]?.value ?? null,
    is_active: true
  })
  showCreateItemModal.value = true
}

const saveItem = async () => {
  if (!itemForm.name?.trim() || !itemForm.slug?.trim() || itemForm.category_id == null) return
  try {
    errorMessage.value = ''
    await menuStore.addItem({ ...itemForm })
    showCreateItemModal.value = false
    feedback.success('آیتم جدید ثبت شد.')
  } catch (error) {
    errorMessage.value = error.message
    feedback.error(error.message)
  }
}

const openItemEdit = (row) => {
  editingItemId.value = row.id
  Object.assign(itemEdit, {
    name: row.name,
    slug: row.slug,
    category_id: row.category_id,
    is_active: row.is_active
  })
  showEditItemModal.value = true
}

const openImageModal = (row) => {
  imageItemId.value = row.id
  showImageModal.value = true
}

const openImagePicker = () => {
  if (imageUploading.value) return
  imageFileInput.value?.click()
}

const onImageFileChange = async (event) => {
  const file = event?.target?.files?.[0]
  if (!file || !imageItemId.value) return
  try {
    imageUploading.value = true
    errorMessage.value = ''
    await menuStore.uploadItemImage(imageItemId.value, file)
    feedback.success('تصویر آیتم ذخیره شد.')
  } catch (error) {
    errorMessage.value = error.message
    feedback.error(error.message)
  } finally {
    imageUploading.value = false
    if (event?.target) event.target.value = ''
  }
}

const removeCurrentImage = async () => {
  if (!imageItemId.value || !currentImageUrl.value || imageUploading.value) return
  try {
    imageUploading.value = true
    errorMessage.value = ''
    await menuStore.deleteItemImage(imageItemId.value)
    feedback.success('تصویر آیتم حذف شد.')
  } catch (error) {
    errorMessage.value = error.message
    feedback.error(error.message)
  } finally {
    imageUploading.value = false
  }
}

const saveItemEdit = async () => {
  if (!editingItemId.value) return
  try {
    errorMessage.value = ''
    await menuStore.updateItem(editingItemId.value, { ...itemEdit })
    showEditItemModal.value = false
    feedback.success('آیتم به‌روز شد.')
  } catch (error) {
    errorMessage.value = error.message
    feedback.error(error.message)
  }
}

const openCreateVariantModal = () => {
  Object.assign(variantForm, {
    menu_item_id: null,
    name: '',
    price: 0,
    discount_price: 0,
    is_active: true
  })
  showCreateVariantModal.value = true
}

const saveVariant = async () => {
  if (!variantForm.name?.trim() || variantForm.menu_item_id == null || variantError.value) return
  try {
    errorMessage.value = ''
    await menuStore.addVariant({ ...variantForm })
    showCreateVariantModal.value = false
    feedback.success('زیرمجموعه ثبت شد.')
  } catch (error) {
    errorMessage.value = error.message
    feedback.error(error.message)
  }
}

const openVariantEdit = (row) => {
  editingVariantId.value = row.id
  Object.assign(variantEdit, {
    menu_item_id: row.menu_item_id,
    name: row.name,
    price: row.price,
    discount_price: row.discount_price,
    is_active: row.is_active
  })
  showEditVariantModal.value = true
}

const saveVariantEdit = async () => {
  if (variantEditError.value || !editingVariantId.value) return
  try {
    errorMessage.value = ''
    await menuStore.updateVariant(editingVariantId.value, { ...variantEdit })
    showEditVariantModal.value = false
    feedback.success('زیرمجموعه به‌روز شد.')
  } catch (error) {
    errorMessage.value = error.message
    feedback.error(error.message)
  }
}

const itemLabel = (itemId) => items.value.find((i) => i.id === itemId)?.name ?? '—'
const categoryLabelForItemId = (itemId) => {
  const item = items.value.find((i) => Number(i.id) === Number(itemId))
  if (!item) return '—'
  return categoryLabel(item.category_id)
}

const variantReorderItemId = ref(null)
const variantReorderOptions = computed(() => {
  const ids = [...new Set(variants.value.map((v) => v.menu_item_id))]
  return ids.map((id) => ({ label: itemLabel(id), value: id }))
})
watch(
  variantReorderOptions,
  (opts) => {
    if (!opts.length) {
      variantReorderItemId.value = null
      return
    }
    if (
      variantReorderItemId.value == null ||
      !opts.some((o) => o.value === variantReorderItemId.value)
    ) {
      variantReorderItemId.value = opts[0].value
    }
  },
  { immediate: true }
)

const variantsSortedForReorder = computed(() =>
  variants.value
    .filter((v) => v.menu_item_id === variantReorderItemId.value)
    .sort((a, b) => (a.sort_order ?? 0) - (b.sort_order ?? 0))
)

const pricedVariants = computed(() =>
  variants.value.filter((v) => v.price !== null && v.price !== undefined)
)

const moveVariant = async (id, delta) => {
  const itemId = variantReorderItemId.value
  if (!itemId) return
  const ids = [...variantsSortedForReorder.value.map((v) => v.id)]
  const idx = ids.indexOf(id)
  const j = idx + delta
  if (idx < 0 || j < 0 || j >= ids.length) return
  ;[ids[idx], ids[j]] = [ids[j], ids[idx]]
  try {
    errorMessage.value = ''
    await menuStore.reorderVariants(itemId, ids)
    feedback.success('ترتیب زیرمجموعه‌ها ذخیره شد.')
  } catch (error) {
    errorMessage.value = error.message
    feedback.error(error.message)
  }
}

const toggleItem = async (id) => {
  try {
    errorMessage.value = ''
    await menuStore.toggleItemActive(id)
  } catch (error) {
    errorMessage.value = error.message
  }
}

const requestRemoveItem = (row) => {
  feedback.confirmDelete({
    message: `آیتم «${row.name}» حذف شود؟ اگر زیرمجموعه داشته باشد، سرور اجازه حذف نمی‌دهد.`,
    accept: async () => {
      try {
        errorMessage.value = ''
        await menuStore.removeItem(row.id)
        feedback.success('آیتم حذف شد.')
      } catch (error) {
        errorMessage.value = error.message
        feedback.error(error.message)
      }
    }
  })
}

const toggleVariant = async (id) => {
  try {
    errorMessage.value = ''
    await menuStore.toggleVariantActive(id)
  } catch (error) {
    errorMessage.value = error.message
  }
}

const requestRemoveVariant = (row) => {
  feedback.confirmDelete({
    message: `زیرمجموعه «${row.name}» حذف شود؟`,
    accept: async () => {
      try {
        errorMessage.value = ''
        await menuStore.removeVariant(row.id)
        feedback.success('زیرمجموعه حذف شد.')
      } catch (error) {
        errorMessage.value = error.message
        feedback.error(error.message)
      }
    }
  })
}

const moveItem = async (id, delta) => {
  if (categoryFilter.value == null) {
    feedback.info('برای ذخیرهٔ ترتیب، یک دسته از فیلتر انتخاب کنید.', '')
    return
  }
  const ids = [...itemIdsInSelectedCategory.value]
  const idx = ids.indexOf(id)
  const j = idx + delta
  if (idx < 0 || j < 0 || j >= ids.length) return
  ;[ids[idx], ids[j]] = [ids[j], ids[idx]]
  try {
    errorMessage.value = ''
    await menuStore.reorderItems(ids)
    feedback.success('ترتیب آیتم‌های این دسته ذخیره شد.')
  } catch (error) {
    errorMessage.value = error.message
    feedback.error(error.message)
  }
}

const reloadWithFilters = async () => {
  await menuStore.bootstrap({ includeInactive: includeInactive.value })
}
</script>

<template>
  <div class="flex justify-content-between align-items-center mb-3">
    <div class="flex align-items-center gap-2">
      <Checkbox v-model="includeInactive" binary inputId="include-inactive-menu" @change="reloadWithFilters" />
      <label for="include-inactive-menu" style="color: #000">نمایش غیرفعال ها</label>
    </div>
  </div>
  <div v-if="isLoading" class="p-4 text-center">
    <ProgressSpinner style="width: 42px; height: 42px" strokeWidth="6" />
    <p class="text-color-secondary">در حال بارگذاری آیتم های منو...</p>
  </div>
  <Message v-else-if="hasError" severity="error" :closable="false">
    {{ menuStore.state.error }}
  </Message>
  <div v-else class="grid">
    <div class="col-12">
      <Card>
        <template #title>
          <div class="flex align-items-center justify-content-between gap-2 flex-wrap">
            <span>مدیریت آیتم ها و واریانت های منو</span>
            <div class="flex gap-2 flex-wrap">
              <Button label="افزودن آیتم جدید" icon="pi pi-plus" @click="openCreateItemModal" />
              <Button label="افزودن زیرمجموعه جدید" icon="pi pi-plus" severity="secondary" outlined @click="openCreateVariantModal" />
            </div>
          </div>
        </template>
        <template #content>
          <Message v-if="errorMessage" severity="error" :closable="false" class="mb-3">
            {{ errorMessage }}
          </Message>
          <Message v-if="!hasMenuData" severity="secondary" :closable="false" class="mb-3">
            هنوز آیتم یا زیرمجموعه ای ثبت نشده است.
          </Message>
          <Tabs value="0">
            <TabList>
              <Tab value="0">مدیریت آیتم ها و واریانت های منو</Tab>
              <Tab value="1">فهرست همه زیرمجموعه‌ها</Tab>
            </TabList>
            <TabPanels>
              <TabPanel value="0">
                <div class="flex flex-wrap gap-2 mb-3">
                  <Select
                    v-model="categoryFilter"
                    :options="normalizedCategoryOptions"
                    optionLabel="label"
                    optionValue="value"
                    showClear
                    placeholder="فیلتر دسته بندی"
                    class="w-14rem"
                  />
                  <Select
                    v-model="statusFilter"
                    :options="statusOptions"
                    optionLabel="label"
                    optionValue="value"
                    showClear
                    placeholder="فیلتر وضعیت"
                    class="w-12rem"
                  />
                </div>
                <Message v-if="categoryFilter == null" severity="info" :closable="false" class="mb-2">
                  برای تغییر ترتیب آیتم‌ها در این جدول، یک دسته را از فیلتر بالا انتخاب کنید.
                </Message>
                <DataTable :value="filteredItems" stripedRows responsiveLayout="scroll">
                  <Column header="نام آیتم">
                    <template #body="{ data }">
                      <div class="flex align-items-center gap-2">
                        <Avatar
                          v-if="data.image_url"
                          :image="data.image_url"
                          shape="circle"
                          size="large"
                          class="cursor-pointer"
                          @click="openImageModal(data)"
                        />
                        <Avatar
                          v-else
                          icon="pi pi-image"
                          shape="circle"
                          size="large"
                          class="cursor-pointer"
                          @click="openImageModal(data)"
                        />
                        <span>{{ data.name }}</span>
                      </div>
                    </template>
                  </Column>
                  <Column field="slug" header="اسلاگ" />
                  <Column header="دسته بندی">
                    <template #body="{ data }">
                      {{ categoryLabel(data.category_id) }}
                    </template>
                  </Column>
                  <Column header="وضعیت">
                    <template #body="{ data }">
                      <Tag :value="data.is_active ? 'فعال' : 'غیرفعال'" :severity="data.is_active ? 'success' : 'danger'" />
                    </template>
                  </Column>
                  <Column v-if="categoryFilter != null" header="ترتیب">
                    <template #body="{ data }">
                      <div class="flex gap-1">
                        <Button title="بالا" icon="pi pi-angle-up" size="small" text @click="moveItem(data.id, -1)" />
                        <Button title="پایین" icon="pi pi-angle-down" size="small" text @click="moveItem(data.id, 1)" />
                      </div>
                    </template>
                  </Column>
                  <Column header="اکشن">
                    <template #body="{ data }">
                      <div class="flex gap-2 flex-wrap">
                        <Button icon="pi pi-pencil" size="small" text @click="openItemEdit(data)" />
                        <Button icon="pi pi-power-off" size="small" text @click="toggleItem(data.id)" />
                        <Button icon="pi pi-trash" size="small" text severity="danger" @click="requestRemoveItem(data)" />
                      </div>
                    </template>
                  </Column>
                </DataTable>
                <h4 class="mt-4 mb-2">زیرمجموعه‌های آیتم انتخاب‌شده برای مرتب‌سازی</h4>
                <div class="flex flex-wrap gap-2 mb-3 align-items-center">
                  <span class="font-semibold white-space-nowrap">مرتب‌سازی برای آیتم:</span>
                  <Select
                    v-model="variantReorderItemId"
                    :options="variantReorderOptions"
                    optionLabel="label"
                    optionValue="value"
                    placeholder="انتخاب آیتم"
                    class="w-20rem"
                    :disabled="!variantReorderOptions.length"
                  />
                </div>
                <DataTable
                  v-if="variantReorderOptions.length"
                  :value="variantsSortedForReorder"
                  stripedRows
                  responsiveLayout="scroll"
                  class="mb-4"
                >
                  <Column field="name" header="نام" />
                  <Column field="price" header="قیمت پایه" />
                  <Column header="ترتیب">
                    <template #body="{ data }">
                      <div class="flex gap-1">
                        <Button title="بالا" icon="pi pi-angle-up" size="small" text @click="moveVariant(data.id, -1)" />
                        <Button title="پایین" icon="pi pi-angle-down" size="small" text @click="moveVariant(data.id, 1)" />
                      </div>
                    </template>
                  </Column>
                  <Column header="اکشن">
                    <template #body="{ data }">
                      <div class="flex gap-2">
                        <Button icon="pi pi-pencil" size="small" text @click="openVariantEdit(data)" />
                        <Button icon="pi pi-power-off" size="small" text @click="toggleVariant(data.id)" />
                        <Button icon="pi pi-trash" size="small" text severity="danger" @click="requestRemoveVariant(data)" />
                      </div>
                    </template>
                  </Column>
                </DataTable>
              </TabPanel>
              <TabPanel value="1">
                <DataTable :value="pricedVariants" stripedRows responsiveLayout="scroll">
                  <Column field="name" header="نام زیرمجموعه" />
                  <Column header="آیتم اصلی">
                    <template #body="{ data }">
                      {{ itemLabel(data.menu_item_id) }}
                    </template>
                  </Column>
                  <Column header="دسته بندی">
                    <template #body="{ data }">
                      {{ categoryLabelForItemId(data.menu_item_id) }}
                    </template>
                  </Column>
                  <Column field="price" header="قیمت پایه" />
                  <Column field="discount_price" header="قیمت با تخفیف" />
                  <Column header="وضعیت">
                    <template #body="{ data }">
                      <Tag :value="data.is_active ? 'فعال' : 'غیرفعال'" :severity="data.is_active ? 'success' : 'danger'" />
                    </template>
                  </Column>
                  <Column header="اکشن">
                    <template #body="{ data }">
                      <div class="flex gap-2 flex-wrap">
                        <Button icon="pi pi-pencil" size="small" text @click="openVariantEdit(data)" />
                        <Button icon="pi pi-power-off" size="small" text @click="toggleVariant(data.id)" />
                        <Button icon="pi pi-trash" size="small" text severity="danger" @click="requestRemoveVariant(data)" />
                      </div>
                    </template>
                  </Column>
                </DataTable>
              </TabPanel>
            </TabPanels>
          </Tabs>
        </template>
      </Card>
    </div>
  </div>

  <Dialog v-model:visible="showCreateItemModal" modal header="افزودن آیتم جدید" :style="{ width: '32rem', maxWidth: '95vw' }">
    <div class="grid">
      <div class="col-12">
        <label class="block mb-2 font-semibold">دسته بندی</label>
        <Select
          v-model="itemForm.category_id"
          :options="normalizedCategoryOptions"
          optionLabel="label"
          optionValue="value"
          placeholder="انتخاب کنید"
          class="w-full"
          :disabled="!normalizedCategoryOptions.length"
        />
      </div>
      <div class="col-12">
        <label class="block mb-2 font-semibold">نام آیتم</label>
        <InputText v-model="itemForm.name" class="w-full" />
      </div>
      <div class="col-12">
        <label class="block mb-2 font-semibold">اسلاگ</label>
        <InputText v-model="itemForm.slug" class="w-full" />
      </div>
      <div class="col-12 flex align-items-center gap-2">
        <Checkbox v-model="itemForm.is_active" binary inputId="item-active-new" />
        <label for="item-active-new">فعال</label>
      </div>
    </div>
    <template #footer>
      <div class="flex gap-2 justify-content-end">
        <Button label="انصراف" severity="secondary" outlined @click="showCreateItemModal = false" />
        <Button label="ثبت آیتم" icon="pi pi-check" @click="saveItem" />
      </div>
    </template>
  </Dialog>

  <Dialog v-model:visible="showEditItemModal" modal header="ویرایش آیتم" :style="{ width: '32rem', maxWidth: '95vw' }">
    <div class="grid">
      <div class="col-12">
        <label class="block mb-2 font-semibold">دسته بندی</label>
        <Select
          v-model="itemEdit.category_id"
          :options="normalizedCategoryOptions"
          optionLabel="label"
          optionValue="value"
          class="w-full"
          :disabled="!normalizedCategoryOptions.length"
        />
      </div>
      <div class="col-12">
        <label class="block mb-2 font-semibold">نام</label>
        <InputText v-model="itemEdit.name" class="w-full" />
      </div>
      <div class="col-12">
        <label class="block mb-2 font-semibold">اسلاگ</label>
        <InputText v-model="itemEdit.slug" class="w-full" />
      </div>
      <div class="col-12 flex align-items-center gap-2">
        <Checkbox v-model="itemEdit.is_active" binary inputId="item-active-edit" />
        <label for="item-active-edit">فعال</label>
      </div>
    </div>
    <template #footer>
      <div class="flex gap-2 justify-content-end">
        <Button label="انصراف" severity="secondary" outlined @click="showEditItemModal = false" />
        <Button label="ذخیره تغییرات" icon="pi pi-check" @click="saveItemEdit" />
      </div>
    </template>
  </Dialog>

  <Dialog v-model:visible="showCreateVariantModal" modal header="افزودن زیرمجموعه جدید" :style="{ width: '32rem', maxWidth: '95vw' }">
    <div class="grid">
      <div class="col-12">
        <label class="block mb-2 font-semibold">آیتم اصلی</label>
        <Select
          v-model="variantForm.menu_item_id"
          :options="itemOptions"
          optionLabel="label"
          optionValue="value"
          placeholder="انتخاب آیتم"
          class="w-full"
          :disabled="!itemOptions.length"
        />
      </div>
      <div class="col-12">
        <label class="block mb-2 font-semibold">نام زیرمجموعه</label>
        <InputText v-model="variantForm.name" class="w-full" />
      </div>
      <div class="col-12">
        <label class="block mb-2 font-semibold">قیمت پایه</label>
        <InputNumber v-model="variantForm.price" class="w-full" inputClass="w-full" :min="0" />
      </div>
      <div class="col-12">
        <label class="block mb-2 font-semibold">قیمت با تخفیف (اختیاری)</label>
        <InputNumber v-model="variantForm.discount_price" class="w-full" inputClass="w-full" :min="0" />
      </div>
      <div v-if="variantError" class="col-12">
        <InlineMessage severity="error">{{ variantError }}</InlineMessage>
      </div>
      <div class="col-12 flex align-items-center gap-2">
        <Checkbox v-model="variantForm.is_active" binary inputId="var-active-new" />
        <label for="var-active-new">فعال</label>
      </div>
    </div>
    <template #footer>
      <div class="flex gap-2 justify-content-end">
        <Button label="انصراف" severity="secondary" outlined @click="showCreateVariantModal = false" />
        <Button label="ثبت زیرمجموعه" icon="pi pi-check" :disabled="Boolean(variantError)" @click="saveVariant" />
      </div>
    </template>
  </Dialog>

  <Dialog v-model:visible="showEditVariantModal" modal header="ویرایش زیرمجموعه" :style="{ width: '32rem', maxWidth: '95vw' }">
    <div class="grid">
      <div class="col-12">
        <label class="block mb-2 font-semibold">آیتم اصلی</label>
        <Select
          v-model="variantEdit.menu_item_id"
          :options="itemOptions"
          optionLabel="label"
          optionValue="value"
          class="w-full"
        />
      </div>
      <div class="col-12">
        <label class="block mb-2 font-semibold">نام</label>
        <InputText v-model="variantEdit.name" class="w-full" />
      </div>
      <div class="col-12">
        <label class="block mb-2 font-semibold">قیمت پایه</label>
        <InputNumber v-model="variantEdit.price" class="w-full" inputClass="w-full" :min="0" />
      </div>
      <div class="col-12">
        <label class="block mb-2 font-semibold">قیمت با تخفیف</label>
        <InputNumber v-model="variantEdit.discount_price" class="w-full" inputClass="w-full" :min="0" />
      </div>
      <div v-if="variantEditError" class="col-12">
        <InlineMessage severity="error">{{ variantEditError }}</InlineMessage>
      </div>
      <div class="col-12 flex align-items-center gap-2">
        <Checkbox v-model="variantEdit.is_active" binary inputId="var-active-edit" />
        <label for="var-active-edit">فعال</label>
      </div>
    </div>
    <template #footer>
      <div class="flex gap-2 justify-content-end">
        <Button label="انصراف" severity="secondary" outlined @click="showEditVariantModal = false" />
        <Button label="ذخیره تغییرات" icon="pi pi-check" :disabled="Boolean(variantEditError)" @click="saveVariantEdit" />
      </div>
    </template>
  </Dialog>

  <Dialog
    v-model:visible="showImageModal"
    modal
    header="ویرایش تصویر"
    :style="{ width: '28rem', maxWidth: '95vw' }"
  >
    <div class="flex flex-column gap-3 align-items-center">
      <div class="text-sm text-color-secondary">
        {{ currentImageItem ? `آیتم: ${currentImageItem.name}` : '' }}
      </div>

      <div
        v-if="currentImageUrl"
        class="w-full border-1 surface-border border-round p-2 flex justify-content-center"
      >
        <img
          :src="currentImageUrl"
          alt="تصویر آیتم"
          style="max-width: 100%; max-height: 16rem; object-fit: contain"
        />
      </div>
      <div v-else class="text-color-secondary">بدون تصویر</div>

      <input
        ref="imageFileInput"
        type="file"
        accept="image/*"
        style="display: none"
        @change="onImageFileChange"
      />
    </div>

    <template #footer>
      <div class="flex justify-content-between align-items-center w-full">
        <Button
          icon="pi pi-trash"
          severity="danger"
          text
          :disabled="!currentImageUrl || imageUploading"
          @click="removeCurrentImage"
        />
        <div class="flex gap-2">
          <Button label="بستن" severity="secondary" outlined @click="showImageModal = false" />
          <Button
            label="ویرایش عکس"
            icon="pi pi-upload"
            :loading="imageUploading"
            @click="openImagePicker"
          />
        </div>
      </div>
    </template>
  </Dialog>
</template>
