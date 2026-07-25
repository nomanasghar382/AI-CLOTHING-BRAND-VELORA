/** Extract the Velora Icon handle from seeded product keywords. */
export function iconHandleFromProduct(product) {
  const raw = product?.keywords || ''
  const match = raw.match(/@[\w.]+/)
  return match?.[0] || null
}

export function iconNameFromProduct(product) {
  const name = product?.name || ''
  const [iconName] = name.split('×')
  return iconName?.trim() || null
}
