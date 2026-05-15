import { adminHttpClient } from '@/services/admin/http'

export const adminPagesApi = {
  list() {
    return adminHttpClient.get('/pages')
  },
  byId(id) {
    return adminHttpClient.get(`/pages/${id}`)
  },
  byKey(pageKey) {
    return adminHttpClient.get(`/pages/key/${pageKey}`)
  },
  update(id, payload) {
    return adminHttpClient.patch(`/pages/${id}`, payload)
  }
}
