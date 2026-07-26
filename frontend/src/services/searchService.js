import apiClient from './apiClient'

export const searchService = {
  search: (params) => apiClient.get('/search', { params }),
  suggestions: (q) => apiClient.get('/search/suggestions', { params: { q } }),
  visual: (image) => {
    const form = new FormData()
    form.append('image', image)
    return apiClient.post('/search/visual', form, { headers: { 'Content-Type': 'multipart/form-data' } })
  },
}
