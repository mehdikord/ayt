import { adminHttpClient } from '@/services/admin/http'
import { adminEndpoints } from '@/services/admin/endpoints/adminEndpoints'

export const adminDashboardApi = {
  async summary() {
    return adminHttpClient.get(adminEndpoints.dashboard.summary)
  }
}
