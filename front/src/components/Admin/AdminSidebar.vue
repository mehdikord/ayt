<script setup>
import { computed } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useAdminStore } from '@/stores/admin'

const route = useRoute()
const router = useRouter()
const adminStore = useAdminStore()

const items = [
  { label: 'داشبورد', icon: 'pi pi-home', to: { name: 'admin-dashboard' } },
  { label: 'دسته بندی ها', icon: 'pi pi-folder', to: { name: 'admin-categories' } },
  { label: 'آیتم های منو', icon: 'pi pi-list-check', to: { name: 'admin-menu' } },
  { label: 'کاربران', icon: 'pi pi-users', to: { name: 'admin-users' } },
  { label: 'تخفیف ها', icon: 'pi pi-percentage', to: { name: 'admin-discounts' } },
  { label: 'صفحات ثابت', icon: 'pi pi-file-edit', to: { name: 'admin-pages' } },
  { label: 'تنظیمات', icon: 'pi pi-cog', to: { name: 'admin-settings' } }
]

const isActive = (name) => computed(() => route.name === name)

const logout = () => {
  adminStore.logout()
  router.push({ name: 'admin-login' })
}
</script>

<template>
  <aside class="admin-sidebar">
    <div>
      <div class="brand">
        <i class="pi pi-shield text-xl"></i>
        <div>
          <h2>پنل مدیریت AYT</h2>
          <small>Admin Console</small>
        </div>
      </div>

      <nav class="menu-list">
        <router-link
          v-for="item in items"
          :key="item.label"
          :to="item.to"
          class="menu-item"
          :class="{ active: isActive(item.to.name).value }"
        >
          <i :class="item.icon"></i>
          <span>{{ item.label }}</span>
        </router-link>
      </nav>
    </div>
    <Button
      label="خروج از حساب"
      icon="pi pi-sign-out"
      class="w-full mt-4"
      severity="danger"
      outlined
      @click="logout"
    />
  </aside>
</template>

<style scoped>
.admin-sidebar {
  background: linear-gradient(180deg, #0f172a 0%, #172554 100%);
  color: #fff;
  padding: 1.25rem 1rem;
  min-height: 100vh;
  position: sticky;
  top: 0;
}

.brand {
  display: flex;
  align-items: center;
  gap: 0.65rem;
  margin-bottom: 1.5rem;
}

.brand h2 {
  margin: 0;
  font-size: 1rem;
}

.brand small {
  color: #bfdbfe;
}

.menu-list {
  display: grid;
  gap: 0.5rem;
}

.menu-item {
  display: flex;
  align-items: center;
  gap: 0.65rem;
  color: #dbeafe;
  padding: 0.7rem 0.8rem;
  border-radius: 12px;
  transition: all 0.2s ease;
}

.menu-item.active,
.menu-item:hover {
  color: #fff;
  background: rgba(255, 255, 255, 0.16);
}

@media (max-width: 1024px) {
  .admin-sidebar {
    min-height: unset;
    position: static;
  }
}
</style>
