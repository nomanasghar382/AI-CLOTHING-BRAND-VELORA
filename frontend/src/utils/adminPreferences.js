const STORAGE_KEY = 'velora_admin_saved_filters'

export function loadSavedFilters() {
  try {
    return JSON.parse(localStorage.getItem(STORAGE_KEY) || '[]')
  } catch {
    return []
  }
}

export function saveAdminFilter(filter) {
  const current = loadSavedFilters()
  const next = [{ ...filter, id: crypto.randomUUID(), saved_at: new Date().toISOString() }, ...current].slice(0, 8)
  localStorage.setItem(STORAGE_KEY, JSON.stringify(next))
  return next
}
