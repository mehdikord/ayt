import { defineConfig, devices } from '@playwright/test'

const baseURL = process.env.E2E_BASE_URL || 'http://localhost:5173'

export default defineConfig({
  testDir: './e2e',
  fullyParallel: true,
  forbidOnly: !!process.env.CI,
  retries: process.env.CI ? 1 : 0,
  reporter: [['list'], ['html', { open: 'never' }]],
  use: {
    baseURL,
    trace: 'on-first-retry',
    locale: 'fa-IR',
    ...(process.env.PW_USE_DOWNLOADED_BROWSER
      ? {}
      : { channel: 'chrome' })
  },
  projects: [
    {
      name: 'chromium',
      use: process.env.PW_USE_DOWNLOADED_BROWSER
        ? { ...devices['Desktop Chrome'] }
        : { channel: 'chrome', viewport: { width: 1280, height: 720 } }
    }
  ],
  webServer: process.env.E2E_NO_WEBSERVER
    ? undefined
    : {
        command: 'pnpm run dev -- --host 127.0.0.1 --port 5173',
        url: baseURL,
        reuseExistingServer: !process.env.CI,
        timeout: 120_000
      }
})
