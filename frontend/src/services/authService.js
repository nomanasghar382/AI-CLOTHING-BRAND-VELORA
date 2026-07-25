import apiClient from './apiClient'

export const authService = {
  login: (payload) => apiClient.post('/auth/login', payload),
  register: (payload) => apiClient.post('/auth/register', payload),
  profile: () => apiClient.get('/auth/me'),
  logout: () => apiClient.post('/auth/logout'),
}
