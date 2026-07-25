import { useState } from 'react'
import { useNavigate } from 'react-router-dom'
import AiPageHeader from '../../components/ai/AiPageHeader'
import Button from '../../components/common/Button'
import useAiStylist from '../../hooks/useAiStylist'

const options = {
  style: ['Minimal', 'Romantic', 'Tailored', 'Expressive'],
  palette: ['Neutrals', 'Earth tones', 'Jewel tones', 'Soft colour'],
  priority: ['Comfort', 'Craft', 'Versatility', 'Statement'],
}

export default function StyleQuizPage() {
  const { profile, updateProfile } = useAiStylist()
  const [answers, setAnswers] = useState({ style: profile.style || 'Minimal', palette: profile.palette || 'Neutrals', priority: profile.priority || 'Comfort' })
  const navigate = useNavigate()
  const submit = async (event) => { event.preventDefault(); await updateProfile(answers); navigate('/ai/profile') }
  return <div><AiPageHeader eyebrow="THE STYLE EDIT / 01" title="Find your visual language" copy="Three small choices help your stylist understand what belongs in your wardrobe." />
    <form className="velora-card p-4" onSubmit={submit}>
      {Object.entries(options).map(([name, values]) => <fieldset className="mb-4" key={name}><legend className="h6 text-capitalize">{name === 'priority' ? 'What matters most?' : `Your ${name}`}</legend><div className="row g-2">{values.map((value) => <div className="col-sm-6" key={value}><label className={`ai-choice ${answers[name] === value ? 'selected' : ''}`}><input className="visually-hidden" type="radio" name={name} value={value} checked={answers[name] === value} onChange={(event) => setAnswers({ ...answers, [name]: event.target.value })} />{value}</label></div>)}</div></fieldset>)}
      <Button type="submit">Save my profile</Button>
    </form>
  </div>
}
