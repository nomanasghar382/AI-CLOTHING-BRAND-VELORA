import { useContext } from 'react'
import { AiStylistContext } from '../context/aiStylistContext'

export default function useAiStylist() {
  const context = useContext(AiStylistContext)
  if (!context) throw new Error('useAiStylist must be used inside AiStylistProvider')
  return context
}
