import { FiBookmark, FiHeart, FiShoppingBag } from 'react-icons/fi'
import { Link } from 'react-router-dom'

export default function LookCard({ look, saved, onSave }) {
  return <article className="look-card">
    <img src={look.image} alt={look.title} />
    <div className="look-card-overlay">
      <div><p className="mb-1 fw-bold">{look.title}</p><small>{look.creator.name} · {look.likes} likes</small></div>
      <div className="d-flex gap-2">
        <button className="icon-action" onClick={() => onSave?.(look.id)} aria-label="Save look"><FiBookmark className={saved ? 'text-info' : ''} /></button>
        <Link className="icon-action" to={`/shop-the-look/${look.id}`} aria-label="Shop this look"><FiShoppingBag /></Link>
      </div>
    </div>
    <div className="look-card-meta"><span><FiHeart /> {look.likes}</span>{look.tags.map((tag) => <span key={tag} className="tag-pill">{tag}</span>)}</div>
  </article>
}
