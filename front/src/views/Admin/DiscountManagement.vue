<script setup>
import { computed, reactive, ref } from 'vue'

const discounts = ref([
  { id: 1, code: 'WELCOME20', discount_type: 'percent', discount_value: 20, expires_at: '1405/03/01', is_active: true },
  { id: 2, code: 'VIP150', discount_type: 'fixed_amount', discount_value: 150000, expires_at: '1405/02/10', is_active: true },
  { id: 3, code: 'NEWUSER', discount_type: 'percent', discount_value: 15, expires_at: null, is_active: false }
])

const discountTypeOptions = [
  { label: 'درصدی', value: 'percent' },
  { label: 'مبلغ ثابت', value: 'fixed_amount' }
]

const discountForm = reactive({
  code: '',
  discount_type: 'percent',
  discount_value: 0,
  expires_at: '',
  is_active: true
})

const isExpired = (expiresAt) => {
  if (!expiresAt) return false
  return expiresAt <= '1405/02/16'
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

const valueHint = computed(() =>
  discountForm.discount_type === 'percent'
    ? 'مثال: 20 (حداکثر 100)'
    : 'مثال: 150000 تومان'
)

const saveDiscount = () => {
  if (!discountForm.code || discountForm.discount_value <= 0) return
  discounts.value.unshift({ id: Date.now(), ...discountForm })
  Object.assign(discountForm, {
    code: '',
    discount_type: 'percent',
    discount_value: 0,
    expires_at: '',
    is_active: true
  })
}

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
    <p class="text-color-secondary">در حال بارگذاری تخفیف ها...</p>
  </div>
  <Message v-else-if="uiState === 'error'" severity="error" :closable="false">
    خطا در دریافت یا ثبت تخفیف ها.
  </Message>
  <Message v-else-if="uiState === 'empty' || discounts.length === 0" severity="secondary" :closable="false">
    تخفیفی برای نمایش وجود ندارد.
  </Message>
  <div v-else class="grid">
    <div class="col-12 xl:col-8">
      <Card>
        <template #title>مدیریت تخفیف ها</template>
        <template #content>
          <DataTable :value="discounts" stripedRows responsiveLayout="scroll">
            <Column field="code" header="کد" />
            <Column header="نوع">
              <template #body="{ data }">
                {{ data.discount_type === 'percent' ? 'درصدی' : 'مبلغ ثابت' }}
              </template>
            </Column>
            <Column field="discount_value" header="مقدار" />
            <Column field="expires_at" header="انقضا" />
            <Column header="وضعیت">
              <template #body="{ data }">
                <Tag :value="statusLabel(data)" :severity="statusSeverity(data)" />
              </template>
            </Column>
          </DataTable>
        </template>
      </Card>
    </div>

    <div class="col-12 xl:col-4">
      <Card>
        <template #title>افزودن تخفیف جدید</template>
        <template #content>
          <div class="grid">
            <div class="col-12">
              <label class="block mb-2 font-semibold">کد تخفیف</label>
              <InputText v-model="discountForm.code" class="w-full" placeholder="مثال: VIP200" />
            </div>
            <div class="col-12">
              <label class="block mb-2 font-semibold">نوع تخفیف</label>
              <Select
                v-model="discountForm.discount_type"
                :options="discountTypeOptions"
                optionLabel="label"
                optionValue="value"
                class="w-full"
              />
            </div>
            <div class="col-12">
              <label class="block mb-2 font-semibold">مقدار تخفیف</label>
              <InputNumber v-model="discountForm.discount_value" class="w-full" inputClass="w-full" />
              <small class="text-color-secondary">{{ valueHint }}</small>
            </div>
            <div class="col-12">
              <label class="block mb-2 font-semibold">تاریخ انقضا (اختیاری)</label>
              <InputText v-model="discountForm.expires_at" class="w-full" placeholder="1405/03/20" />
            </div>
            <div class="col-12">
              <Button label="ثبت تخفیف" class="w-full" @click="saveDiscount" />
            </div>
          </div>
        </template>
      </Card>
    </div>
  </div>
</template>
