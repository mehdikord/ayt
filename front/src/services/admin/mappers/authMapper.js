const EMPTY_ADMIN = {
  id: null,
  name: 'ادمین AYT',
  phone: '',
  image: null,
  isActive: true,
  lastLoginAt: null
}

export const mapAdminProfile = (admin = {}) => ({
  ...EMPTY_ADMIN,
  id: admin?.id ?? null,
  name: admin?.name || EMPTY_ADMIN.name,
  phone: admin?.phone || '',
  image: admin?.image || admin?.avatar || null,
  isActive: Boolean(admin?.is_active ?? admin?.isActive ?? true),
  lastLoginAt: admin?.last_login_at || admin?.lastLoginAt || null
})

export const mapLoginResponse = (payload = {}) => {
  const token = payload?.access_token || payload?.accessToken || null
  const expiresAt = payload?.expires_at || payload?.expiresAt || null
  const admin = mapAdminProfile(payload?.admin || {})

  return {
    accessToken: token,
    expiresAt,
    admin
  }
}

export const mapMeResponse = (payload = {}) => {
  const admin = payload?.admin || payload
  return mapAdminProfile(admin)
}
