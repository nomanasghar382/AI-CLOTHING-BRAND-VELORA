import apiClient from './apiClient'

// These calls follow the authenticated API client convention. Screens retain a
// useful empty state until the loyalty API is enabled for an environment.
export const loyaltyService = {
  overview: () => apiClient.get('/loyalty/overview'),
  rewards: () => apiClient.get('/loyalty/rewards'),
  redeem: (rewardId) => apiClient.post(`/loyalty/rewards/${rewardId}/redeem`),
  referrals: () => apiClient.get('/loyalty/referrals'),
  createReferral: () => apiClient.post('/loyalty/referrals'),
  wallet: () => apiClient.get('/wallet'),
  giftCards: () => apiClient.get('/gift-cards'),
  redeemGiftCard: (code) => apiClient.post('/gift-cards/redeem', { code }),
  achievements: () => apiClient.get('/loyalty/achievements'),
  notifications: () => apiClient.get('/notifications'),
  markNotificationRead: (id) => apiClient.patch(`/notifications/${id}`, { read: true }),
  createAlert: (payload) => apiClient.post('/product-alerts', payload),
  alerts: () => apiClient.get('/product-alerts'),
}
