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

const phoneRegex = /^09[0-9]{9}$/

const submit = async () => {
  errorMessage.value = ''

  if (!phoneRegex.test(form.phone.trim())) {
    errorMessage.value = 'شماره موبایل باید در قالب 09xxxxxxxxx باشد.'
    return
  }

  if (!form.password || form.password.length < 6) {
    errorMessage.value = 'رمز عبور باید حداقل 6 کاراکتر باشد.'
    return
  }

  loading.value = true
  await new Promise((resolve) => setTimeout(resolve, 600))

  adminStore.login({
    phone: form.phone.trim(),
    name: 'مدیر سیستم AYT'
  })

  loading.value = false
  router.push(route.query.redirect || { name: 'admin-dashboard' })
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
</style>
