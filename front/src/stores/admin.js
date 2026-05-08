import { defineStore } from 'pinia'
import { adminMockService } from '@/services/admin/mockService'

const ADMIN_SESSION_KEY = 'ayt_admin_session'

const defaultProfile = {
  name: 'ادمین AYT',
  phone: '09120000000',
  image: null,
  isActive: true,
  lastLoginAt: null
}

const getPersistedSession = () => {
  try {
    const raw = localStorage.getItem(ADMIN_SESSION_KEY)
    if (!raw) {
      return { isLoggedIn: false, profile: { ...defaultProfile } }
    }

    const parsed = JSON.parse(raw)
    return {
      isLoggedIn: Boolean(parsed?.isLoggedIn),
      profile: {
        ...defaultProfile,
        ...(parsed?.profile || {})
      }
    }
  } catch (_error) {
    return { isLoggedIn: false, profile: { ...defaultProfile } }
  }
}

export const useAdminStore = defineStore('admin', {
  state: () => ({
    session: getPersistedSession(),
    ui: {
      sidebarOpen: true,
      theme: 'light'
    },
    dashboardStats: [],
    rows: []
  }),
  actions: {
    persistSession() {
      localStorage.setItem(ADMIN_SESSION_KEY, JSON.stringify(this.session))
    },
    login(payload) {
      this.session.isLoggedIn = true
      this.session.profile = {
        ...defaultProfile,
        ...payload,
        lastLoginAt: new Date().toISOString()
      }
      this.persistSession()
    },
    logout() {
      this.session.isLoggedIn = false
      this.session.profile = { ...defaultProfile }
      this.persistSession()
    },
    updateProfile(payload) {
      this.session.profile = {
        ...this.session.profile,
        ...payload
      }
      this.persistSession()
    },
    bootstrap() {
      this.dashboardStats = adminMockService.dashboardStats()
      this.rows = adminMockService.tableRows()
    }
  }
})
