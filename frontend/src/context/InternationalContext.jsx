import { useCallback, useEffect, useMemo, useState } from 'react'
import { InternationalContext } from './internationalContext'
import { internationalService } from '../services/internationalService'
import { markets } from '../config/markets'

const defaults = { country: 'US', currency: 'USD', language: 'en' }

export function InternationalProvider({ children }) {
  const [preferences, setPreferences] = useState(defaults)
  useEffect(() => { internationalService.loadPreferences().then((saved) => saved && setPreferences({ ...defaults, ...saved })) }, [])
  const updatePreferences = useCallback((next) => {
    const country = next.country || preferences.country
    const market = markets.find((item) => item.country === country)
    const value = { ...preferences, ...next, ...(next.country ? { currency: market.currency, language: market.language } : {}) }
    setPreferences(value)
    internationalService.savePreferences(value)
  }, [preferences])
  const value = useMemo(() => ({ ...preferences, market: markets.find((item) => item.country === preferences.country), updatePreferences }), [preferences, updatePreferences])
  return <InternationalContext.Provider value={value}>{children}</InternationalContext.Provider>
}
