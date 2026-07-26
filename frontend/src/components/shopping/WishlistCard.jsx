import { Link } from 'react-router-dom'
import { FiShoppingBag, FiTrash2 } from 'react-icons/fi'

export default function WishlistCard({ item, onMoveToCart, onRemove, busy }) {
  return <article className="velora-card wishlist-card p-3 h-100 d-flex flex-column">
    <p className="eyebrow mb-2">SAVED PIECE</p>
    <h2 className="h5"><Link className="text-white" to={`/catalog/${item.slug}`}>{item.name}</Link></h2>
    <p className="small text-slate-300">Saved {new Date(item.added_at).toLocaleDateString()}</p>
    <div className="d-flex gap-2 mt-auto">
      <button className="btn btn-velora-primary flex-grow-1" type="button" disabled={busy} onClick={() => onMoveToCart(item)}><FiShoppingBag /> Add to bag</button>
      <button className="btn btn-velora-secondary" type="button" aria-label={`Remove ${item.name} from wishlist`} disabled={busy} onClick={() => onRemove(item.id)}><FiTrash2 /></button>
    </div>
  </article>
}
