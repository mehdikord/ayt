const PERSIAN_ARABIC_DIGITS = '۰۱۲۳۴۵۶۷۸۹٠١٢٣٤٥٦٧٨٩'
const ENGLISH_DIGITS = '01234567890123456789'

/** Persian (۰-۹) and Arabic-Indic (٠-٩) digits → ASCII 0-9 */
export function toEnglishDigits(value) {
  if (value == null || value === '') {
    return ''
  }

  return String(value).replace(/[۰-۹٠-٩]/g, (char) => {
    const index = PERSIAN_ARABIC_DIGITS.indexOf(char)
    return index >= 0 ? ENGLISH_DIGITS[index] : char
  })
}
