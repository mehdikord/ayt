import axios from 'axios'
import { normalizeAdminApiError } from '@/services/admin/errors/adminApiError'
import { clearAdminSession, getAdminAccessToken } from '@/services/admin/http/adminSession'

const trimSlash = (value = '') => value.replace(/\/+$/, '')
const API_ROOT = trimSlash(import.meta.env.API_URL || 'http://localhost:8000/api')
const ADMIN_BASE_PATH = '/v1/admin'

const BASE_URL = `${API_ROOT}${ADMIN_BASE_PATH}`

const DEFAULT_TIMEOUT_MS = 15000
let onUnauthorized = null

export const setAdminUnauthorizedHandler = (handler) => {
  onUnauthorized = typeof handler === 'function' ? handler : null
}

export const adminHttpClient = axios.create({
  baseURL: BASE_URL,
  timeout: DEFAULT_TIMEOUT_MS
})

adminHttpClient.interceptors.request.use((config) => {
  const headers = config.headers || {}
  headers.Accept = 'application/json'

  const token = getAdminAccessToken()
  if (token) {
    headers.Authorization = `Bearer ${token}`
  }

  return {
    ...config,
    headers
  }
})

adminHttpClient.interceptors.response.use(
  (response) => {
    const payload = response?.data
    if (payload && typeof payload === 'object' && 'data' in payload) {
      return payload.data
    }

    return payload
  },
  (error) => {
    const normalizedError = normalizeAdminApiError(error)

    if (normalizedError.status === 401) {
      clearAdminSession()
      if (onUnauthorized) {
        onUnauthorized(normalizedError)
      }
    }

    return Promise.reject(normalizedError)
  }
)

export const adminApiConfig = {
  apiRoot: API_ROOT,
  baseUrl: BASE_URL,
  timeout: DEFAULT_TIMEOUT_MS
}
