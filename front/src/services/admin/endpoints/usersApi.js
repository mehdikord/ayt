import { adminHttpClient } from '@/services/admin/http'

export const adminUsersApi = {
  list(params = {}) {
    return adminHttpClient.get('/users', { params })
  },
  show(id) {
    return adminHttpClient.get(`/users/${id}`)
  },
  update(id, payload) {
    return adminHttpClient.patch(`/users/${id}`, payload)
  },
  discounts(userId) {
    return adminHttpClient.get(`/users/${userId}/discounts`)
  }
}
