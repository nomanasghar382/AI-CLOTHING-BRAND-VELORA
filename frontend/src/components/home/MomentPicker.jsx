import { Link } from 'react-router-dom'
import { GYM_MOMENTS } from '../../constants/gymToStreet'

export default function MomentPicker() {
  return (
    <div className="moment-picker">
      <p className="moment-picker-label">WHAT&apos;S THE MOVE?</p>
      <div className="moment-picker-grid">
        {GYM_MOMENTS.map((moment) => (
          <Link key={moment.id} className="moment-card" to={`/catalog?gender=men&moment=${moment.id}`}>
            <span className="moment-card-title">{moment.label}</span>
            <span className="moment-card-copy">{moment.copy}</span>
            <span className="moment-card-cta">Shop this vibe →</span>
          </Link>
        ))}
      </div>
    </div>
  )
}
