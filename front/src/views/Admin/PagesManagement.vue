<script setup>
import { reactive, ref } from 'vue'

const pageForm = reactive({
  page_key: 'about',
  title: 'درباره ما',
  content: 'این متن نمونه برای صفحه درباره ما است و از پنل مدیریت قابل ویرایش است.',
  is_active: true
})

const uiState = ref('ready')
const stateOptions = [
  { label: 'Ready', value: 'ready' },
  { label: 'Loading', value: 'loading' },
  { label: 'Empty', value: 'empty' },
  { label: 'Error', value: 'error' }
]
</script>

<template>
  <div class="flex justify-content-end mb-3">
    <SelectButton v-model="uiState" :options="stateOptions" optionLabel="label" optionValue="value" />
  </div>
  <div v-if="uiState === 'loading'" class="p-4 text-center">
    <ProgressSpinner style="width: 42px; height: 42px" strokeWidth="6" />
    <p class="text-color-secondary">در حال بارگذاری صفحه About...</p>
  </div>
  <Message v-else-if="uiState === 'error'" severity="error" :closable="false">
    خطا در دریافت اطلاعات صفحه About.
  </Message>
  <Message v-else-if="uiState === 'empty'" severity="secondary" :closable="false">
    محتوایی برای این صفحه ثبت نشده است.
  </Message>
  <Card v-else>
    <template #title>مدیریت صفحه About (static_pages)</template>
    <template #content>
      <div class="grid">
        <div class="col-12 md:col-4">
          <label class="block mb-2 font-semibold">page_key</label>
          <InputText v-model="pageForm.page_key" class="w-full" disabled />
        </div>
        <div class="col-12 md:col-8">
          <label class="block mb-2 font-semibold">عنوان</label>
          <InputText v-model="pageForm.title" class="w-full" />
        </div>
        <div class="col-12">
          <label class="block mb-2 font-semibold">محتوا</label>
          <Textarea v-model="pageForm.content" rows="8" class="w-full" />
        </div>
        <div class="col-12 flex align-items-center justify-content-between">
          <Tag :value="pageForm.is_active ? 'فعال' : 'غیرفعال'" :severity="pageForm.is_active ? 'success' : 'danger'" />
          <div class="flex gap-2">
            <Button label="پیش نمایش" icon="pi pi-eye" severity="secondary" outlined />
            <Button label="ذخیره تغییرات" icon="pi pi-save" />
          </div>
        </div>
      </div>
    </template>
  </Card>
</template>
