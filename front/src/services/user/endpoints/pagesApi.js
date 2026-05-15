import { userHttpClient } from '@/services/user/http'
import { userEndpoints } from '@/services/user/endpoints/userEndpoints'
import { mapStaticPage } from '@/services/user/mappers/menuMapper'

export const userPagesApi = {
  async about() {
    const response = await userHttpClient.get(userEndpoints.pages.about)
    return mapStaticPage(response)
  }
}
