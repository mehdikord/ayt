<script setup>
import { computed, onMounted } from 'vue'
import { useAdminStore } from '@/stores/admin'
import AdminStatsCards from '@/components/Admin/AdminStatsCards.vue'

const adminStore = useAdminStore()

onMounted(() => {
  adminStore.loadDashboardSummary()
})

const hasData = computed(() => adminStore.dashboardStats.length > 0)
const isLoading = computed(() => adminStore.dashboardState.isLoading)
const hasError = computed(() => Boolean(adminStore.dashboardState.error))
</script>

<template>
  <div v-if="isLoading" class="p-4 text-center">
    <ProgressSpinner style="width: 42px; height: 42px" strokeWidth="6" />
    <p class="text-color-secondary">در حال بارگذاری داشبورد...</p>
  </div>
  <Message v-else-if="hasError" severity="error" :closable="false">
    خطا در دریافت اطلاعات داشبورد. لطفا دوباره تلاش کنید.
  </Message>
  <Message v-else-if="!hasData" severity="secondary" :closable="false">
    داده ای برای نمایش در داشبورد وجود ندارد.
  </Message>
  <div v-else class="grid">
    <div class="col-12">
      <AdminStatsCards :items="adminStore.dashboardStats" />
    </div>
  </div>
</template>
