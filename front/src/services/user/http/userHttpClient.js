import axios from 'axios'
import { normalizeUserApiError } from '@/services/user/errors/userApiError'
import { clearUserSession, getUserAccessToken } from '@/services/user/http/userSession'

const trimSlash = (value = '') => value.replace(/\/+$/, '')
const API_ROOT = trimSlash(import.meta.env.API_URL || 'http://localhost:8000/api')
const USER_BASE_PATH = '/v1'
const BASE_URL = `${API_ROOT}${USER_BASE_PATH}`
const DEFAULT_TIMEOUT_MS = 15000

let onUnauthorized = null

export const setUserUnauthorizedHandler = (handler) => {
  onUnauthorized = typeof handler === 'function' ? handler : null
}

export const userHttpClient = axios.create({
  baseURL: BASE_URL,
  timeout: DEFAULT_TIMEOUT_MS
})

userHttpClient.interceptors.request.use((config) => {
  const headers = config.headers || {}
  headers.Accept = 'application/json'

  const token = getUserAccessToken()
  if (token) {
    headers.Authorization = `Bearer ${token}`
  }

  return {
    ...config,
    headers
  }
})

userHttpClient.interceptors.response.use(
  (response) => {
    const payload = response?.data
    if (payload && typeof payload === 'object' && 'data' in payload) {
      return payload.data
    }
    return payload
  },
  (error) => {
    const normalizedError = normalizeUserApiError(error)
    if (normalizedError.status === 401) {
      clearUserSession()
      if (onUnauthorized) {
        onUnauthorized(normalizedError)
      }
    }
    return Promise.reject(normalizedError)
  }
)

export const userApiConfig = {
  apiRoot: API_ROOT,
  baseUrl: BASE_URL,
  timeout: DEFAULT_TIMEOUT_MS
}
