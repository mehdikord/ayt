<script setup>
import { reactive, ref } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useAdminStore } from '@/stores/admin'

const router = useRouter()
const route = useRoute()
const adminStore = useAdminStore()

const form = reactive({
  phone: '',
  password: '',
  remember: true
})

const loading = ref(false)
const errorMessage = ref('')
const fieldErrors = reactive({
  phone: '',
  password: ''
})

const phoneRegex = /^09[0-9]{9}$/

const submit = async () => {
  errorMessage.value = ''
  fieldErrors.phone = ''
  fieldErrors.password = ''

  if (!phoneRegex.test(form.phone.trim())) {
    fieldErrors.phone = 'شماره موبایل باید در قالب 09xxxxxxxxx باشد.'
    return
  }

  if (!form.password || form.password.length < 6) {
    fieldErrors.password = 'رمز عبور باید حداقل 6 کاراکتر باشد.'
    return
  }

  try {
    loading.value = true
    await adminStore.login({
      phone: form.phone.trim(),
      password: form.password
    })
    router.push(route.query.redirect || { name: 'admin-dashboard' })
  } catch (error) {
    if (error?.status === 422 && error?.errors) {
      fieldErrors.phone = error.errors?.phone?.[0] || ''
      fieldErrors.password = error.errors?.password?.[0] || ''
      errorMessage.value = error.message
      return
    }

    if (error?.status === 401) {
      errorMessage.value = 'شماره موبایل یا رمز عبور نامعتبر است.'
      return
    }

    if (error?.status === 403) {
      errorMessage.value = 'حساب مدیر غیرفعال است یا دسترسی لازم را ندارد.'
      return
    }

    errorMessage.value = error?.message || 'خطا در ورود. لطفا دوباره تلاش کنید.'
  } finally {
    loading.value = false
  }
}
</script>

<template>
  <div class="admin-login-wrapper">
    <Card class="admin-login-card">
      <template #title>
        <div class="text-center">
          <h2 class="m-0">ورود مدیران</h2>
          <small class="text-color-secondary">AYT Admin Console</small>
        </div>
      </template>
      <template #content>
        <div class="grid">
          <div class="col-12">
            <label class="block mb-2 font-semibold">شماره موبایل</label>
            <InputText v-model="form.phone" class="w-full" placeholder="0912xxxxxxx" />
            <small v-if="fieldErrors.phone" class="field-error">{{ fieldErrors.phone }}</small>
          </div>
          <div class="col-12">
            <label class="block mb-2 font-semibold">رمز عبور</label>
            <Password
              v-model="form.password"
              class="w-full"
              inputClass="w-full"
              :feedback="false"
              toggleMask
            />
            <small v-if="fieldErrors.password" class="field-error">{{ fieldErrors.password }}</small>
          </div>
          <div class="col-12">
            <div class="flex align-items-center gap-2">
              <Checkbox v-model="form.remember" binary inputId="remember-admin" />
              <label for="remember-admin">مرا به خاطر بسپار</label>
            </div>
          </div>
          <div v-if="errorMessage" class="col-12">
            <InlineMessage severity="error">{{ errorMessage }}</InlineMessage>
          </div>
          <div class="col-12">
            <Button
              label="ورود به پنل"
              icon="pi pi-sign-in"
              class="w-full"
              :loading="loading"
              @click="submit"
            />
          </div>
        </div>
      </template>
    </Card>
  </div>
</template>

<style scoped>
.admin-login-wrapper {
  min-height: 100vh;
  display: grid;
  place-items: center;
  padding: 1rem;
  background: radial-gradient(circle at top, #dbeafe 0%, #f1f5f9 45%, #f8fafc 100%);
}

.admin-login-card {
  width: min(100%, 430px);
  border-radius: 20px;
  border: 1px solid #dbe4f0;
  box-shadow: 0 24px 50px rgba(15, 23, 42, 0.08);
}

.field-error {
  color: #b91c1c;
  font-size: 0.8rem;
  margin-top: 0.35rem;
  display: inline-block;
}
</style>
