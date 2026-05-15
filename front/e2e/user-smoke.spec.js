import { test, expect } from '@playwright/test'

const apiRoot = process.env.E2E_API_URL || 'http://localhost:8000/api'
const apiBase = `${apiRoot.replace(/\/$/, '')}/v1`

const randomMobile = () => {
  const suffix = `${Date.now()}`.slice(-7)
  return `09${suffix.padStart(9, '1')}`
}

test.describe('User app smoke', () => {
  test('API health reachable', async ({ request }) => {
    const res = await request.get(`${apiBase}/health`, {
      headers: { Accept: 'application/json' }
    })
    expect(res.ok()).toBeTruthy()
    const json = await res.json()
    expect(json.success).toBe(true)
  })

  test('menu and about endpoints are reachable', async ({ request }) => {
    const categoriesRes = await request.get(`${apiBase}/menu/categories`, {
      headers: { Accept: 'application/json' }
    })
    expect(categoriesRes.ok()).toBeTruthy()

    const aboutRes = await request.get(`${apiBase}/pages/about`, {
      headers: { Accept: 'application/json' }
    })
    expect(aboutRes.ok()).toBeTruthy()
  })

  test('otp flow creates session and can call protected endpoint', async ({ request }) => {
    const mobile = randomMobile()

    const requestOtpRes = await request.post(`${apiBase}/auth/request-otp`, {
      headers: { Accept: 'application/json' },
      data: { mobile }
    })
    expect(requestOtpRes.ok()).toBeTruthy()
    const requestOtpBody = await requestOtpRes.json()
    expect(requestOtpBody.success).toBe(true)
    expect(requestOtpBody.data?.otp_session_id).toBeTruthy()

    const verifyOtpRes = await request.post(`${apiBase}/auth/verify-otp`, {
      headers: { Accept: 'application/json' },
      data: {
        mobile,
        otp_code: '123456',
        otp_session_id: requestOtpBody.data.otp_session_id,
        device_name: 'playwright-smoke'
      }
    })
    expect(verifyOtpRes.ok()).toBeTruthy()
    const verifyOtpBody = await verifyOtpRes.json()
    expect(verifyOtpBody.success).toBe(true)
    const accessToken = verifyOtpBody.data?.access_token
    expect(accessToken).toBeTruthy()

    const meRes = await request.get(`${apiBase}/auth/me`, {
      headers: {
        Accept: 'application/json',
        Authorization: `Bearer ${accessToken}`
      }
    })
    expect(meRes.ok()).toBeTruthy()
    const meBody = await meRes.json()
    expect(meBody.success).toBe(true)
    expect(meBody.data?.mobile).toBe(mobile)
  })

  test('profile endpoint without token returns 401', async ({ request }) => {
    const res = await request.patch(`${apiBase}/profile`, {
      headers: { Accept: 'application/json' },
      data: { name: 'تست کاربر' }
    })
    expect(res.status()).toBe(401)
  })
})
