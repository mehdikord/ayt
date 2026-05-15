import { adminHttpClient } from '@/services/admin/http'
import { adminEndpoints } from '@/services/admin/endpoints/adminEndpoints'
import { mapLoginResponse, mapMeResponse } from '@/services/admin/mappers/authMapper'

export const adminAuthApi = {
  async login(payload) {
    const response = await adminHttpClient.post(adminEndpoints.auth.login, payload)
    return mapLoginResponse(response)
  },

  async me() {
    const response = await adminHttpClient.get(adminEndpoints.auth.me)
    return mapMeResponse(response)
  },

  async logout() {
    await adminHttpClient.post(adminEndpoints.auth.logout)
  },

  async health() {
    return adminHttpClient.get(adminEndpoints.health)
  }
}
