<script setup>
import { computed, onMounted, reactive, ref } from 'vue'
import { storeToRefs } from 'pinia'
import { useAdminMenuStore } from '@/stores/adminMenu'
import { useAdminFeedback } from '@/composables/useAdminFeedback'

const menuStore = useAdminMenuStore()
const { categoriesWithStats } = storeToRefs(menuStore)
const feedback = useAdminFeedback()

const includeInactive = ref(true)
const errorMessage = ref('')

const categoryForm = reactive({
  name: '',
  slug: '',
  sort_order: 1,
  is_active: true
})

const editingId = ref(null)
const categoryEdit = reactive({
  name: '',
  slug: '',
  sort_order: 1,
  is_active: true
})

const showCreateModal = ref(false)
const showEditModal = ref(false)

const hasCategories = computed(() => menuStore.categories.length > 0)
const isLoading = computed(() => menuStore.state.isLoading)
const hasError = computed(() => Boolean(menuStore.state.error))
const canCreateCategory = computed(() => Boolean(categoryForm.name?.trim() && categoryForm.slug?.trim()))

onMounted(async () => {
  await menuStore.bootstrap({ includeInactive: includeInactive.value })
})

const openCreateModal = () => {
  Object.assign(categoryForm, {
    name: '',
    slug: '',
    sort_order: 1,
    is_active: true
  })
  showCreateModal.value = true
}

const saveNew = async () => {
  if (!categoryForm.name?.trim() || !categoryForm.slug?.trim()) return
  try {
    errorMessage.value = ''
    await menuStore.addCategory({ ...categoryForm })
    showCreateModal.value = false
    feedback.success('دسته‌بندی جدید ثبت شد.')
  } catch (error) {
    errorMessage.value = error.message
    feedback.error(error.message)
  }
}

const openEdit = (row) => {
  editingId.value = row.id
  categoryEdit.name = row.name
  categoryEdit.slug = row.slug
  categoryEdit.sort_order = row.sort_order
  categoryEdit.is_active = row.is_active
  showEditModal.value = true
}

const saveEdit = async () => {
  if (!editingId.value) return
  try {
    errorMessage.value = ''
    await menuStore.updateCategory(editingId.value, { ...categoryEdit })
    showEditModal.value = false
    feedback.success('دسته‌بندی به‌روز شد.')
  } catch (error) {
    errorMessage.value = error.message
    feedback.error(error.message)
  }
}

const toggleCategory = async (id) => {
  try {
    errorMessage.value = ''
    await menuStore.toggleCategoryActive(id)
  } catch (error) {
    errorMessage.value = error.message
  }
}

const requestRemoveCategory = (row) => {
  feedback.confirmDelete({
    message: `آیا دسته‌بندی «${row.name}» حذف شود؟ اگر آیتمی داشته باشد، سرور مانع این کار می‌شود.`,
    accept: async () => {
      try {
        errorMessage.value = ''
        await menuStore.removeCategory(row.id)
        feedback.success('دسته‌بندی حذف شد.')
      } catch (error) {
        errorMessage.value = error.message
        feedback.error(error.message)
      }
    }
  })
}

const sortedCategoryIds = () =>
  [...menuStore.categories]
    .sort((a, b) => (a.sort_order ?? 0) - (b.sort_order ?? 0))
    .map((c) => c.id)

