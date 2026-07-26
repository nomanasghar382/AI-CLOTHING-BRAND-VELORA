import { API_BASE_URL } from '../config/api'

const API_ORIGIN = API_BASE_URL.replace(/\/api\/v1\/?$/, '')

/** Frontend public assets (Vite /public) — never prefix with API host. */
const FRONTEND_STATIC_PREFIXES = ['/catalog/', '/favicon', '/assets/']

export function resolveMediaUrl(url) {
  if (!url) return url
  if (url.startsWith('http://') || url.startsWith('https://') || url.startsWith('data:')) return url
  if (FRONTEND_STATIC_PREFIXES.some((prefix) => url.startsWith(prefix))) return url
  if (url.startsWith('/')) return `${API_ORIGIN}${url}`
  return `${API_ORIGIN}/${url}`
}
