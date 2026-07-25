import { createContext, useCallback, useContext, useMemo, useRef, useState } from 'react'

const NotificationContext = createContext(null)

let toastId = 0

export function NotificationProvider({ children }) {
  const [toasts, setToasts] = useState([])
  const timers = useRef(new Map())

  const dismissToast = useCallback((id) => {
    setToasts((current) => current.filter((toast) => toast.id !== id))
    const timer = timers.current.get(id)
    if (timer) {
      clearTimeout(timer)
      timers.current.delete(id)
    }
  }, [])

  const pushToast = useCallback(({ message, variant = 'info', duration = 4200 }) => {
    const id = ++toastId
    setToasts((current) => [...current.slice(-4), { id, message, variant }])
    const timer = setTimeout(() => dismissToast(id), duration)
    timers.current.set(id, timer)
    return id
  }, [dismissToast])

  const value = useMemo(() => ({ pushToast, dismissToast, toasts }), [pushToast, dismissToast, toasts])

  return <NotificationContext.Provider value={value}>{children}</NotificationContext.Provider>
}

export function useNotifications() {
  const context = useContext(NotificationContext)
  if (!context) throw new Error('useNotifications must be used within NotificationProvider')
  return context
}
