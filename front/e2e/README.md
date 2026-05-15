# تست E2E پنل ادمین (Playwright)

## پیش‌نیاز

- بک‌اند لاراول روی `http://localhost:8000` (یا آدرس دلخواه)
- فرانت روی `http://localhost:5173` یا اجرای خودکار توسط Playwright

## متغیرهای محیطی

| متغیر | توضیح |
|--------|--------|
| `E2E_BASE_URL` | آدرس فرانت، پیش‌فرض `http://localhost:5173` |
| `E2E_API_URL` | ریشه API، پیش‌فرض `http://localhost:8000/api` — برای تست health |
| `E2E_ADMIN_PHONE` | موبایل ادمین واقعی |
| `E2E_ADMIN_PASSWORD` | رمز ادمین |
| `E2E_NO_WEBSERVER` | اگر `1` باشد Playwright سرور Vite را بالا نمی‌آورد |
| `PW_USE_DOWNLOADED_BROWSER` | اگر `1` باشد از Chromium دانلودشده Playwright استفاده می‌شود؛ وگرنه از **Google Chrome** نصب‌شده روی سیستم |

## اجرا

```bash
cd front
pnpm test:e2e
```

نصب مرورگر Playwright (در صورت نیاز):

```bash
set PW_USE_DOWNLOADED_BROWSER=1
pnpm exec playwright install chromium
```

## نکته

سناریوی happy path به بک‌اند و کاربر ادمین واقعی وابسته است؛ بدون `E2E_ADMIN_*` آن تست skip می‌شود.
