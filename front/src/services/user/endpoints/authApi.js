import { userHttpClient } from '@/services/user/http'
import { userEndpoints } from '@/services/user/endpoints/userEndpoints'
import { mapRequestOtpResponse, mapUserProfile, mapVerifyOtpResponse } from '@/services/user/mappers/authMapper'

export const userAuthApi = {
  async requestOtp(payload) {
    const response = await userHttpClient.post(userEndpoints.auth.requestOtp, payload)
    return mapRequestOtpResponse(response)
  },

  async verifyOtp(payload) {
    const response = await userHttpClient.post(userEndpoints.auth.verifyOtp, payload)
    return mapVerifyOtpResponse(response)
  },

  async me() {
    const response = await userHttpClient.get(userEndpoints.auth.me)
    return mapUserProfile(response)
  },

  async logout() {
    await userHttpClient.post(userEndpoints.auth.logout)
  }
}
