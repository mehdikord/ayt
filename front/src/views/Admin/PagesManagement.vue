<script setup>
import { computed, onMounted, reactive, ref } from 'vue'
import { adminPagesApi } from '@/services/admin/endpoints/pagesApi'
import { useAdminFeedback } from '@/composables/useAdminFeedback'

const feedback = useAdminFeedback()

const pages = ref([])
const selectedPageId = ref(null)
const isLoading = ref(false)
const isSaving = ref(false)
const errorMessage = ref('')
const previewOpen = ref(false)
const fieldErrors = reactive({
  title: '',
  content: ''
})
const pageForm = reactive({
  page_key: '',
  title: '',
  content: '',
  is_active: true
})

const hasPages = computed(() => pages.value.length > 0)

const loadPages = async () => {
  isLoading.value = true
  errorMessage.value = ''
  try {
    pages.value = await adminPagesApi.list()
    const aboutPage = pages.value.find((p) => p.page_key === 'about')
    const target = aboutPage || pages.value[0]
    if (target) {
      await selectPage(target.id)
    }
  } catch (error) {
    errorMessage.value = error?.message || 'خطا در دریافت صفحات.'
  } finally {
    isLoading.value = false
  }
}

const selectPage = async (id) => {
  errorMessage.value = ''
  try {
    const page = await adminPagesApi.byId(id)
    selectedPageId.value = page.id
    Object.assign(pageForm, {
      page_key: page.page_key,
      title: page.title,
      content: page.content,
      is_active: page.is_active
    })
  } catch (error) {
    errorMessage.value = error?.message || 'دریافت جزئیات صفحه انجام نشد.'
  }
}

const savePage = async () => {
  if (!selectedPageId.value) return
  fieldErrors.title = ''
  fieldErrors.content = ''
  isSaving.value = true
  errorMessage.value = ''
  try {
    await adminPagesApi.update(selectedPageId.value, {
      title: pageForm.title,
      content: pageForm.content,
      is_active: pageForm.is_active
    })
    await loadPages()
    feedback.success('صفحه ثابت به‌روز شد.')
  } catch (error) {
    if (error?.status === 422) {
      fieldErrors.title = error?.errors?.title?.[0] || ''
      fieldErrors.content = error?.errors?.content?.[0] || ''
    }
    errorMessage.value = error?.message || 'ذخیره صفحه انجام نشد.'
  } finally {
    isSaving.value = false
  }
}

onMounted(loadPages)
</script>

<template>
  <div v-if="isLoading" class="p-4 text-center">
    <ProgressSpinner style="width: 42px; height: 42px" strokeWidth="6" />
    <p class="text-color-secondary">در حال بارگذاری صفحات...</p>
  </div>
  <Message v-else-if="errorMessage" severity="error" :closable="false">{{ errorMessage }}</Message>
  <Message v-else-if="!hasPages" severity="secondary" :closable="false">محتوایی برای این صفحه ثبت نشده است.</Message>
  <Card v-else>
    <template #title>مدیریت صفحات ثابت</template>
    <template #content>
      <div class="grid">
        <div class="col-12 md:col-4">
          <label class="block mb-2 font-semibold">انتخاب صفحه</label>
          <Select
            :model-value="selectedPageId"
            :options="pages"
            optionLabel="page_key"
            optionValue="id"
            class="w-full"
            @update:model-value="selectPage"
          />
        </div>
        <div class="col-12 md:col-8">
          <label class="block mb-2 font-semibold">عنوان</label>
          <InputText v-model="pageForm.title" class="w-full" />
          <small v-if="fieldErrors.title" class="text-red-500">{{ fieldErrors.title }}</small>
        </div>
        <div class="col-12">
          <label class="block mb-2 font-semibold">محتوا</label>
          <Textarea v-model="pageForm.content" rows="8" class="w-full" />
          <small v-if="fieldErrors.content" class="text-red-500">{{ fieldErrors.content }}</small>
        </div>
        <div class="col-12 flex align-items-center gap-2">
          <Checkbox v-model="pageForm.is_active" binary inputId="page-active-edit" />
          <label for="page-active-edit">صفحه فعال است</label>
        </div>
        <div class="col-12 flex align-items-center justify-content-between">
          <Tag :value="pageForm.is_active ? 'فعال' : 'غیرفعال'" :severity="pageForm.is_active ? 'success' : 'danger'" />
          <div class="flex gap-2">
            <Button label="پیش نمایش" icon="pi pi-eye" severity="secondary" outlined @click="previewOpen = true" />
            <Button label="ذخیره تغییرات" icon="pi pi-save" :loading="isSaving" @click="savePage" />
          </div>
        </div>
      </div>
    </template>
  </Card>

  <Dialog v-model:visible="previewOpen" modal header="پیش نمایش صفحه" :style="{ width: '50rem', maxWidth: '95vw' }">
    <h3>{{ pageForm.title }}</h3>
    <div style="white-space: pre-wrap">{{ pageForm.content }}</div>
  </Dialog>
</template>
