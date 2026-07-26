import apiClient from './apiClient'

export const styleService = {
  getBodyProfile: () => apiClient.get('/style/body'),
  updateBodyProfile: (payload) => apiClient.put('/style/body', payload),
}
