import { adminHttpClient } from '@/services/admin/http'

export const adminDiscountsApi = {
  list(params = {}) {
    return adminHttpClient.get('/discounts', { params })
  },
  create(payload) {
    return adminHttpClient.post('/discounts', payload)
  },
  update(id, payload) {
    return adminHttpClient.patch(`/discounts/${id}`, payload)
  },
  delete(id) {
    return adminHttpClient.delete(`/discounts/${id}`)
  }
}
