<script setup>
import { computed, ref } from 'vue'

const users = ref([
  {
    id: 1,
    mobile: '09121234567',
    name: 'علی رضایی',
    avatar_url: null,
    is_active: true,
    last_login_at: '1405/02/16 11:30'
  },
  {
    id: 2,
    mobile: '09129876543',
    name: 'نگار محمدی',
    avatar_url: null,
    is_active: true,
    last_login_at: '1405/02/16 09:12'
  },
  {
    id: 3,
    mobile: '09351112233',
    name: 'محمد حقی',
    avatar_url: null,
    is_active: false,
    last_login_at: '1405/02/10 18:44'
  }
])

const selectedUserId = ref(users.value[0]?.id || null)
const selectedUser = computed(() => users.value.find((u) => u.id === selectedUserId.value))

const recentActivities = computed(() => {
  if (!selectedUser.value) return []
  return [
    { title: 'ورود به سیستم', date: selectedUser.value.last_login_at },
    { title: 'مشاهده منو', date: '1405/02/16 11:45' },
    { title: 'استفاده از تخفیف', date: '1405/02/15 20:10' }
  ]
})

const uiState = ref('ready')
const stateOptions = [
  { label: 'Ready', value: 'ready' },
  { label: 'Loading', value: 'loading' },
  { label: 'Empty', value: 'empty' },
  { label: 'Error', value: 'error' }
]
</script>

<template>
  <div class="flex justify-content-end mb-3">
    <SelectButton v-model="uiState" :options="stateOptions" optionLabel="label" optionValue="value" />
  </div>
  <div v-if="uiState === 'loading'" class="p-4 text-center">
    <ProgressSpinner style="width: 42px; height: 42px" strokeWidth="6" />
    <p class="text-color-secondary">در حال بارگذاری اطلاعات کاربران...</p>
  </div>
  <Message v-else-if="uiState === 'error'" severity="error" :closable="false">
    خطا در دریافت داده های کاربران.
  </Message>
  <Message v-else-if="uiState === 'empty' || users.length === 0" severity="secondary" :closable="false">
    هنوز کاربری برای نمایش ثبت نشده است.
  </Message>
  <div v-else class="grid">
    <div class="col-12 xl:col-8">
      <Card>
        <template #title>مدیریت کاربران</template>
        <template #content>
          <DataTable :value="users" stripedRows responsiveLayout="scroll">
            <Column field="mobile" header="موبایل" />
            <Column field="name" header="نام" />
            <Column field="last_login_at" header="آخرین ورود" />
            <Column header="وضعیت">
              <template #body="{ data }">
                <Tag :value="data.is_active ? 'فعال' : 'غیرفعال'" :severity="data.is_active ? 'success' : 'danger'" />
              </template>
            </Column>
            <Column header="جزئیات">
              <template #body="{ data }">
                <Button label="مشاهده" size="small" text @click="selectedUserId = data.id" />
              </template>
            </Column>
          </DataTable>
        </template>
      </Card>
    </div>

    <div class="col-12 xl:col-4">
      <Card v-if="selectedUser">
        <template #title>جزئیات کاربر</template>
        <template #content>
          <div class="flex align-items-center gap-2 mb-3">
            <Avatar icon="pi pi-user" shape="circle" />
            <div>
              <div class="font-semibold">{{ selectedUser.name }}</div>
              <small class="text-color-secondary">{{ selectedUser.mobile }}</small>
            </div>
          </div>
          <div class="mb-3">
            <Tag :value="selectedUser.is_active ? 'فعال' : 'غیرفعال'" :severity="selectedUser.is_active ? 'success' : 'danger'" />
          </div>
          <ul class="m-0 pl-3">
            <li v-for="activity in recentActivities" :key="activity.title + activity.date" class="mb-2">
              {{ activity.title }} - {{ activity.date }}
            </li>
          </ul>
        </template>
      </Card>
    </div>
  </div>
</template>
