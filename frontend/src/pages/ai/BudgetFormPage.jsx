import { useState } from 'react'
import { useNavigate } from 'react-router-dom'
import AiPageHeader from '../../components/ai/AiPageHeader'
import Button from '../../components/common/Button'
import Input from '../../components/common/Input'
import useAiStylist from '../../hooks/useAiStylist'

export default function BudgetFormPage() {
  const { createRecommendation } = useAiStylist()
  const [budget, setBudget] = useState('250')
  const [focus, setFocus] = useState('A versatile everyday look')
  const [loading, setLoading] = useState(false)
  const navigate = useNavigate()
  const submit = async (event) => { event.preventDefault(); setLoading(true); try { await createRecommendation({ occasion: focus, budget: Number(budget), notes: 'Budget-led edit' }); navigate('/ai/history') } finally { setLoading(false) } }
  return <div><AiPageHeader eyebrow="BUDGET EDIT" title="Make room for the right pieces" copy="Define a total budget and what you want it to accomplish." />
    <form className="velora-card p-4" onSubmit={submit}><Input label="Total budget (USD)" type="number" min="1" value={budget} onChange={(event) => setBudget(event.target.value)} required /><Input label="What should this look do?" value={focus} onChange={(event) => setFocus(event.target.value)} required /><Button type="submit" disabled={loading}>{loading ? 'Creating edit…' : 'Create budget edit'}</Button></form>
  </div>
}
