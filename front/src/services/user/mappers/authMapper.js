const EMPTY_USER = {
  id: null,
  name: '',
  mobile: '',
  avatarUrl: null
}

export const mapUserProfile = (user = {}) => ({
  ...EMPTY_USER,
  id: user?.id ?? null,
  name: user?.name || '',
  mobile: user?.mobile || '',
  avatarUrl: user?.avatar_url || user?.avatarUrl || null
})

export const mapRequestOtpResponse = (payload = {}) => ({
  otpSessionId: payload?.otp_session_id ?? payload?.otpSessionId ?? null,
  expiresAt: payload?.expires_at ?? payload?.expiresAt ?? null,
  resendAvailableAt: payload?.resend_available_at ?? payload?.resendAvailableAt ?? null,
  devOtp: payload?.dev_otp ?? null
})

export const mapVerifyOtpResponse = (payload = {}) => ({
  accessToken: payload?.access_token || payload?.accessToken || null,
  tokenType: payload?.token_type || payload?.tokenType || 'Bearer',
  expiresAt: payload?.expires_at || payload?.expiresAt || null,
  user: mapUserProfile(payload?.user || {})
})
