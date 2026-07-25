import { useCallback, useEffect, useMemo, useState } from 'react'
import { aiStylistService } from '../services/aiStylistService'
import { AiStylistContext } from './aiStylistContext'

const storageKey = 'velora_ai_stylist'
const initialState = { profile: {}, savedOutfits: [], history: [] }

const createFallbackOutfit = ({ occasion = 'Everyday', budget = 'Considered', profile = {} }) => ({
  id: crypto.randomUUID(),
  title: `${occasion} edit`,
  occasion,
  budget,
  description: `A ${profile.style || 'refined'} look with a composed silhouette, tactile layer, and a deliberate finishing detail.`,
  pieces: ['Structured foundation', 'Texture-led layer', 'Refined accessory'],
  fallback: true,
  createdAt: new Date().toISOString(),
})

export function AiStylistProvider({ children }) {
  const [state, setState] = useState(() => {
    try { return JSON.parse(localStorage.getItem(storageKey)) || initialState } catch { return initialState }
  })

  useEffect(() => { localStorage.setItem(storageKey, JSON.stringify(state)) }, [state])

  const updateProfile = useCallback(async (profile) => {
    setState((current) => ({ ...current, profile: { ...current.profile, ...profile } }))
    return aiStylistService.updateProfile(profile)
  }, [])

  const saveOutfit = useCallback(async (outfit) => {
    await aiStylistService.saveOutfit(outfit)
    setState((current) => current.savedOutfits.some((item) => item.id === outfit.id)
      ? current
      : { ...current, savedOutfits: [outfit, ...current.savedOutfits] })
  }, [])

  const removeOutfit = useCallback((id) => {
    setState((current) => ({ ...current, savedOutfits: current.savedOutfits.filter((outfit) => outfit.id !== id) }))
  }, [])

  const createRecommendation = useCallback(async (brief) => {
    const payload = { ...brief, profile: state.profile }
    const response = await aiStylistService.createRecommendation(payload, brief.type)
    const outfit = response.recommendation || createFallbackOutfit(payload)
    const recommendation = { ...outfit, id: outfit.id || crypto.randomUUID(), fallback: response.fallback, createdAt: outfit.createdAt || new Date().toISOString() }
    setState((current) => ({ ...current, history: [recommendation, ...current.history] }))
    return recommendation
  }, [state.profile])

  const value = useMemo(() => ({
    ...state, updateProfile, saveOutfit, removeOutfit, createRecommendation,
  }), [state, updateProfile, saveOutfit, removeOutfit, createRecommendation])

  return <AiStylistContext.Provider value={value}>{children}</AiStylistContext.Provider>
}
