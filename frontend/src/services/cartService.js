import apiClient from './apiClient'

export const cartService = {
  get: () => apiClient.get('/cart'),
  addItem: (item) => apiClient.post('/cart/items', item),
  updateItem: (id, quantity) => apiClient.patch(`/cart/items/${id}`, { quantity }),
  removeItem: (id) => apiClient.delete(`/cart/items/${id}`),
}
