import apiClient from './apiClient'

const preferencesKey = 'velora_international_preferences'
const safeRead = () => {
  try { return JSON.parse(localStorage.getItem(preferencesKey)) || null } catch { return null }
}

export const internationalService = {
  loadPreferences: () => Promise.resolve(safeRead()),
  savePreferences: (preferences) => {
    localStorage.setItem(preferencesKey, JSON.stringify(preferences))
    return Promise.resolve(preferences)
  },
  countries: () => apiClient.get('/international/countries'),
  currencies: () => apiClient.get('/international/currencies'),
  locale: (payload) => apiClient.post('/international/locale', payload),
  // A single estimate returns shipping, duty, carrier and warehouse data.
  shippingEstimate: (payload) => apiClient.post('/international/estimate', payload),
  dutyEstimate: (payload) => apiClient.post('/international/estimate', payload),
  track: (reference) => apiClient.get(`/international/tracking/${encodeURIComponent(reference)}`),
  availability: (payload) => apiClient.post('/international/estimate', payload),
}
