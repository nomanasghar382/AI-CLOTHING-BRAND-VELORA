import { Link, useLocation } from 'react-router-dom'
import { FiCamera, FiMic, FiSearch, FiStar, FiUser } from 'react-icons/fi'

const links = [
  { to: '/catalog?gender=men', label: 'Shop sport', icon: FiSearch },
  { to: '/avatar', label: 'Avatar & try-on', icon: FiUser },
  { to: '/visual-search', label: 'Image search', icon: FiCamera },
  { to: '/catalog?gender=men', label: 'Voice search', icon: FiMic, hint: 'Use mic in catalog search' },
  { to: '/ai', label: 'AI designer', icon: FiStar },
]

export default function FeatureLaunchBar() {
  const location = useLocation()
  if (location.pathname === '/') return null

  return (
    <div className="feature-launch-bar">
      <div className="container d-flex flex-wrap gap-2 py-2 align-items-center justify-content-between">
        <p className="small mb-0 text-slate-300">VELORA Sport features</p>
        <div className="d-flex flex-wrap gap-2">
          {links.map((link) => (
            <Link key={link.label} className="feature-launch-link" to={link.to} title={link.hint}>
              <link.icon aria-hidden="true" /> {link.label}
            </Link>
          ))}
        </div>
      </div>
    </div>
  )
}
