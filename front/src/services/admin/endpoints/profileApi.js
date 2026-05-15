import { adminHttpClient } from '@/services/admin/http'

export const adminProfileApi = {
  update(payload) {
    return adminHttpClient.patch('/profile', payload)
  }
}
