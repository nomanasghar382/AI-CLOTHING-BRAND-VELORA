import axios from 'axios'
import { API_BASE_URL, API_TIMEOUT } from '../config/api'

const RETRYABLE_STATUSES = new Set([408, 425, 429, 500, 502, 503, 504])
const MAX_RETRIES = 3

const sleep = (ms) => new Promise((resolve) => window.setTimeout(resolve, ms))

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
  config.__retryCount = config.__retryCount || 0
  return config
})

apiClient.interceptors.response.use(
  (response) => response,
  async (error) => {
    const config = error.config
    const status = error.response?.status
    const isNetworkError = !error.response
    const shouldRetry = config && !config.__skipRetry && (isNetworkError || RETRYABLE_STATUSES.has(status)) && config.__retryCount < MAX_RETRIES

    if (shouldRetry) {
      config.__retryCount += 1
      const delay = 2 ** (config.__retryCount - 1) * 400
      await sleep(delay)
      return apiClient(config)
    }

    if (status === 401 && !window.location.pathname.includes('/login')) window.location.assign('/unauthorized')
    if (status === 419) window.location.assign('/session-expired')
    if (status === 429) window.location.assign('/rate-limited')
    if (status === 503) window.location.assign('/maintenance')
    if (isNetworkError && !navigator.onLine) {
      error.isOffline = true
      error.userMessage = 'You appear to be offline. Changes will sync when connectivity returns.'
    } else if (error.code === 'ECONNABORTED') {
      error.userMessage = 'The request timed out. Please try again.'
    } else if (!error.response) {
      error.userMessage = 'Unable to reach the server. Retrying did not succeed.'
    }

    return Promise.reject(error)
  },
)

export default apiClient
