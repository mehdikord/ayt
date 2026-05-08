<script setup>
import { computed, reactive, ref } from 'vue'
import { storeToRefs } from 'pinia'
import { useAdminMenuStore } from '@/stores/adminMenu'

const menuStore = useAdminMenuStore()
const { items, variants, categoryOptions, itemOptions } = storeToRefs(menuStore)

const uiState = ref('ready')
const stateOptions = [
  { label: 'Ready', value: 'ready' },
  { label: 'Loading', value: 'loading' },
  { label: 'Empty', value: 'empty' },
  { label: 'Error', value: 'error' }
]

const categoryFilter = ref(null)
const statusFilter = ref(null)
const statusOptions = [
  { label: 'فعال', value: true },
  { label: 'غیرفعال', value: false }
]

const categoryLabel = (categoryId) =>
  menuStore.categories.find((c) => c.id === categoryId)?.name ?? '—'

const filteredItems = computed(() =>
  items.value.filter((item) => {
    const categoryMatch = categoryFilter.value == null || item.category_id === categoryFilter.value
    const statusMatch = statusFilter.value === null || item.is_active === statusFilter.value
    return categoryMatch && statusMatch
  })
)

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

const openCreateItemModal = () => {
  Object.assign(itemForm, {
    name: '',
    slug: '',
    category_id: null,
    is_active: true
  })
  showCreateItemModal.value = true
}

const saveItem = () => {
  if (!itemForm.name?.trim() || !itemForm.slug?.trim() || itemForm.category_id == null) return
  menuStore.addItem({ ...itemForm })
  showCreateItemModal.value = false
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

const saveItemEdit = () => {
  if (!editingItemId.value) return
  menuStore.updateItem(editingItemId.value, { ...itemEdit })
  showEditItemModal.value = false
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

const saveVariant = () => {
  if (!variantForm.name?.trim() || variantForm.menu_item_id == null || variantError.value) return
  menuStore.addVariant({ ...variantForm })
  showCreateVariantModal.value = false
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

const saveVariantEdit = () => {
  if (variantEditError.value || !editingVariantId.value) return
  menuStore.updateVariant(editingVariantId.value, { ...variantEdit })
  showEditVariantModal.value = false
}

const itemLabel = (itemId) => items.value.find((i) => i.id === itemId)?.name ?? '—'
</script>

<template>
  <div class="flex justify-content-end mb-3">
    <SelectButton v-model="uiState" :options="stateOptions" optionLabel="label" optionValue="value" />
  </div>
  <div v-if="uiState === 'loading'" class="p-4 text-center">
    <ProgressSpinner style="width: 42px; height: 42px" strokeWidth="6" />
    <p class="text-color-secondary">در حال بارگذاری آیتم های منو...</p>
  </div>
  <Message v-else-if="uiState === 'error'" severity="error" :closable="false">
    خطا در دریافت اطلاعات آیتم های منو.
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
          <Message v-if="uiState === 'empty' || !hasMenuData" severity="secondary" :closable="false" class="mb-3">
            هنوز آیتم یا زیرمجموعه ای ثبت نشده است.
          </Message>
          <Tabs value="0">
            <TabList>
              <Tab value="0">آیتم های منو</Tab>
              <Tab value="1">زیرمجموعه ها (واریانت)</Tab>
            </TabList>
            <TabPanels>
              <TabPanel value="0">
                <div class="flex flex-wrap gap-2 mb-3">
                  <Select
                    v-model="categoryFilter"
                    :options="categoryOptions"
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
                <DataTable :value="filteredItems" stripedRows responsiveLayout="scroll">
                  <Column field="name" header="نام آیتم" />
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
                  <Column header="اکشن">
                    <template #body="{ data }">
                      <div class="flex gap-2 flex-wrap">
                        <Button icon="pi pi-pencil" size="small" text @click="openItemEdit(data)" />
                        <Button icon="pi pi-power-off" size="small" text @click="menuStore.toggleItemActive(data.id)" />
                        <Button icon="pi pi-trash" size="small" text severity="danger" @click="menuStore.removeItem(data.id)" />
                      </div>
                    </template>
                  </Column>
                </DataTable>
              </TabPanel>
              <TabPanel value="1">
                <DataTable :value="variants" stripedRows responsiveLayout="scroll">
                  <Column field="name" header="نام زیرمجموعه" />
                  <Column header="آیتم اصلی">
                    <template #body="{ data }">
                      {{ itemLabel(data.menu_item_id) }}
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
                        <Button icon="pi pi-power-off" size="small" text @click="menuStore.toggleVariantActive(data.id)" />
                        <Button icon="pi pi-trash" size="small" text severity="danger" @click="menuStore.removeVariant(data.id)" />
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
          :options="categoryOptions"
          optionLabel="label"
          optionValue="value"
          placeholder="انتخاب کنید"
          class="w-full"
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
          :options="categoryOptions"
          optionLabel="label"
          optionValue="value"
          class="w-full"
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
</template>
