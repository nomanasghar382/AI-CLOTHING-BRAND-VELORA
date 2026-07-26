import { Link } from 'react-router-dom'
import { FiEdit3, FiUser } from 'react-icons/fi'
import AiPageHeader from '../../components/ai/AiPageHeader'
import Card from '../../components/common/Card'
import useAiStylist from '../../hooks/useAiStylist'

export default function StyleProfilePage() {
  const { profile } = useAiStylist()
  const entries = [['Signature', profile.style], ['Palette', profile.palette], ['Priority', profile.priority]]
  return <div><AiPageHeader eyebrow="MY STYLE PROFILE" title="A quiet picture of your style" copy="This profile stays in your browser and shapes future AI recommendations." />
    {!profile.style ? <Card className="p-4 text-center"><FiUser className="feature-icon" /><h3 className="h5 mt-3">Your profile is waiting</h3><p className="text-slate-300">Answer a few questions and your stylist can begin with a clearer point of view.</p><Link className="btn btn-velora-primary" to="/ai/quiz">Take the style quiz</Link></Card> : <Card className="p-4"><div className="row g-3">{entries.map(([label, value]) => <div className="col-md-4" key={label}><p className="eyebrow mb-1">{label}</p><p className="h5 mb-0">{value}</p></div>)}</div><Link className="btn btn-velora-secondary mt-4" to="/ai/quiz"><FiEdit3 /> Refine profile</Link></Card>}
  </div>
}
