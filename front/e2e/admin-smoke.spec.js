import { test, expect } from '@playwright/test'

const adminPhone = process.env.E2E_ADMIN_PHONE
const adminPassword = process.env.E2E_ADMIN_PASSWORD
const apiRoot = process.env.E2E_API_URL || 'http://localhost:8000/api'

const apiBase = `${apiRoot.replace(/\/$/, '')}/v1/admin`

test.describe('Admin panel smoke', () => {
  test('API health reachable', async ({ request }) => {
    test.skip(!apiRoot, 'E2E_API_URL not set')
    const res = await request.get(`${apiBase}/health`, {
      headers: { Accept: 'application/json' }
    })
    expect(res.ok()).toBeTruthy()
    const json = await res.json()
    expect(json.success).toBe(true)
  })

  test('API GET /auth/me without bearer returns 401', async ({ request }) => {
    test.skip(!apiRoot, 'E2E_API_URL not set')
    const res = await request.get(`${apiBase}/auth/me`, {
      headers: { Accept: 'application/json' }
    })
    expect(res.status()).toBe(401)
    const json = await res.json()
    expect(json.success).toBe(false)
  })

  test('API POST /auth/login with invalid validation returns 422', async ({
    request
  }) => {
    test.skip(!apiRoot, 'E2E_API_URL not set')
    const res = await request.post(`${apiBase}/auth/login`, {
      headers: { Accept: 'application/json' },
      data: {
        phone: '123not-a-phone',
        password: 'x'
      }
    })
    expect(res.status()).toBe(422)
    const json = await res.json()
    expect(json.success).toBe(false)
  })

  test('login shows error for invalid credentials', async ({ page }) => {
    await page.goto('/admins/login')
    await page.getByPlaceholder('0912xxxxxxx').fill('09120000000')
    await page.locator('input[type="password"]').first().fill('wrongxx1')
    await page.getByRole('button', { name: 'ورود به پنل' }).click()
    await expect(page.getByText(/نامعتبر|Invalid|رمز|شماره/i).first()).toBeVisible({ timeout: 15_000 })
  })

  test('happy path: login and see dashboard', async ({ page }) => {
    test.skip(!adminPhone || !adminPassword, 'Set E2E_ADMIN_PHONE and E2E_ADMIN_PASSWORD')

    await page.goto('/admins/login')
    await page.getByPlaceholder('0912xxxxxxx').fill(adminPhone)
    const pwd = page.locator('input[type="password"]').first()
    await pwd.fill(adminPassword)
    await page.getByRole('button', { name: 'ورود به پنل' }).click()

    await expect(page).toHaveURL(/\/admins\/?$/i, { timeout: 20_000 })
    await expect(page.getByText('دسته بندی ها').first()).toBeVisible({ timeout: 15_000 })
  })
})
