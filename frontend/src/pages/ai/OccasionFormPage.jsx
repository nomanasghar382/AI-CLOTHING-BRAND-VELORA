import { useState } from 'react'
import { useNavigate } from 'react-router-dom'
import AiPageHeader from '../../components/ai/AiPageHeader'
import Button from '../../components/common/Button'
import Input from '../../components/common/Input'
import useAiStylist from '../../hooks/useAiStylist'

export default function OccasionFormPage() {
  const { createRecommendation } = useAiStylist()
  const [occasion, setOccasion] = useState('Gym session')
  const [notes, setNotes] = useState('')
  const [loading, setLoading] = useState(false)
  const navigate = useNavigate()
  const submit = async (event) => { event.preventDefault(); setLoading(true); try { await createRecommendation({ occasion, notes, type: 'occasion' }); navigate('/ai/history') } finally { setLoading(false) } }
  return <div><AiPageHeader eyebrow="SPORT OCCASION" title="Dress for the grind" copy="Gym, court, run, or street — your AI designer builds a full sport fit with matching kicks." />
    <form className="velora-card p-4" onSubmit={submit}><Input label="What are you dressing for?" value={occasion} onChange={(event) => setOccasion(event.target.value)} required /><label className="form-label text-slate-200" htmlFor="occasion-notes">Any details to consider?</label><textarea id="occasion-notes" className="form-control velora-input mb-4" rows="4" value={notes} onChange={(event) => setNotes(event.target.value)} placeholder="Training type, weather, brand preference, budget…" /><Button type="submit" disabled={loading}>{loading ? 'Designing fit…' : 'Design sport outfit'}</Button></form>
  </div>
}
