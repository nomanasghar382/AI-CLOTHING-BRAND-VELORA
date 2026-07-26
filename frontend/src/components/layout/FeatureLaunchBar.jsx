import { Link, useLocation } from 'react-router-dom'
import { NICHE_TAGLINE } from '../../constants/gymToStreet'

const links = [
  { to: '/catalog?gender=men', label: 'Shop' },
  { to: '/ai/occasion', label: 'Build my fit' },
  { to: '/avatar', label: 'Try on' },
]

export default function FeatureLaunchBar() {
  const location = useLocation()
  if (location.pathname === '/') return null

  return (
    <div className="feature-launch-bar">
      <div className="container d-flex flex-wrap gap-2 py-2 align-items-center justify-content-between">
        <p className="small mb-0 text-slate-300">VELORA · {NICHE_TAGLINE} only</p>
        <div className="d-flex flex-wrap gap-2">
          {links.map((link) => (
            <Link key={link.label} className="feature-launch-link" to={link.to}>{link.label}</Link>
          ))}
        </div>
      </div>
    </div>
  )
}
