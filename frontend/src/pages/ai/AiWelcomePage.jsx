import { Link } from 'react-router-dom'
import { FiArrowUpRight } from 'react-icons/fi'
import Card from '../../components/common/Card'
import useAiStylist from '../../hooks/useAiStylist'
import { NICHE_PROMISE, NICHE_TAGLINE } from '../../constants/gymToStreet'

export default function AiWelcomePage() {
  const { profile, history } = useAiStylist()
  const styledBefore = Boolean(profile.style)
  return <div className="ai-welcome">
    <Card className="p-4 p-lg-5 ai-welcome-hero">
      <p className="eyebrow">GYM-TO-STREET DESIGNER</p>
      <h2 className="display-ai mb-3">Your <em>fit architect.</em></h2>
      <p className="hero-copy">{NICHE_PROMISE} Tell us your grind — we build training gear + matching street kicks in one accurate look.</p>
      <div className="d-flex flex-wrap gap-2">
        <Link className="btn btn-velora-primary" to="/ai/occasion">Build my fit <FiArrowUpRight /></Link>
        <Link className="btn btn-velora-secondary" to={styledBefore ? '/ai/chat' : '/ai/quiz'}>{styledBefore ? 'Refine my profile' : 'Set my gym style'}</Link>
        <Link className="btn btn-velora-secondary" to="/avatar">Try on avatar</Link>
      </div>
    </Card>
    <div className="row g-3 mt-1">
      <div className="col-md-6"><Card className="p-4 h-100"><h3 className="h5">Leg day → post-gym street</h3><p className="text-slate-300 mb-0">{styledBefore ? `Your ${profile.style} fit preference shapes every recommendation.` : 'We only recommend gym-to-street pieces — never random sport catalog filler.'}</p></Card></div>
      <div className="col-md-6"><Card className="p-4 h-100"><h3 className="h5">{history.length ? `${history.length} saved fit${history.length === 1 ? '' : 's'}` : `${NICHE_TAGLINE} ready`}</h3><p className="text-slate-300 mb-0">Every look includes why it works: lift, layer, leave the gym looking right.</p></Card></div>
    </div>
  </div>
}
