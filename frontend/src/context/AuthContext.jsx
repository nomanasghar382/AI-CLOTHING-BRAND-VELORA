import { createContext, useCallback, useContext, useEffect, useMemo, useState } from 'react'
import { authService } from '../services/authService'

const AuthContext = createContext(null)

export function AuthProvider({ children }) {
  const [user, setUser] = useState(null)
  const [isLoading, setIsLoading] = useState(true)

  const clearSession = useCallback(() => {
    localStorage.removeItem('velora_access_token')
    setUser(null)
  }, [])

  const refreshProfile = useCallback(async () => {
    try {
      const { data } = await authService.profile()
      setUser(data.data)
    } catch {
      clearSession()
    } finally {
      setIsLoading(false)
    }
  }, [clearSession])

  useEffect(() => {
    if (localStorage.getItem('velora_access_token')) refreshProfile()
    else setIsLoading(false)
  }, [refreshProfile])

  const signIn = useCallback(async (credentials) => {
    const { data } = await authService.login(credentials)
    localStorage.setItem('velora_access_token', data.data.token)
    setUser(data.data.user)
    return data
  }, [])

  const signOut = useCallback(async () => {
    try {
      await authService.logout()
    } finally {
      clearSession()
    }
  }, [clearSession])

  const value = useMemo(() => ({
    user, isLoading, signIn, signOut, refreshProfile, isAuthenticated: Boolean(user),
  }), [user, isLoading, signIn, signOut, refreshProfile])

  return <AuthContext.Provider value={value}>{children}</AuthContext.Provider>
}

export function useAuth() {
  const context = useContext(AuthContext)
  if (!context) throw new Error('useAuth must be used within AuthProvider.')
  return context
}
