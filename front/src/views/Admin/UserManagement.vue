<script setup>
import { computed, onMounted, reactive, ref } from 'vue'
import { adminUsersApi } from '@/services/admin/endpoints/usersApi'
import { useAdminFeedback } from '@/composables/useAdminFeedback'

const feedback = useAdminFeedback()

const users = ref([])
const selectedUser = ref(null)
const userDiscounts = ref([])
const isLoading = ref(false)
const isSaving = ref(false)
const errorMessage = ref('')
const perPage = ref(10)
const filters = reactive({
  search: '',
  is_active: null,
  page: 1
})
const pagination = reactive({
  current_page: 1,
  per_page: 10,
  total: 0,
  last_page: 1
})
const profileForm = reactive({
  name: '',
  is_active: true
})
const fieldErrors = reactive({
  name: ''
})

const statusOptions = [
  { label: 'همه', value: null },
  { label: 'فعال', value: true },
  { label: 'غیرفعال', value: false }
]

const hasUsers = computed(() => users.value.length > 0)
const hasPrevPage = computed(() => pagination.current_page > 1)
const hasNextPage = computed(() => pagination.current_page < pagination.last_page)

const mapUserPayload = (payload) => ({
  id: payload.id,
  mobile: payload.mobile,
  name: payload.name,
  avatar_url: payload.avatar_url,
  is_active: payload.is_active,
  last_login_at: payload.last_login_at
})

const loadUsers = async () => {
  isLoading.value = true
  errorMessage.value = ''
  try {
    const response = await adminUsersApi.list({
      search: filters.search || undefined,
      is_active: filters.is_active,
      per_page: perPage.value,
      page: filters.page
    })
    users.value = (response?.items || []).map(mapUserPayload)
    Object.assign(pagination, response?.pagination || {})
    if (!selectedUser.value && users.value[0]) {
      await selectUser(users.value[0].id)
    }
  } catch (error) {
    errorMessage.value = error?.message || 'خطا در دریافت داده های کاربران.'
  } finally {
    isLoading.value = false
  }
}

const selectUser = async (id) => {
  errorMessage.value = ''
  try {
    selectedUser.value = await adminUsersApi.show(id)
    profileForm.name = selectedUser.value?.name || ''
    profileForm.is_active = Boolean(selectedUser.value?.is_active)
    userDiscounts.value = await adminUsersApi.discounts(id)
  } catch (error) {
    errorMessage.value = error?.message || 'خطا در دریافت جزئیات کاربر.'
  }
}

const saveUser = async () => {
  if (!selectedUser.value?.id) return
  fieldErrors.name = ''
  isSaving.value = true
  errorMessage.value = ''
  try {
    const payload = {
      name: profileForm.name,
      is_active: profileForm.is_active
    }
    const updated = await adminUsersApi.update(selectedUser.value.id, payload)
    selectedUser.value = updated
    const rowIndex = users.value.findIndex((u) => u.id === updated.id)
    if (rowIndex >= 0) users.value[rowIndex] = mapUserPayload(updated)
    feedback.success('اطلاعات کاربر ذخیره شد.')
  } catch (error) {
    if (error?.status === 422) {
      fieldErrors.name = error?.errors?.name?.[0] || ''
    }
    errorMessage.value = error?.message || 'ذخیره تغییرات کاربر انجام نشد.'
  } finally {
    isSaving.value = false
  }
}

const goToPage = async (page) => {
  filters.page = page
  await loadUsers()
}

onMounted(loadUsers)
</script>

<template>
  <div class="grid">
    <div class="col-12 xl:col-8">
      <Card>
        <template #title>مدیریت کاربران</template>
        <template #content>
          <div class="flex gap-2 flex-wrap mb-3">
            <InputText v-model="filters.search" class="w-16rem" placeholder="جستجو نام یا موبایل" />
            <Select
              v-model="filters.is_active"
              :options="statusOptions"
              optionLabel="label"
              optionValue="value"
              class="w-12rem"
            />
            <InputNumber v-model="perPage" class="w-8rem" :min="1" :max="100" />
            <Button label="اعمال فیلتر" icon="pi pi-filter" @click="goToPage(1)" />
          </div>

          <div v-if="isLoading" class="p-4 text-center">
            <ProgressSpinner style="width: 42px; height: 42px" strokeWidth="6" />
          </div>
          <Message v-else-if="errorMessage" severity="error" :closable="false">{{ errorMessage }}</Message>
          <Message v-else-if="!hasUsers" severity="secondary" :closable="false">کاربری یافت نشد.</Message>
          <DataTable v-else :value="users" stripedRows responsiveLayout="scroll">
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
                <Button label="مشاهده" size="small" text @click="selectUser(data.id)" />
              </template>
            </Column>
          </DataTable>

          <div class="flex justify-content-between mt-3">
            <Button label="صفحه قبل" text :disabled="!hasPrevPage" @click="goToPage(pagination.current_page - 1)" />
            <small class="text-color-secondary">
              صفحه {{ pagination.current_page }} از {{ pagination.last_page }} - مجموع {{ pagination.total }}
            </small>
            <Button label="صفحه بعد" text :disabled="!hasNextPage" @click="goToPage(pagination.current_page + 1)" />
          </div>
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
          <div class="grid">
            <div class="col-12">
              <label class="block mb-2 font-semibold">نام</label>
              <InputText v-model="profileForm.name" class="w-full" />
              <small v-if="fieldErrors.name" class="text-red-500">{{ fieldErrors.name }}</small>
            </div>
            <div class="col-12 flex align-items-center gap-2">
              <Checkbox v-model="profileForm.is_active" binary inputId="user-active-switch" />
              <label for="user-active-switch">فعال</label>
            </div>
            <div class="col-12">
              <Button label="ذخیره تغییرات" icon="pi pi-save" :loading="isSaving" @click="saveUser" />
            </div>
          </div>

          <Divider />
          <h4 class="mt-0">تخفیف های کاربر</h4>
          <Message v-if="!userDiscounts.length" severity="secondary" :closable="false">تخفیفی ثبت نشده است.</Message>
          <ul v-else class="m-0 pl-3">
            <li v-for="discount in userDiscounts" :key="discount.id" class="mb-2">
              {{ discount.code }} - {{ discount.discount_type === 'percent' ? 'درصدی' : 'مبلغ ثابت' }}
            </li>
          </ul>
        </template>
      </Card>
    </div>
  </div>
</template>
