import apiClient from './apiClient'

export const wishlistService = {
  get: () => apiClient.get('/wishlist'),
  addItem: (item) => apiClient.post('/wishlist/items', item),
  removeItem: (id) => apiClient.delete(`/wishlist/items/${id}`),
}
