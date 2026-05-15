const toNumber = (value, fallback = 0) => {
  const casted = Number(value)
  return Number.isFinite(casted) ? casted : fallback
}

export const mapMenuCategory = (category = {}) => ({
  id: category?.id ?? null,
  name: category?.name || '',
  slug: category?.slug || '',
  imageUrl: category?.image_url || null,
  isActive: Boolean(category?.is_active ?? true),
  sortOrder: toNumber(category?.sort_order)
})

export const mapMenuVariant = (variant = {}) => ({
  id: variant?.id ?? null,
  name: variant?.name || '',
  description: variant?.description || '',
  price: toNumber(variant?.price),
  discountPrice: variant?.discount_price != null ? toNumber(variant?.discount_price) : null,
  finalPrice: toNumber(variant?.final_price),
  isActive: Boolean(variant?.is_active ?? true),
  sortOrder: toNumber(variant?.sort_order)
})

export const mapMenuItem = (item = {}) => ({
  id: item?.id ?? null,
  categoryId: item?.category_id ?? null,
  name: item?.name || '',
  slug: item?.slug || '',
  description: item?.description || '',
  imageUrl: item?.image_url || null,
  isActive: Boolean(item?.is_active ?? true),
  sortOrder: toNumber(item?.sort_order),
  variants: Array.isArray(item?.variants) ? item.variants.map(mapMenuVariant) : []
})

export const mapDiscount = (discount = {}) => ({
  id: discount?.id ?? null,
  title: discount?.title || '',
  code: discount?.code || '',
  discountType: discount?.discount_type || '',
  discountValue: toNumber(discount?.discount_value),
  expiresAt: discount?.expires_at || null,
  status: discount?.status || 'expired',
  isActive: Boolean(discount?.is_active ?? false)
})

export const mapStaticPage = (page = {}) => ({
  id: page?.id ?? null,
  pageKey: page?.page_key || '',
  title: page?.title || '',
  content: page?.content || '',
  isActive: Boolean(page?.is_active ?? false)
})
