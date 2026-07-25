import apiClient from './apiClient'

export const catalogService = {
  products: (params) => apiClient.get('/products', { params }),
  product: (slug) => apiClient.get(`/products/${slug}`),
  categories: () => apiClient.get('/categories'),
  filters: () => apiClient.get('/catalog/filters'),
}
