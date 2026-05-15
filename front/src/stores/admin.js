import { defineStore } from 'pinia'
import { adminAuthApi } from '@/services/admin/endpoints/authApi'
import { adminDashboardApi } from '@/services/admin/endpoints/dashboardApi'
import { adminProfileApi } from '@/services/admin/endpoints/profileApi'
import { mapDashboardSummary } from '@/services/admin/mappers/dashboardMapper'
import { mapAdminProfile } from '@/services/admin/mappers/authMapper'

const ADMIN_SESSION_KEY = 'ayt_admin_session'

const defaultProfile = {
  id: null,
  name: 'ادمین AYT',
  phone: '',
  image: null,
  isActive: true,
  lastLoginAt: null
}

const getPersistedSession = () => {
  try {
    const raw = localStorage.getItem(ADMIN_SESSION_KEY)
    if (!raw) {
      return { isLoggedIn: false, accessToken: null, profile: { ...defaultProfile } }
    }

    const parsed = JSON.parse(raw)
    return {
      isLoggedIn: Boolean(parsed?.isLoggedIn),
      accessToken: parsed?.accessToken || null,
      expiresAt: parsed?.expiresAt || null,
      profile: {
        ...defaultProfile,
        ...(parsed?.profile || {})
      }
    }
  } catch (_error) {
    return { isLoggedIn: false, accessToken: null, profile: { ...defaultProfile } }
  }
}

export const useAdminStore = defineStore('admin', {
  state: () => ({
    session: getPersistedSession(),
    auth: {
      hydrated: false,
      isBootstrapping: false
    },
    ui: {
      sidebarOpen: true,
      theme: 'light'
    },
    dashboardStats: [],
    rows: [],
    dashboardState: {
      isLoading: false,
      error: null,
      lastUpdatedAt: null
    }
  }),
  getters: {
    hasToken: (state) => Boolean(state.session.accessToken),
    isTokenExpired: (state) => {
      if (!state.session.expiresAt) return false
      return new Date(state.session.expiresAt).getTime() <= Date.now()
    },
    canAccessAdmin() {
      return this.session.isLoggedIn && this.hasToken && !this.isTokenExpired
    }
  },
  actions: {
    persistSession() {
      localStorage.setItem(ADMIN_SESSION_KEY, JSON.stringify(this.session))
    },
    setSession(payload = {}) {
      this.session.isLoggedIn = true
      this.session.accessToken = payload?.accessToken || null
      this.session.expiresAt = payload?.expiresAt || null
      this.session.profile = {
        ...defaultProfile,
        ...(payload?.profile || {})
      }
      this.persistSession()
    },
    clearSession() {
      this.session.isLoggedIn = false
      this.session.accessToken = null
      this.session.expiresAt = null
      this.session.profile = { ...defaultProfile }
      this.auth.hydrated = false
      this.persistSession()
    },
    async login(payload) {
      const session = await adminAuthApi.login(payload)
      this.setSession({
        accessToken: session.accessToken,
        expiresAt: session.expiresAt,
        profile: {
          ...session.admin,
          lastLoginAt: new Date().toISOString()
        }
      })
      this.auth.hydrated = true
      return session
    },
    async hydrateSession() {
      if (!this.hasToken || this.isTokenExpired) {
        this.clearSession()
        return false
      }

      this.auth.isBootstrapping = true
      try {
        const profile = await adminAuthApi.me()
        if (!profile?.isActive) {
          this.clearSession()
          return false
        }

        this.session.isLoggedIn = true
        this.session.profile = {
          ...this.session.profile,
          ...profile
        }
        this.auth.hydrated = true
        this.persistSession()
        return true
      } catch (_error) {
        this.clearSession()
        return false
      } finally {
        this.auth.isBootstrapping = false
      }
    },
    async ensureSession() {
      if (this.canAccessAdmin && this.auth.hydrated) {
        return true
      }
      return this.hydrateSession()
    },
    async logout() {
      try {
        if (this.hasToken) {
          await adminAuthApi.logout()
        }
      } catch (_error) {
        // Local cleanup must always happen even if API logout fails.
      } finally {
        this.clearSession()
      }
    },
    updateProfile(payload) {
      this.session.profile = {
        ...this.session.profile,
        ...payload
      }
      this.persistSession()
    },
    async patchProfileRemote({ name }) {
      const data = await adminProfileApi.update({ name })
      const merged = mapAdminProfile({
        id: data?.id ?? this.session.profile.id,
        name: data?.name ?? name,
        phone: data?.phone ?? this.session.profile.phone,
        image: data?.image ?? this.session.profile.image,
        is_active: this.session.profile.isActive,
        last_login_at: this.session.profile.lastLoginAt
      })
      this.session.profile = merged
      this.persistSession()
      return merged
    },
    bootstrap() {
      return this.loadDashboardSummary()
    },
    async loadDashboardSummary() {
      this.dashboardState.isLoading = true
      this.dashboardState.error = null
      try {
        const payload = await adminDashboardApi.summary()
        const statsPayload = [
          { title: 'دسته بندی ها', value: payload?.menu_categories_count, icon: 'pi pi-list' },
          { title: 'آیتم های منو', value: payload?.menu_items_count, icon: 'pi pi-box' },
          { title: 'کاربران', value: payload?.users_count, icon: 'pi pi-users' },
          { title: 'تخفیف های فعال', value: payload?.active_discounts_count, icon: 'pi pi-percentage' }
        ]
        this.dashboardStats = mapDashboardSummary({ stats: statsPayload })
        this.rows = this.dashboardStats
        this.dashboardState.lastUpdatedAt = new Date().toISOString()
        return true
      } catch (error) {
        this.dashboardState.error = error
        this.dashboardStats = []
        this.rows = []
        return false
      } finally {
        this.dashboardState.isLoading = false
      }
    }
  }
})
