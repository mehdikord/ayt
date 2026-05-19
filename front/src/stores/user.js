import { defineStore } from 'pinia'
import { userAuthApi } from '@/services/user/endpoints/authApi'

const USER_SESSION_KEY = 'ayt_user_session'
const AUTH_FLOW_KEY = 'ayt_auth_flow'

const defaultAuthFlow = () => ({
  step: 'mobile',
  phone: '',
  otpSessionId: null
})

const readAuthFlowFromStorage = () => {
  try {
    const raw = sessionStorage.getItem(AUTH_FLOW_KEY)
    if (!raw) {
      return defaultAuthFlow()
    }

    const parsed = JSON.parse(raw)
    if (parsed?.step === 'otp' && parsed?.phone) {
      return {
        step: 'otp',
        phone: parsed.phone,
        otpSessionId: parsed.otpSessionId ?? null
      }
    }
  } catch (_error) {
    // Ignore invalid persisted auth flow.
  }

  return defaultAuthFlow()
}

const defaultProfile = {
  id: null,
  name: '',
  mobile: '',
  avatarUrl: null
}

const getPersistedSession = () => {
  try {
    const raw = localStorage.getItem(USER_SESSION_KEY)
    if (!raw) {
      return { isLoggedIn: false, accessToken: null, expiresAt: null, profile: { ...defaultProfile } }
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
    return { isLoggedIn: false, accessToken: null, expiresAt: null, profile: { ...defaultProfile } }
  }
}

export const useUserStore = defineStore('user', {
  state: () => ({
    session: getPersistedSession(),
    authFlow: readAuthFlowFromStorage(),
    auth: {
      hydrated: false,
      isBootstrapping: false
    }
  }),
  getters: {
    hasToken: (state) => Boolean(state.session.accessToken),
    isTokenExpired: (state) => {
      if (!state.session.expiresAt) return false
      return new Date(state.session.expiresAt).getTime() <= Date.now()
    },
    canAccessProtectedRoutes() {
      return this.session.isLoggedIn && this.hasToken && !this.isTokenExpired
    }
  },
  actions: {
    persistSession() {
      localStorage.setItem(USER_SESSION_KEY, JSON.stringify(this.session))
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
    persistAuthFlow() {
      if (this.authFlow.step === 'otp' && this.authFlow.phone) {
        sessionStorage.setItem(AUTH_FLOW_KEY, JSON.stringify(this.authFlow))
        return
      }

      sessionStorage.removeItem(AUTH_FLOW_KEY)
    },
    setAuthFlowOtp({ phone, otpSessionId }) {
      this.authFlow = {
        step: 'otp',
        phone,
        otpSessionId: otpSessionId ?? null
      }
      this.persistAuthFlow()
    },
    resetAuthFlowToMobile() {
      this.authFlow = {
        step: 'mobile',
        phone: this.authFlow.phone,
        otpSessionId: null
      }
      sessionStorage.removeItem(AUTH_FLOW_KEY)
    },
    clearAuthFlow() {
      this.authFlow = defaultAuthFlow()
      sessionStorage.removeItem(AUTH_FLOW_KEY)
    },
    setAuthFlowPhone(phone) {
      this.authFlow.phone = phone
      if (this.authFlow.step === 'otp') {
        this.persistAuthFlow()
      }
    },
    async requestOtp(payload) {
      return userAuthApi.requestOtp(payload)
    },
    async verifyOtp(payload) {
      const session = await userAuthApi.verifyOtp(payload)
      this.setSession({
        accessToken: session.accessToken,
        expiresAt: session.expiresAt,
        profile: session.user
      })
      this.auth.hydrated = true
      this.clearAuthFlow()
      return session
    },
    async hydrateSession() {
      if (!this.hasToken || this.isTokenExpired) {
        this.clearSession()
        return false
      }

      this.auth.isBootstrapping = true
      try {
        const profile = await userAuthApi.me()
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
      if (this.canAccessProtectedRoutes && this.auth.hydrated) {
        return true
      }
      return this.hydrateSession()
    },
    async logout() {
      try {
        if (this.hasToken) {
          await userAuthApi.logout()
        }
      } catch (_error) {
        // Session cleanup should happen regardless of API failures.
      } finally {
        this.clearSession()
        this.clearAuthFlow()
      }
    }
  }
})
