import { userHttpClient } from '@/services/user/http'
import { userEndpoints } from '@/services/user/endpoints/userEndpoints'
import { mapUserProfile } from '@/services/user/mappers/authMapper'

export const userProfileApi = {
  async update(payload) {
    const response = await userHttpClient.patch(userEndpoints.profile.update, payload)
    return mapUserProfile(response)
  },

  async uploadAvatar(formData) {
    const response = await userHttpClient.post(userEndpoints.profile.avatar, formData, {
      headers: {
        'Content-Type': 'multipart/form-data'
      }
    })
    return {
      avatarUrl: response?.avatar_url || null
    }
  }
}
