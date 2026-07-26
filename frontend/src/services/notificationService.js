import apiClient from './apiClient'

export const notificationService = {
  list: () => apiClient.get('/notifications'),
  markRead: (id) => apiClient.patch(`/notifications/${id}`, { read: true }),
  markAllRead: () => apiClient.post('/notifications/read-all'),
}
