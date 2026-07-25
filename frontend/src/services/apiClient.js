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
  return config
})

export default apiClient
