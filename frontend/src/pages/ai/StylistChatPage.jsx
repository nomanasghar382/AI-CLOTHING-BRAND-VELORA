import { useState } from 'react'
import { FiSend } from 'react-icons/fi'
import AiPageHeader from '../../components/ai/AiPageHeader'
import Button from '../../components/common/Button'
import useAiStylist from '../../hooks/useAiStylist'
import { aiStylistService } from '../../services/aiStylistService'

export default function StylistChatPage() {
  const { profile } = useAiStylist()
  const [message, setMessage] = useState('')
  const [messages, setMessages] = useState([{ role: 'stylist', content: 'What would you like to get dressed for today?' }])
  const [loading, setLoading] = useState(false)
  const [fallback, setFallback] = useState(false)
  const send = async (event) => {
    event.preventDefault()
    const prompt = message.trim()
    if (!prompt) return
    setMessages((current) => [...current, { role: 'you', content: prompt }]); setMessage(''); setLoading(true)
    try {
      const response = await aiStylistService.sendMessage(prompt, profile)
      setFallback(response.fallback); setMessages((current) => [...current, { role: 'stylist', content: response.message }])
    } catch { setMessages((current) => [...current, { role: 'stylist', content: 'I could not reach the styling service. Please try again shortly.' }]) } finally { setLoading(false) }
  }
  return <div><AiPageHeader eyebrow="PRIVATE STYLING SESSION" title="Ask your Velora stylist" copy="Use this space for ideas, proportions, and finishing details." />
    <div className="velora-card chat-panel"><div className="chat-messages">{messages.map((item, index) => <div className={`chat-message ${item.role === 'you' ? 'from-user' : ''}`} key={`${item.role}-${index}`}><span>{item.role === 'you' ? 'You' : 'Velora stylist'}</span><p className="mb-0">{item.content}</p></div>)}{loading && <div className="chat-message"><span>Velora stylist</span><p className="mb-0 text-slate-300">Considering the details…</p></div>}</div>
      {fallback && <div className="ai-server-notice mx-3 mb-3" role="status">The AI service is unavailable, so this response uses Velora’s catalog fallback.</div>}
      <form className="p-3 border-top border-secondary-subtle d-flex gap-2" onSubmit={send}><input className="form-control velora-input" value={message} onChange={(event) => setMessage(event.target.value)} placeholder="e.g. How can I elevate a navy suit?" aria-label="Message your stylist" /><Button type="submit" disabled={loading}><FiSend /> Send</Button></form>
    </div>
  </div>
}
