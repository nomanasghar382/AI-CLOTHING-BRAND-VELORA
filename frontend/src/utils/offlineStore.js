const PREFIX = 'velora.offline.'

export function readOffline(key, fallback = null) {
  try {
    const raw = localStorage.getItem(`${PREFIX}${key}`)
    return raw ? JSON.parse(raw) : fallback
  } catch {
    return fallback
  }
}

export function writeOffline(key, value) {
  try {
    localStorage.setItem(`${PREFIX}${key}`, JSON.stringify(value))
  } catch {
    // Ignore quota errors in private browsing.
  }
}

export function cacheCartSnapshot(items = []) {
  writeOffline('cart', { items, cached_at: new Date().toISOString() })
}

export function cacheWishlistSnapshot(items = []) {
  writeOffline('wishlist', { items, cached_at: new Date().toISOString() })
}

export function cacheOrdersSnapshot(orders = []) {
  writeOffline('orders', { orders, cached_at: new Date().toISOString() })
}

export function cacheAiHistorySnapshot(history = []) {
  writeOffline('ai-history', { history, cached_at: new Date().toISOString() })
}

export function readCachedCart() {
  return readOffline('cart', { items: [] })
}

export function readCachedWishlist() {
  return readOffline('wishlist', { items: [] })
}

export function readCachedOrders() {
  return readOffline('orders', { orders: [] })
}

export function readCachedAiHistory() {
  return readOffline('ai-history', { history: [] })
}

export function readSavedFilters() {
  return readOffline('saved-filters', [])
}

export function saveFilterPreset(preset) {
  const current = readSavedFilters()
  const next = [{ id: crypto.randomUUID(), saved_at: new Date().toISOString(), ...preset }, ...current].slice(0, 8)
  writeOffline('saved-filters', next)
  return next
}
