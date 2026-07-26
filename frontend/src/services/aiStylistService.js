import apiClient from './apiClient'

const unavailable = (error) => {
  const status = error?.response?.status
  return !status || [404, 422, 500, 501, 503].includes(status)
}

const fallbackReply = (message, profile = {}) => ({
  fallback: true,
  message: `The AI stylist is not configured on this server yet. Based on your ${profile.style || 'personal'} style, start with a considered silhouette, one focal texture, and a polished finishing layer for “${message}”.`,
})

const normaliseRecommendation = (recommendation, brief) => ({
  id: recommendation.id,
  title: `${brief.occasion || recommendation.kind || 'Sport'} fit`,
  occasion: brief.occasion || 'Sport styling',
  budget: brief.budget ? `$${brief.budget}` : 'Gen Z drop',
  description: recommendation.reply,
  pieces: (recommendation.items || []).map((item) => item.name),
  products: recommendation.items || [],
  productIds: (recommendation.items || []).map((item) => item.product_id),
  fallback: recommendation.provider === 'catalog_fallback',
  createdAt: recommendation.generated_at,
})

export const aiStylistService = {
  async updateProfile(profile) {
    const payload = {
      style_preferences: profile.style ? [profile.style] : [],
      color_preferences: profile.palette ? [profile.palette] : [],
      quiz_answers: profile.priority ? { priority: profile.priority } : {},
    }
    try {
      await apiClient.put('/style/profile', payload)
      return { fallback: false }
    } catch (error) {
      if (unavailable(error)) return { fallback: true }
      throw error
    }
  },

  async createRecommendation(preferences, type = 'general') {
    try {
      const endpoint = type === 'occasion' ? '/style/outfits/occasion' : '/style/recommendations'
      const { data } = await apiClient.post(endpoint, preferences)
      const recommendation = data.data || data
      return { fallback: recommendation.provider === 'catalog_fallback', recommendation: normaliseRecommendation(recommendation, preferences) }
    } catch (error) {
      if (unavailable(error)) return { fallback: true, recommendation: null }
      throw error
    }
  },

  async saveOutfit(outfit) {
    if (!Number.isInteger(outfit.id) || !outfit.productIds?.length) return { fallback: true }
    try {
      await apiClient.post('/style/saved-outfits', {
        name: outfit.title,
        occasion: outfit.occasion,
        notes: outfit.description,
        recommendation_id: outfit.id,
        product_ids: outfit.productIds,
      })
      return { fallback: false }
    } catch (error) {
      if (unavailable(error)) return { fallback: true }
      throw error
    }
  },

  async sendMessage(message, profile) {
    try {
      const { data } = await apiClient.post('/style/chat', { message })
      const recommendation = data.data?.recommendation || data.recommendation
      return { fallback: recommendation?.provider === 'catalog_fallback', message: recommendation?.reply || 'I have prepared a new edit for you.' }
    } catch (error) {
      if (unavailable(error)) return fallbackReply(message, profile)
      throw error
    }
  },
}
