import { useCallback, useEffect, useMemo, useState } from 'react'
import useAuth from '../hooks/useAuth'
import { loyaltyService } from '../services/loyaltyService'
import { LoyaltyContext } from './loyaltyContext'

const emptyState = { overview: null, rewards: [], wallet: null, achievements: [], notifications: [], alerts: [] }

const dataOf = (response) => response?.data?.data ?? response?.data ?? null

export function LoyaltyProvider({ children }) {
  const { isAuthenticated } = useAuth()
  const [state, setState] = useState(emptyState)
  const [isLoading, setIsLoading] = useState(false)

  const refresh = useCallback(async () => {
    if (!isAuthenticated) { setState(emptyState); return }
    setIsLoading(true)
    const requests = await Promise.allSettled([
      loyaltyService.overview(), loyaltyService.rewards(), loyaltyService.wallet(),
      loyaltyService.achievements(), loyaltyService.notifications(), loyaltyService.alerts(),
    ])
    const [overview, rewards, wallet, achievements, notifications, alerts] = requests.map((result) => result.status === 'fulfilled' ? dataOf(result.value) : null)
    setState({
      overview,
      rewards: Array.isArray(rewards) ? rewards : rewards?.data || [],
      wallet,
      achievements: Array.isArray(achievements) ? achievements : achievements?.data || [],
      notifications: Array.isArray(notifications) ? notifications : notifications?.data || [],
      alerts: Array.isArray(alerts) ? alerts : alerts?.data || [],
    })
    setIsLoading(false)
  }, [isAuthenticated])

  useEffect(() => { refresh() }, [refresh])

  const addAlert = useCallback(async (payload) => {
    const response = await loyaltyService.createAlert(payload)
    const alert = dataOf(response)
    setState((current) => ({ ...current, alerts: alert ? [alert, ...current.alerts] : current.alerts }))
    return alert
  }, [])
  const markNotificationRead = useCallback(async (id) => {
    await loyaltyService.markNotificationRead(id)
    setState((current) => ({ ...current, notifications: current.notifications.map((notice) => notice.id === id ? { ...notice, read: true } : notice) }))
  }, [])

  const value = useMemo(() => ({ ...state, isLoading, refresh, addAlert, markNotificationRead }), [state, isLoading, refresh, addAlert, markNotificationRead])
  return <LoyaltyContext.Provider value={value}>{children}</LoyaltyContext.Provider>
}
