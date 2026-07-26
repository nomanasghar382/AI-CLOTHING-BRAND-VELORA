import { Link } from 'react-router-dom'
import { FiArrowUpRight, FiMessageCircle, FiStar } from 'react-icons/fi'
import Card from '../../components/common/Card'
import useAiStylist from '../../hooks/useAiStylist'

export default function AiWelcomePage() {
  const { profile, history } = useAiStylist()
  const styledBefore = Boolean(profile.style)
  return <div className="ai-welcome">
    <Card className="p-4 p-lg-5 ai-welcome-hero">
      <p className="eyebrow">AI FASHION DESIGNER</p>
      <h2 className="display-ai mb-3">Your <em>sport stylist.</em></h2>
      <p className="hero-copy">Get full men&apos;s sport outfits with matching Nike, Adidas &amp; Puma kicks — tailored to your training, budget, and vibe.</p>
      <div className="d-flex flex-wrap gap-2">
        <Link className="btn btn-velora-primary" to={styledBefore ? '/ai/chat' : '/ai/quiz'}>{styledBefore ? 'Design my next fit' : 'Start sport style quiz'} <FiArrowUpRight /></Link>
        <Link className="btn btn-velora-secondary" to="/ai/occasion">Plan gym / game day</Link>
        <Link className="btn btn-velora-secondary" to="/avatar">Try on avatar</Link>
      </div>
    </Card>
    <div className="row g-3 mt-1">
      <div className="col-md-6"><Card className="p-4 h-100"><FiStar className="feature-icon" /><h3 className="h5 mt-3">Sport-first recommendations</h3><p className="text-slate-300 mb-0">{styledBefore ? `Your ${profile.style} sport preferences guide every outfit + matching sneakers.` : 'Tell us your sport, fit, and brands — we build full looks with kicks included.'}</p></Card></div>
      <div className="col-md-6"><Card className="p-4 h-100"><FiMessageCircle className="feature-icon" /><h3 className="h5 mt-3">{history.length ? `${history.length} fit${history.length === 1 ? '' : 's'} in your archive` : 'Ready when you are'}</h3><p className="text-slate-300 mb-0">Ask for gym fits, court heat, or streetwear drops — voice, text, or occasion-based.</p></Card></div>
    </div>
  </div>
}
