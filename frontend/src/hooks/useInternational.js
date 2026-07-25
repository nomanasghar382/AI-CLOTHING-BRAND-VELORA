import { useContext } from 'react'
import { InternationalContext } from '../context/internationalContext'

export default function useInternational() {
  const context = useContext(InternationalContext)
  if (!context) throw new Error('useInternational must be used within InternationalProvider')
  return context
}
