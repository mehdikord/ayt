import { userHttpClient } from '@/services/user/http'
import { userEndpoints } from '@/services/user/endpoints/userEndpoints'
import { mapRequestOtpResponse, mapUserProfile, mapVerifyOtpResponse } from '@/services/user/mappers/authMapper'
import { toEnglishDigits } from '@/utils/digits'

const normalizeAuthPayload = (payload = {}) => {
  const normalized = { ...payload }

  if (payload.mobile != null) {
    normalized.mobile = toEnglishDigits(payload.mobile).trim()
  }

  if (payload.otp_code != null) {
    normalized.otp_code = toEnglishDigits(payload.otp_code).trim()
  }

  return normalized
}

export const userAuthApi = {
  async requestOtp(payload) {
    const response = await userHttpClient.post(
      userEndpoints.auth.requestOtp,
      normalizeAuthPayload(payload)
    )
    return mapRequestOtpResponse(response)
  },

  async verifyOtp(payload) {
    const response = await userHttpClient.post(
      userEndpoints.auth.verifyOtp,
      normalizeAuthPayload(payload)
    )
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
