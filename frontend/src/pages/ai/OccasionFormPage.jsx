import { useState } from 'react'
import { useNavigate } from 'react-router-dom'
import AiPageHeader from '../../components/ai/AiPageHeader'
import Button from '../../components/common/Button'
import useAiStylist from '../../hooks/useAiStylist'
import { GYM_MOMENTS } from '../../constants/gymToStreet'

export default function OccasionFormPage() {
  const { createRecommendation } = useAiStylist()
  const [moment, setMoment] = useState(GYM_MOMENTS[0].id)
  const [budget, setBudget] = useState('120')
  const [notes, setNotes] = useState('')
  const [loading, setLoading] = useState(false)
  const navigate = useNavigate()
  const selected = GYM_MOMENTS.find((item) => item.id === moment)

  const submit = async (event) => {
    event.preventDefault()
    setLoading(true)
    try {
      await createRecommendation({
        occasion: selected?.label || 'Gym-to-street',
        notes: [selected?.copy, notes, `Budget around $${budget}`].filter(Boolean).join('. '),
        budget: Number(budget) || undefined,
        type: 'occasion',
      })
      navigate('/ai/history')
    } finally {
      setLoading(false)
    }
  }

  return (
    <div>
      <AiPageHeader eyebrow="BUILD YOUR FIT" title="What’s the move today?" copy="Pick your gym moment. We’ll return one accurate gym-to-street look with matching kicks." />
      <form className="velora-card p-4" onSubmit={submit}>
        <fieldset className="mb-4">
          <legend className="form-label">Gym moment</legend>
          <div className="row g-2">
            {GYM_MOMENTS.map((item) => (
              <div className="col-md-4" key={item.id}>
                <label className={`ai-choice w-100 ${moment === item.id ? 'selected' : ''}`}>
                  <input className="visually-hidden" type="radio" name="moment" value={item.id} checked={moment === item.id} onChange={() => setMoment(item.id)} />
                  <strong className="d-block">{item.label}</strong>
                  <small className="text-slate-300">{item.copy}</small>
                </label>
              </div>
            ))}
          </div>
        </fieldset>
        <label className="form-label" htmlFor="fit-budget">Budget (USD)</label>
        <input id="fit-budget" type="number" className="form-control velora-input mb-3" min="40" step="10" value={budget} onChange={(event) => setBudget(event.target.value)} />
        <label className="form-label text-slate-200" htmlFor="fit-notes">Brand or fit notes (optional)</label>
        <textarea id="fit-notes" className="form-control velora-input mb-4" rows="3" value={notes} onChange={(event) => setNotes(event.target.value)} placeholder="e.g. Nike only, oversized hoodie, size M tops…" />
        <Button type="submit" disabled={loading}>{loading ? 'Building accurate fit…' : 'Build gym-to-street fit'}</Button>
      </form>
    </div>
  )
}
