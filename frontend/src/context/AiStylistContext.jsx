import { useCallback, useEffect, useMemo, useState } from 'react'
import { aiStylistService } from '../services/aiStylistService'
import { AiStylistContext } from './aiStylistContext'

const storageKey = 'velora_ai_stylist'
const initialState = { profile: {}, savedOutfits: [], history: [] }

const createFallbackOutfit = ({ occasion = 'Leg day', budget = '$120', profile = {} }) => ({
  id: crypto.randomUUID(),
  title: `${occasion} gym-to-street fit`,
  occasion,
  budget,
  description: `Built for ${profile.style || 'athletic'} training → street. Compression or tee base, post-gym layer, matching kicks under ${budget}.`,
  pieces: ['Training base layer', 'Post-gym hoodie or joggers', 'Matching street sneakers'],
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
