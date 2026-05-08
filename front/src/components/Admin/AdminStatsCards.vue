<script setup>
defineProps({
  items: {
    type: Array,
    default: () => []
  }
})
</script>

<template>
  <div class="grid">
    <div v-for="item in items" :key="item.title" class="col-12 md:col-6 xl:col-3">
      <Card class="stat-card">
        <template #content>
          <div class="flex justify-content-between align-items-center">
            <div>
              <small class="text-color-secondary">{{ item.title }}</small>
              <h3 class="m-0 mt-2">{{ item.value }}</h3>
            </div>
            <div class="stat-icon">
              <i :class="item.icon"></i>
            </div>
          </div>
        </template>
      </Card>
    </div>
  </div>
</template>

<style scoped>
.stat-card {
  border-radius: 16px;
  border: 1px solid #e4e8f3;
  box-shadow: 0 8px 24px rgba(14, 25, 80, 0.05);
}

.stat-icon {
  width: 42px;
  height: 42px;
  border-radius: 12px;
  display: grid;
  place-items: center;
  color: #1e40af;
  background: #dbeafe;
}
</style>
