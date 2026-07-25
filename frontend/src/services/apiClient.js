import axios from 'axios'
import { API_BASE_URL, API_TIMEOUT } from '../config/api'

const apiClient = axios.create({
  baseURL: API_BASE_URL,
  timeout: API_TIMEOUT,
  headers: { Accept: 'application/json', 'Content-Type': 'application/json' },
})

apiClient.interceptors.request.use((config) => {
  const token = localStorage.getItem('velora_access_token')
  if (token) config.headers.Authorization = `Bearer ${token}`
  const sessionId = localStorage.getItem('velora_session_id') || crypto.randomUUID()
  localStorage.setItem('velora_session_id', sessionId)
  config.headers['X-Session-Id'] = sessionId
  return config
})

apiClient.interceptors.response.use(
  (response) => response,
  (error) => {
    const status = error.response?.status
    if (status === 401 && !window.location.pathname.includes('/login')) window.location.assign('/unauthorized')
    if (status === 419) window.location.assign('/session-expired')
    if (status === 429) window.location.assign('/rate-limited')
    if (status === 503) window.location.assign('/maintenance')
    return Promise.reject(error)
  },
)

export default apiClient
