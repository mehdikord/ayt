<script setup>
import { computed, onMounted, ref } from 'vue'
import { useAdminStore } from '@/stores/admin'
import AdminStatsCards from '@/components/Admin/AdminStatsCards.vue'

const adminStore = useAdminStore()

onMounted(() => {
  adminStore.bootstrap()
})

const uiState = ref('ready')
const stateOptions = [
  { label: 'Ready', value: 'ready' },
  { label: 'Loading', value: 'loading' },
  { label: 'Empty', value: 'empty' },
  { label: 'Error', value: 'error' }
]

const hasData = computed(() => adminStore.dashboardStats.length > 0 && adminStore.rows.length > 0)
</script>

<template>
  <div class="flex justify-content-end mb-3">
    <SelectButton v-model="uiState" :options="stateOptions" optionLabel="label" optionValue="value" />
  </div>
  <div v-if="uiState === 'loading'" class="p-4 text-center">
    <ProgressSpinner style="width: 42px; height: 42px" strokeWidth="6" />
    <p class="text-color-secondary">در حال بارگذاری داشبورد...</p>
  </div>
  <Message v-else-if="uiState === 'error'" severity="error" :closable="false">
    خطا در دریافت اطلاعات داشبورد. لطفا دوباره تلاش کنید.
  </Message>
  <Message v-else-if="uiState === 'empty' || !hasData" severity="secondary" :closable="false">
    داده ای برای نمایش در داشبورد وجود ندارد.
  </Message>
  <div v-else class="grid">
    <div class="col-12">
      <AdminStatsCards :items="adminStore.dashboardStats" />
    </div>
  </div>
</template>
