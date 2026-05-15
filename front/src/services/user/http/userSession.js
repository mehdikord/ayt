const USER_SESSION_KEY = 'ayt_user_session'

const safeParse = (raw) => {
  try {
    return JSON.parse(raw)
  } catch (_error) {
    return null
  }
}

export const getUserSession = () => {
  const raw = localStorage.getItem(USER_SESSION_KEY)
  if (!raw) {
    return null
  }
  return safeParse(raw)
}

export const getUserAccessToken = () => {
  const session = getUserSession()
  return session?.accessToken || null
}

export const clearUserSession = () => {
  localStorage.removeItem(USER_SESSION_KEY)
}

export const USER_SESSION_STORAGE_KEY = USER_SESSION_KEY
