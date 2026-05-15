<script setup>
import { computed, onMounted, reactive, ref } from 'vue'
import { adminDiscountsApi } from '@/services/admin/endpoints/discountsApi'
import { useAdminFeedback } from '@/composables/useAdminFeedback'

const feedback = useAdminFeedback()

const discounts = ref([])
const isLoading = ref(false)
const isSaving = ref(false)
const errorMessage = ref('')
const editingId = ref(null)
const discountTypeOptions = [
  { label: 'درصدی', value: 'percent' },
  { label: 'مبلغ ثابت', value: 'fixed_amount' }
]
const statusOptions = [
  { label: 'همه', value: null },
  { label: 'فعال', value: true },
  { label: 'غیرفعال', value: false }
]
const filters = reactive({
  user_id: null,
  is_active: null,
  discount_type: null
})
const discountForm = reactive({
  user_id: null,
  title: '',
  code: '',
  discount_type: 'percent',
  discount_value: 0,
  expires_at: '',
  is_active: true
})
const fieldErrors = reactive({
  user_id: '',
  code: '',
  discount_value: ''
})

const hasDiscounts = computed(() => discounts.value.length > 0)
const valueHint = computed(() =>
  discountForm.discount_type === 'percent' ? 'مثال: 20 (حداکثر 100)' : 'مثال: 150000 تومان'
)

const isExpired = (expiresAt) => {
  if (!expiresAt) return false
  return new Date(expiresAt).getTime() < Date.now()
}

const statusLabel = (row) => {
  if (!row.is_active) return 'غیرفعال'
  if (isExpired(row.expires_at)) return 'منقضی'
  return 'فعال'
}

const statusSeverity = (row) => {
  if (!row.is_active) return 'danger'
  if (isExpired(row.expires_at)) return 'warn'
  return 'success'
}

const resetForm = () => {
  editingId.value = null
  Object.assign(discountForm, {
    user_id: null,
    title: '',
    code: '',
    discount_type: 'percent',
    discount_value: 0,
    expires_at: '',
    is_active: true
  })
}

const loadDiscounts = async () => {
  isLoading.value = true
  errorMessage.value = ''
  try {
    const response = await adminDiscountsApi.list({
      user_id: filters.user_id || undefined,
      is_active: filters.is_active,
      discount_type: filters.discount_type || undefined
    })
    discounts.value = response?.items || []
  } catch (error) {
    errorMessage.value = error?.message || 'خطا در دریافت تخفیف ها.'
  } finally {
    isLoading.value = false
  }
}

const startEdit = (row) => {
  editingId.value = row.id
  Object.assign(discountForm, {
    user_id: row.user_id,
    title: row.title || '',
    code: row.code,
    discount_type: row.discount_type,
    discount_value: Number(row.discount_value),
    expires_at: row.expires_at ? row.expires_at.slice(0, 10) : '',
    is_active: row.is_active
  })
}

const saveDiscount = async () => {
  fieldErrors.user_id = ''
  fieldErrors.code = ''
  fieldErrors.discount_value = ''
  isSaving.value = true
  errorMessage.value = ''
  try {
    const payload = {
      user_id: discountForm.user_id,
      title: discountForm.title || null,
      code: discountForm.code,
      discount_type: discountForm.discount_type,
      discount_value: discountForm.discount_value,
      expires_at: discountForm.expires_at || null,
      is_active: discountForm.is_active
    }
    if (editingId.value) {
      await adminDiscountsApi.update(editingId.value, payload)
    } else {
      await adminDiscountsApi.create(payload)
    }
    resetForm()
    await loadDiscounts()
    feedback.success('اطلاعات تخفیف ذخیره شد.')
  } catch (error) {
    if (error?.status === 422) {
      fieldErrors.user_id = error?.errors?.user_id?.[0] || ''
      fieldErrors.code = error?.errors?.code?.[0] || ''
      fieldErrors.discount_value = error?.errors?.discount_value?.[0] || ''
    }
    errorMessage.value = error?.message || 'ذخیره تخفیف انجام نشد.'
  } finally {
    isSaving.value = false
  }
}

const requestDeleteDiscount = (row) => {
  feedback.confirmDelete({
    message: `تخفیف «${row.code}» برای همیشه حذف شود؟`,
    accept: async () => {
      errorMessage.value = ''
      try {
        await adminDiscountsApi.delete(row.id)
        await loadDiscounts()
        feedback.success('تخفیف حذف شد.')
      } catch (error) {
        errorMessage.value = error?.message || 'حذف تخفیف انجام نشد.'
        feedback.error(errorMessage.value)
      }
    }
  })
}

