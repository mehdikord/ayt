<script setup>
import { reactive, ref } from 'vue'
import { useAdminStore } from '@/stores/admin'
import { useAdminFeedback } from '@/composables/useAdminFeedback'

const adminStore = useAdminStore()
const feedback = useAdminFeedback()

const profileForm = reactive({
  name: adminStore.session.profile.name
})

const isSaving = ref(false)
const fieldErrors = reactive({ name: '' })

const saveProfile = async () => {
  fieldErrors.name = ''
  isSaving.value = true
  try {
    await adminStore.patchProfileRemote({
      name: profileForm.name?.trim()
    })
    feedback.success('نام مدیر با موفقیت ذخیره شد.')
  } catch (error) {
    if (error?.status === 422) {
      fieldErrors.name = error?.errors?.name?.[0] || ''
    }
    feedback.error(error?.message || 'ذخیره پروفایل انجام نشد.')
  } finally {
    isSaving.value = false
  }
}
</script>

<template>
  <Card>
    <template #title>پروفایل مدیر</template>
    <template #content>
      <div class="grid">
        <div class="col-12 md:col-6">
          <label class="block mb-2 font-semibold">نام مدیر</label>
          <InputText v-model="profileForm.name" class="w-full" autocomplete="name" />
          <small v-if="fieldErrors.name" class="text-red-500">{{ fieldErrors.name }}</small>
        </div>
        <div class="col-12 md:col-6">
          <label class="block mb-2 font-semibold">شماره موبایل</label>
          <InputText :model-value="adminStore.session.profile.phone" class="w-full" disabled />
          <small class="text-color-secondary">در حال حاضر API مدیر برای ویرایش شماره موبایل در این فرم در نظر گرفته نشده است.</small>
        </div>
        <div class="col-12 md:col-6">
          <label class="block mb-2 font-semibold">تصویر</label>
          <InputText :model-value="adminStore.session.profile.image || '-'" class="w-full" disabled />
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
          <Button label="ذخیره تغییرات" icon="pi pi-save" :loading="isSaving" @click="saveProfile" />
        </div>
      </div>
    </template>
  </Card>
</template>
