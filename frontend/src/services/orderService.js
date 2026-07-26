import apiClient from './apiClient'

export const orderService = {
  checkout: (payload) => apiClient.post('/checkout', payload),
  list: () => apiClient.get('/orders'),
  get: (id) => apiClient.get(`/orders/${id}`),
}
