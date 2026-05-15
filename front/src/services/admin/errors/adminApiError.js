const DEFAULT_ERROR_MESSAGE = 'خطایی در ارتباط با سرور رخ داد.'

const STATUS_MESSAGE_MAP = {
  401: 'نشست مدیریتی شما منقضی شده است. لطفا دوباره وارد شوید.',
  403: 'شما دسترسی لازم برای انجام این عملیات را ندارید.',
  409: 'به دلیل تعارض داده، عملیات قابل انجام نیست.',
  422: 'برخی از فیلدها معتبر نیستند. لطفا فرم را بررسی کنید.'
}

const SERVER_MESSAGE_MAP = {
  'Internal server error.': 'خطای داخلی سرور. لطفا بعداً دوباره امتحان کنید.',
  'Invalid phone or password.': 'شماره موبایل یا رمز عبور نامعتبر است.',
  'Account is disabled.': 'حساب مدیر غیرفعال است.',
  'Cannot delete category while it has menu items.': 'تا وقتی آیتم در این دسته وجود دارد، حذف ممکن نیست.',
  'Cannot delete menu item while it has variants.': 'تا وقتی برای این آیتم زیرمجموعه وجود دارد، حذف ممکن نیست.',
  'Menu item image updated successfully.': 'تصویر آیتم با موفقیت به‌روزرسانی شد.',
  'Menu item image removed successfully.': 'تصویر آیتم حذف شد.',
  'The discount price must be less than or equal to the price.': 'قیمت با تخفیف نباید بیشتر از قیمت پایه باشد.',
  'Percent discount cannot exceed 100.': 'درصد تخفیف نمی‌تواند بیشتر از ۱۰۰ باشد.'
}

const translateServerMessage = (text) => {
  if (!text || typeof text !== 'string') return text
  return SERVER_MESSAGE_MAP[text.trim()] || text
}

export class AdminApiError extends Error {
  constructor({
    status = null,
    message = DEFAULT_ERROR_MESSAGE,
    code = null,
    errors = null,
    meta = null,
    original = null
  } = {}) {
    super(message)
    this.name = 'AdminApiError'
    this.status = status
    this.code = code
    this.errors = errors
    this.meta = meta
    this.original = original
  }
}

export const normalizeAdminApiError = (error) => {
  const status = error?.response?.status ?? null
  const payload = error?.response?.data ?? {}

  let message =
    payload?.message ||
    STATUS_MESSAGE_MAP[status] ||
    error?.message ||
    DEFAULT_ERROR_MESSAGE

  if (!error?.response) {
    if (error?.code === 'ECONNABORTED') {
      message = 'مهلت درخواست به پایان رسید؛ دوباره تلاش کنید.'
    } else {
      message = 'اتصال به سرور برقرار نشد. آدرس API و شبکه را بررسی کنید.'
    }
  }

  message = translateServerMessage(message)

  return new AdminApiError({
    status,
    message,
    code: payload?.code ?? null,
    errors: payload?.errors ?? null,
    meta: payload?.meta ?? null,
    original: error
  })
}
