<script setup>
import { computed } from 'vue'
import { useRoute } from 'vue-router'
import { useAdminStore } from '@/stores/admin'
import AdminSidebar from '@/components/Admin/AdminSidebar.vue'
import Toast from 'primevue/toast'
import ConfirmDialog from 'primevue/confirmdialog'

const route = useRoute()
const adminStore = useAdminStore()

const pageTitle = computed(() => route.meta?.title || 'پنل مدیریت')
</script>

<template>
  <div class="admin-layout" dir="rtl">
    <Toast position="bottom-center" />
    <ConfirmDialog />
    <AdminSidebar />
    <div class="admin-main">
      <header class="admin-header">
        <div>
          <h1 class="admin-title">{{ pageTitle }}</h1>
          <p class="admin-subtitle">مدیریت حرفه ای کافی شاپ AYT</p>
        </div>
        <div class="admin-profile-chip">
          <Avatar icon="pi pi-user" shape="circle" />
          <div>
            <div class="font-semibold admin-manager-name">{{ adminStore.session.profile.name }}</div>
            <small class="text-color-secondary">مدیر سیستم</small>
          </div>
        </div>
      </header>

      <main class="admin-content">
        <router-view />
      </main>
    </div>
  </div>
</template>

<style scoped>
.admin-layout {
  min-height: 100vh;
  background: linear-gradient(180deg, #f7f8fc 0%, #f2f4fa 100%);
  display: grid;
  grid-template-columns: 280px 1fr;
}

.admin-main {
  padding: 1.25rem;
}

.admin-header {
  border-radius: 18px;
  border: 1px solid #e4e8f3;
  background: #fff;
  padding: 1rem 1.25rem;
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 1rem;
  box-shadow: 0 8px 24px rgba(14, 25, 80, 0.05);
}

.admin-title {
  margin: 0;
  color: #1e293b;
  font-size: 1.25rem;
}

.admin-subtitle {
  margin: 0.3rem 0 0;
  color: #64748b;
}

.admin-profile-chip {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  background: #f8fafc;
  border: 1px solid #e2e8f0;
  border-radius: 999px;
  padding: 0.45rem 0.8rem;
}

.admin-content {
  margin-top: 1rem;
}

.admin-manager-name {
  color: #000;
}

@media (max-width: 1024px) {
  .admin-layout {
    grid-template-columns: 1fr;
  }

  .admin-main {
    padding: 1rem;
  }
}
</style>
