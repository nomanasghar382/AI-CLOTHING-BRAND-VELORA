import { useContext } from 'react'
import { LoyaltyContext } from '../context/loyaltyContext'

export default function useLoyalty() {
  const value = useContext(LoyaltyContext)
  if (!value) throw new Error('useLoyalty must be used within LoyaltyProvider')
  return value
}
