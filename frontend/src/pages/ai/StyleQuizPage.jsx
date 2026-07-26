import { useState } from 'react'
import { useNavigate } from 'react-router-dom'
import AiPageHeader from '../../components/ai/AiPageHeader'
import Button from '../../components/common/Button'
import useAiStylist from '../../hooks/useAiStylist'

const options = {
  style: ['Athletic', 'Oversized', 'Compression', 'Minimal'],
  palette: ['Blackout', 'Neutral', 'Volt accent', 'Earth tones'],
  priority: ['Lift performance', 'Post-gym look', 'Both equally'],
}

export default function StyleQuizPage() {
  const { profile, updateProfile } = useAiStylist()
  const [answers, setAnswers] = useState({
    style: profile.style || 'Athletic',
    palette: profile.palette || 'Blackout',
    priority: profile.priority || 'Both equally',
  })
  const navigate = useNavigate()
  const submit = async (event) => {
    event.preventDefault()
    await updateProfile(answers)
    navigate('/ai/occasion')
  }

  return (
    <div>
      <AiPageHeader eyebrow="GYM-TO-STREET PROFILE" title="How do you dress for the grind?" copy="Three choices so every fit recommendation is accurate to how you actually train and walk out." />
      <form className="velora-card p-4" onSubmit={submit}>
        {Object.entries(options).map(([name, values]) => (
          <fieldset className="mb-4" key={name}>
            <legend className="h6 text-capitalize">{name === 'priority' ? 'What matters most?' : `Your ${name}`}</legend>
            <div className="row g-2">
              {values.map((value) => (
                <div className="col-sm-6" key={value}>
                  <label className={`ai-choice ${answers[name] === value ? 'selected' : ''}`}>
                    <input className="visually-hidden" type="radio" name={name} value={value} checked={answers[name] === value} onChange={(event) => setAnswers({ ...answers, [name]: event.target.value })} />
                    {value}
                  </label>
                </div>
              ))}
            </div>
          </fieldset>
        ))}
        <Button type="submit">Save &amp; build my fit</Button>
      </form>
    </div>
  )
}