onMounted(loadDiscounts)
</script>

<template>
  <div class="grid">
    <div class="col-12 xl:col-8">
      <Card>
        <template #title>مدیریت تخفیف ها</template>
        <template #content>
          <div class="flex gap-2 flex-wrap mb-3">
            <InputNumber v-model="filters.user_id" class="w-10rem" placeholder="user_id" />
            <Select v-model="filters.discount_type" :options="discountTypeOptions" optionLabel="label" optionValue="value" placeholder="نوع" class="w-10rem" />
            <Select v-model="filters.is_active" :options="statusOptions" optionLabel="label" optionValue="value" class="w-10rem" />
            <Button label="فیلتر" icon="pi pi-filter" @click="loadDiscounts" />
          </div>
          <Message v-if="errorMessage" severity="error" :closable="false" class="mb-3">{{ errorMessage }}</Message>
          <div v-if="isLoading" class="p-4 text-center"><ProgressSpinner style="width: 42px; height: 42px" strokeWidth="6" /></div>
          <Message v-else-if="!hasDiscounts" severity="secondary" :closable="false">تخفیفی برای نمایش وجود ندارد.</Message>
          <DataTable v-else :value="discounts" stripedRows responsiveLayout="scroll">
            <Column field="code" header="کد" />
            <Column field="user_id" header="کاربر" />
            <Column header="نوع">
              <template #body="{ data }">{{ data.discount_type === 'percent' ? 'درصدی' : 'مبلغ ثابت' }}</template>
            </Column>
            <Column field="discount_value" header="مقدار" />
            <Column field="expires_at" header="انقضا" />
            <Column header="وضعیت">
              <template #body="{ data }"><Tag :value="statusLabel(data)" :severity="statusSeverity(data)" /></template>
            </Column>
            <Column header="اکشن">
              <template #body="{ data }">
                <div class="flex gap-2">
                  <Button icon="pi pi-pencil" text size="small" @click="startEdit(data)" />
                  <Button icon="pi pi-trash" text severity="danger" size="small" @click="requestDeleteDiscount(data)" />
                </div>
              </template>
            </Column>
          </DataTable>
        </template>
      </Card>
    </div>

    <div class="col-12 xl:col-4">
      <Card>
        <template #title>{{ editingId ? 'ویرایش تخفیف' : 'افزودن تخفیف جدید' }}</template>
        <template #content>
          <div class="grid">
            <div class="col-12">
              <label class="block mb-2 font-semibold">شناسه کاربر</label>
              <InputNumber v-model="discountForm.user_id" class="w-full" />
              <small v-if="fieldErrors.user_id" class="text-red-500">{{ fieldErrors.user_id }}</small>
            </div>
            <div class="col-12">
              <label class="block mb-2 font-semibold">عنوان</label>
              <InputText v-model="discountForm.title" class="w-full" />
            </div>
            <div class="col-12">
              <label class="block mb-2 font-semibold">کد تخفیف</label>
              <InputText v-model="discountForm.code" class="w-full" placeholder="VIP200" />
              <small v-if="fieldErrors.code" class="text-red-500">{{ fieldErrors.code }}</small>
            </div>
            <div class="col-12">
              <label class="block mb-2 font-semibold">نوع تخفیف</label>
              <Select v-model="discountForm.discount_type" :options="discountTypeOptions" optionLabel="label" optionValue="value" class="w-full" />
            </div>
            <div class="col-12">
              <label class="block mb-2 font-semibold">مقدار تخفیف</label>
              <InputNumber v-model="discountForm.discount_value" class="w-full" inputClass="w-full" />
              <small class="text-color-secondary">{{ valueHint }}</small>
              <small v-if="fieldErrors.discount_value" class="text-red-500 block">{{ fieldErrors.discount_value }}</small>
            </div>
            <div class="col-12">
              <label class="block mb-2 font-semibold">انقضا (YYYY-MM-DD)</label>
              <InputText v-model="discountForm.expires_at" class="w-full" />
            </div>
            <div class="col-12 flex align-items-center gap-2">
              <Checkbox v-model="discountForm.is_active" binary inputId="discount-active" />
              <label for="discount-active">فعال</label>
            </div>
            <div class="col-12 flex gap-2">
              <Button :label="editingId ? 'ذخیره تغییرات' : 'ثبت تخفیف'" class="w-full" :loading="isSaving" @click="saveDiscount" />
              <Button v-if="editingId" label="انصراف" severity="secondary" outlined @click="resetForm" />
            </div>
          </div>
        </template>
      </Card>
    </div>
  </div>
</template>
