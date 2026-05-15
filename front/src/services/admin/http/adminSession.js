const ADMIN_SESSION_KEY = 'ayt_admin_session'

const safeParse = (raw) => {
  try {
    return JSON.parse(raw)
  } catch (_error) {
    return null
  }
}

export const getAdminSession = () => {
  const raw = localStorage.getItem(ADMIN_SESSION_KEY)
  if (!raw) {
    return null
  }

  return safeParse(raw)
}

export const getAdminAccessToken = () => {
  const session = getAdminSession()
  return session?.accessToken || null
}

export const clearAdminSession = () => {
  localStorage.removeItem(ADMIN_SESSION_KEY)
}

export const ADMIN_SESSION_STORAGE_KEY = ADMIN_SESSION_KEY
