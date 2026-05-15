import { userHttpClient } from '@/services/user/http'
import { userEndpoints } from '@/services/user/endpoints/userEndpoints'
import { mapMenuCategory, mapMenuItem } from '@/services/user/mappers/menuMapper'

export const userMenuApi = {
  async categories() {
    const response = await userHttpClient.get(userEndpoints.menu.categories)
    return Array.isArray(response) ? response.map(mapMenuCategory) : []
  },

  async categoryItems(categoryId) {
    const response = await userHttpClient.get(userEndpoints.menu.categoryItems(categoryId))
    return Array.isArray(response) ? response.map(mapMenuItem) : []
  },

  async itemDetail(itemId) {
    const response = await userHttpClient.get(userEndpoints.menu.itemDetail(itemId))
    return mapMenuItem(response)
  }
}
