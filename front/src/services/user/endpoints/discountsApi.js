import { userHttpClient } from '@/services/user/http'
import { userEndpoints } from '@/services/user/endpoints/userEndpoints'
import { mapDiscount } from '@/services/user/mappers/menuMapper'

export const userDiscountsApi = {
  async myDiscounts() {
    const response = await userHttpClient.get(userEndpoints.discounts.my)
    return Array.isArray(response) ? response.map(mapDiscount) : []
  }
}
