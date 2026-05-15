const DEFAULT_ERROR_MESSAGE = 'خطایی در ارتباط با سرور رخ داد.'

const STATUS_MESSAGE_MAP = {
  401: 'نشست شما منقضی شده است. لطفا دوباره وارد شوید.',
  403: 'شما دسترسی لازم برای این عملیات را ندارید.',
  404: 'مورد درخواستی یافت نشد.',
  409: 'به دلیل تعارض داده، عملیات قابل انجام نیست.',
  422: 'برخی از فیلدها معتبر نیستند. لطفا فرم را بررسی کنید.',
  429: 'تعداد تلاش بیش از حد مجاز است. کمی بعد دوباره تلاش کنید.'
}

export class UserApiError extends Error {
  constructor({
    status = null,
    message = DEFAULT_ERROR_MESSAGE,
    code = null,
    errors = null,
    meta = null,
    original = null
  } = {}) {
    super(message)
    this.name = 'UserApiError'
    this.status = status
    this.code = code
    this.errors = errors
    this.meta = meta
    this.original = original
  }
}

export const normalizeUserApiError = (error) => {
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

  return new UserApiError({
    status,
    message,
    code: payload?.code ?? null,
    errors: payload?.errors ?? null,
    meta: payload?.meta ?? null,
    original: error
  })
}
