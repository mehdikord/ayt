<script setup>
import { reactive } from 'vue'
import { useAdminStore } from '@/stores/admin'

const adminStore = useAdminStore()

const profileForm = reactive({
  name: adminStore.session.profile.name,
  phone: adminStore.session.profile.phone,
  image: adminStore.session.profile.image
})

const saveProfile = () => {
  adminStore.updateProfile(profileForm)
}
</script>

<template>
  <Card>
    <template #title>پروفایل مدیر</template>
    <template #content>
      <div class="grid">
        <div class="col-12 md:col-6">
          <label class="block mb-2 font-semibold">نام مدیر</label>
          <InputText v-model="profileForm.name" class="w-full" />
        </div>
        <div class="col-12 md:col-6">
          <label class="block mb-2 font-semibold">شماره موبایل</label>
          <InputText v-model="profileForm.phone" class="w-full" />
        </div>
        <div class="col-12 md:col-6">
          <label class="block mb-2 font-semibold">آدرس تصویر پروفایل</label>
          <InputText v-model="profileForm.image" class="w-full" />
        </div>
        <div class="col-12 md:col-6">
          <label class="block mb-2 font-semibold">وضعیت حساب</label>
          <Tag :value="adminStore.session.profile.isActive ? 'فعال' : 'غیرفعال'" :severity="adminStore.session.profile.isActive ? 'success' : 'danger'" />
        </div>
        <div class="col-12">
          <small class="text-color-secondary">
            آخرین ورود:
            {{ adminStore.session.profile.lastLoginAt || 'هنوز ثبت نشده است' }}
          </small>
        </div>
        <div class="col-12 flex justify-content-end">
          <Button label="ذخیره تغییرات" icon="pi pi-save" @click="saveProfile" />
        </div>
      </div>
    </template>
  </Card>
</template>
