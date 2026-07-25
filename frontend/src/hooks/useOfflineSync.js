import { useEffect } from 'react'
import useCart from './useCart'
import useWishlist from './useWishlist'
import { cacheCartSnapshot, cacheWishlistSnapshot } from '../utils/offlineStore'

export default function useOfflineSync() {
  const { cart } = useCart()
  const { wishlist } = useWishlist()

  useEffect(() => {
    if (cart?.items?.length) cacheCartSnapshot(cart.items)
  }, [cart])

  useEffect(() => {
    if (wishlist?.items?.length) cacheWishlistSnapshot(wishlist.items)
  }, [wishlist])

  useEffect(() => {
    const onOnline = () => window.dispatchEvent(new CustomEvent('velora:online'))
    const onOffline = () => window.dispatchEvent(new CustomEvent('velora:offline'))
    window.addEventListener('online', onOnline)
    window.addEventListener('offline', onOffline)
    return () => {
      window.removeEventListener('online', onOnline)
      window.removeEventListener('offline', onOffline)
    }
  }, [])
}
