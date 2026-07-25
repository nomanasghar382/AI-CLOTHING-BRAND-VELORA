import { Link } from 'react-router-dom'
import { FiArrowUpRight, FiMessageCircle, FiStar } from 'react-icons/fi'
import Card from '../../components/common/Card'
import useAiStylist from '../../hooks/useAiStylist'

export default function AiWelcomePage() {
  const { profile, history } = useAiStylist()
  const styledBefore = Boolean(profile.style)
  return <div className="ai-welcome">
    <Card className="p-4 p-lg-5 ai-welcome-hero">
      <p className="eyebrow">A MORE PERSONAL WAY TO GET DRESSED</p>
      <h2 className="display-ai mb-3">Meet your <em>Velora stylist.</em></h2>
      <p className="hero-copy">Build a private style profile, then explore measured recommendations for the occasions that matter to you.</p>
      <div className="d-flex flex-wrap gap-2">
        <Link className="btn btn-velora-primary" to={styledBefore ? '/ai/chat' : '/ai/quiz'}>{styledBefore ? 'Talk to my stylist' : 'Begin style quiz'} <FiArrowUpRight /></Link>
        <Link className="btn btn-velora-secondary" to="/ai/occasion">Plan an occasion</Link>
      </div>
    </Card>
    <div className="row g-3 mt-1">
      <div className="col-md-6"><Card className="p-4 h-100"><FiStar className="feature-icon" /><h3 className="h5 mt-3">Personal, never prescriptive</h3><p className="text-slate-300 mb-0">{styledBefore ? `Your ${profile.style} preferences guide every edit.` : 'Tell us what feels like you, and we will start from there.'}</p></Card></div>
      <div className="col-md-6"><Card className="p-4 h-100"><FiMessageCircle className="feature-icon" /><h3 className="h5 mt-3">{history.length ? `${history.length} edit${history.length === 1 ? '' : 's'} in your archive` : 'Ready when you are'}</h3><p className="text-slate-300 mb-0">Ask for a look, define your budget, or save ideas for later.</p></Card></div>
    </div>
  </div>
}
