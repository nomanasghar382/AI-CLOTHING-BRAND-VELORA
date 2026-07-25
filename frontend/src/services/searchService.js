import apiClient from './apiClient'

export const searchService = {
  search: (params) => apiClient.get('/search', { params }),
  suggestions: (q) => apiClient.get('/search/suggestions', { params: { q } }),
}