const moveCategory = async (id, delta) => {
  const ids = sortedCategoryIds()
  const idx = ids.indexOf(id)
  const j = idx + delta
  if (idx < 0 || j < 0 || j >= ids.length) return
  ;[ids[idx], ids[j]] = [ids[j], ids[idx]]
  try {
    errorMessage.value = ''
    await menuStore.reorderCategories(ids)
    feedback.success('ترتیب دسته‌ها ذخیره شد.')
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
      <Checkbox v-model="includeInactive" binary inputId="include-inactive-categories" @change="reloadWithFilters" />
      <label for="include-inactive-categories" style="color: #000">نمایش غیرفعال ها</label>
    </div>
  </div>
  <div v-if="isLoading" class="p-4 text-center">
    <ProgressSpinner style="width: 42px; height: 42px" strokeWidth="6" />
    <p class="text-color-secondary">در حال بارگذاری دسته بندی ها...</p>
  </div>
  <Message v-else-if="hasError" severity="error" :closable="false">
    {{ menuStore.state.error }}
  </Message>
  <div v-else class="grid">
    <div class="col-12">
      <Card>
        <template #title>
          <div class="flex align-items-center justify-content-between gap-2">
            <span>لیست دسته بندی ها</span>
            <Button label="افزودن دسته بندی جدید" icon="pi pi-plus" @click="openCreateModal" />
          </div>
        </template>
        <template #content>
          <Message v-if="errorMessage" severity="error" :closable="false" class="mb-3">
            {{ errorMessage }}
          </Message>
          <Message v-if="!hasCategories" severity="secondary" :closable="false" class="mb-3">
            هنوز دسته بندی ای ثبت نشده است.
          </Message>
          <DataTable :value="categoriesWithStats" stripedRows responsiveLayout="scroll">
            <Column field="name" header="نام دسته" />
            <Column field="slug" header="اسلاگ" />
            <Column field="sort_order" header="ترتیب" />
            <Column header="تعداد آیتم ها">
              <template #body="{ data }">
                {{ data.items_count }}
              </template>
            </Column>
            <Column header="تعداد واریانت ها">
              <template #body="{ data }">
                {{ data.variants_count }}
              </template>
            </Column>
            <Column header="وضعیت">
              <template #body="{ data }">
                <Tag :value="data.is_active ? 'فعال' : 'غیرفعال'" :severity="data.is_active ? 'success' : 'danger'" />
              </template>
            </Column>
            <Column header="ترتیب">
              <template #body="{ data }">
                <div class="flex gap-1">
                  <Button title="جابجایی به بالا" icon="pi pi-angle-up" size="small" text @click="moveCategory(data.id, -1)" />
                  <Button title="جابجایی به پایین" icon="pi pi-angle-down" size="small" text @click="moveCategory(data.id, 1)" />
                </div>
              </template>
            </Column>
            <Column header="اکشن">
              <template #body="{ data }">
                <div class="flex gap-2 flex-wrap">
                  <Button icon="pi pi-pencil" size="small" text @click="openEdit(data)" />
                  <Button icon="pi pi-power-off" size="small" text @click="toggleCategory(data.id)" />
                  <Button icon="pi pi-trash" size="small" text severity="danger" @click="requestRemoveCategory(data)" />
                </div>
              </template>
            </Column>
          </DataTable>
        </template>
      </Card>
    </div>
  </div>

  <Dialog v-model:visible="showCreateModal" modal header="ایجاد دسته بندی جدید" :style="{ width: '30rem', maxWidth: '95vw' }">
    <div class="grid">
      <div class="col-12">
        <label class="block mb-2 font-semibold">نام</label>
        <InputText v-model="categoryForm.name" class="w-full" placeholder="مثال: نوشیدنی گرم" />
      </div>
      <div class="col-12">
        <label class="block mb-2 font-semibold">اسلاگ</label>
        <InputText v-model="categoryForm.slug" class="w-full" placeholder="hot-drinks" />
      </div>
      <div class="col-12">
        <label class="block mb-2 font-semibold">ترتیب نمایش</label>
        <InputNumber v-model="categoryForm.sort_order" class="w-full" inputClass="w-full" :min="0" />
      </div>
      <div class="col-12 flex align-items-center gap-2">
        <Checkbox v-model="categoryForm.is_active" binary inputId="cat-active-new" />
        <label for="cat-active-new">فعال</label>
      </div>
    </div>
    <template #footer>
      <div class="flex gap-2 justify-content-end">
        <Button label="انصراف" severity="secondary" outlined @click="showCreateModal = false" />
        <Button label="ثبت دسته بندی" icon="pi pi-check" :disabled="!canCreateCategory" @click="saveNew" />
      </div>
    </template>
  </Dialog>

  <Dialog v-model:visible="showEditModal" modal header="ویرایش دسته بندی" :style="{ width: '30rem', maxWidth: '95vw' }">
    <div class="grid">
      <div class="col-12">
        <label class="block mb-2 font-semibold">نام</label>
        <InputText v-model="categoryEdit.name" class="w-full" />
      </div>
      <div class="col-12">
        <label class="block mb-2 font-semibold">اسلاگ</label>
        <InputText v-model="categoryEdit.slug" class="w-full" />
      </div>
      <div class="col-12">
        <label class="block mb-2 font-semibold">ترتیب</label>
        <InputNumber v-model="categoryEdit.sort_order" class="w-full" inputClass="w-full" :min="0" />
      </div>
      <div class="col-12 flex align-items-center gap-2">
        <Checkbox v-model="categoryEdit.is_active" binary inputId="cat-active-edit" />
        <label for="cat-active-edit">فعال</label>
      </div>
    </div>
    <template #footer>
      <div class="flex gap-2 justify-content-end">
        <Button label="انصراف" severity="secondary" outlined @click="showEditModal = false" />
        <Button label="ذخیره تغییرات" icon="pi pi-check" @click="saveEdit" />
      </div>
    </template>
  </Dialog>
</template>
