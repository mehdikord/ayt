export const userEndpoints = {
  health: '/health',
  auth: {
    requestOtp: '/auth/request-otp',
    verifyOtp: '/auth/verify-otp',
    me: '/auth/me',
    logout: '/auth/logout'
  },
  menu: {
    categories: '/menu/categories',
    categoryItems: (categoryId) => `/menu/categories/${categoryId}/items`,
    itemDetail: (itemId) => `/menu/items/${itemId}`
  },
  profile: {
    update: '/profile',
    avatar: '/profile/avatar'
  },
  discounts: {
    my: '/discounts/my'
  },
  pages: {
    about: '/pages/about'
  }
}
