<script lang="ts">
import { defineComponent } from 'vue'
import { userDiscountsApi } from '@/services/user/endpoints/discountsApi'

export default defineComponent({
  name: "Discounts",
  data() {
    return {
      discounts: [],
      isLoading: false,
      errorMessage: ''
    }
  },
  async mounted() {
    this.isLoading = true
    this.errorMessage = ''
    try {
      this.discounts = await userDiscountsApi.myDiscounts()
    } catch (error: any) {
      this.errorMessage = error?.message || 'خطا در دریافت تخفیف‌ها'
      this.discounts = []
    } finally {
      this.isLoading = false
    }
  }
})
</script>

<template>
  <div class="pt-3">
    <div>
      <strong class="text-black-alpha-90 font-26">تخفیف‌های من</strong>
    </div>
    <div v-if="isLoading" class="mt-4">
      در حال دریافت اطلاعات...
    </div>
    <div v-else-if="errorMessage" class="mt-4 text-red-500">
      {{ errorMessage }}
    </div>
    <div v-else-if="!discounts.length" class="mt-4 text-black-alpha-70">
      تخفیف فعالی برای شما ثبت نشده است.
    </div>
    <div v-else class="mt-4">
      <div v-for="discount in discounts" :key="discount.id" class="p-3 mb-3 bg-white border-round-2xl">
        <div class="flex justify-content-between align-items-center">
          <strong class="text-black-alpha-90 font-20">{{ discount.title }}</strong>
          <Tag :value="discount.status === 'active' ? 'فعال' : 'منقضی'" :severity="discount.status === 'active' ? 'success' : 'contrast'" />
        </div>
        <div class="mt-2 ayt-text-dark font-16">
          کد: <strong>{{ discount.code }}</strong>
        </div>
        <div class="mt-2 ayt-text-dark font-16">
          مقدار: <strong>{{ discount.discountValue }}</strong>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>

</style>