import apiClient from './apiClient'

export const loyaltyService = {
  overview: () => apiClient.get('/loyalty/overview'),
  rewards: () => apiClient.get('/loyalty/rewards'),
  redeem: (rewardId) => apiClient.post(`/loyalty/rewards/${rewardId}/redeem`),
  referrals: () => apiClient.get('/loyalty/referral-code'),
  createReferral: () => apiClient.post('/loyalty/referrals'),
  wallet: () => apiClient.get('/loyalty/wallet'),
  giftCards: () => apiClient.get('/loyalty/gift-cards'),
  redeemGiftCard: (code) => apiClient.post('/loyalty/gift-cards/redeem', { code }),
  achievements: () => apiClient.get('/loyalty/achievements'),
  notifications: () => apiClient.get('/notifications'),
  markNotificationRead: (id) => apiClient.patch(`/notifications/${id}`, { read: true }),
  createAlert: (payload) => apiClient.post('/loyalty/alerts', payload),
  alerts: () => apiClient.get('/loyalty/alerts'),
}
