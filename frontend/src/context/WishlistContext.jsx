import { useCallback, useEffect, useMemo, useState } from 'react'
import useAuth from '../hooks/useAuth'
import { wishlistService } from '../services/wishlistService'
import { WishlistContext } from './wishlistContext'

const emptyWishlist = { items: [] }

export function WishlistProvider({ children }) {
  const { isAuthenticated } = useAuth()
  const [wishlist, setWishlist] = useState(emptyWishlist)
  const [isLoading, setIsLoading] = useState(false)

  const refreshWishlist = useCallback(async () => {
    if (!isAuthenticated) {
      setWishlist(emptyWishlist)
      return emptyWishlist
    }
    setIsLoading(true)
    try {
      const { data } = await wishlistService.get()
      setWishlist(data.data)
      return data.data
    } finally {
      setIsLoading(false)
    }
  }, [isAuthenticated])

  useEffect(() => { refreshWishlist().catch(() => {}) }, [refreshWishlist])

  const addItem = useCallback(async (item) => {
    const { data } = await wishlistService.addItem(item)
    setWishlist(data.data)
    return data.data
  }, [])
  const removeItem = useCallback(async (id) => {
    const { data } = await wishlistService.removeItem(id)
    setWishlist(data.data)
  }, [])

  const value = useMemo(() => ({
    wishlist, isLoading, refreshWishlist, addItem, removeItem, itemCount: wishlist.items.length,
  }), [wishlist, isLoading, refreshWishlist, addItem, removeItem])

  return <WishlistContext.Provider value={value}>{children}</WishlistContext.Provider>
}
