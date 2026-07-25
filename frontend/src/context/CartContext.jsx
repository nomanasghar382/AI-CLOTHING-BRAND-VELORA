import { useCallback, useEffect, useMemo, useState } from 'react'
import useAuth from '../hooks/useAuth'
import { cartService } from '../services/cartService'
import { CartContext } from './cartContext'

const emptyCart = { items: [], subtotal: '0.00', currency: 'USD' }

export function CartProvider({ children }) {
  const { isAuthenticated } = useAuth()
  const [cart, setCart] = useState(emptyCart)
  const [isLoading, setIsLoading] = useState(true)

  const refreshCart = useCallback(async () => {
    if (!isAuthenticated) {
      setCart(emptyCart)
      setIsLoading(false)
      return emptyCart
    }
    setIsLoading(true)
    try {
      const { data } = await cartService.get()
      setCart(data.data)
      return data.data
    } finally {
      setIsLoading(false)
    }
  }, [isAuthenticated])

  useEffect(() => { refreshCart().catch(() => {}) }, [refreshCart])

  const addItem = useCallback(async (item) => {
    const { data } = await cartService.addItem(item)
    setCart(data.data)
    return data.data
  }, [])
  const updateItem = useCallback(async (id, quantity) => {
    const { data } = await cartService.updateItem(id, quantity)
    setCart(data.data)
  }, [])
  const removeItem = useCallback(async (id) => {
    const { data } = await cartService.removeItem(id)
    setCart(data.data)
  }, [])

  const value = useMemo(() => ({
    cart, isLoading, refreshCart, addItem, updateItem, removeItem,
    itemCount: cart.items.reduce((count, item) => count + item.quantity, 0),
  }), [cart, isLoading, refreshCart, addItem, updateItem, removeItem])

  return <CartContext.Provider value={value}>{children}</CartContext.Provider>
}
