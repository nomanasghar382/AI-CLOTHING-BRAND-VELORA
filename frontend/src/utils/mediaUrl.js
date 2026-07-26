import { API_BASE_URL } from '../config/api'

const API_ORIGIN = API_BASE_URL.replace(/\/api\/v1\/?$/, '')

export function resolveMediaUrl(url) {
  if (!url) return url
  if (url.startsWith('http://') || url.startsWith('https://') || url.startsWith('data:')) return url
  if (url.startsWith('/')) return `${API_ORIGIN}${url}`
  return `${API_ORIGIN}/${url}`
}
