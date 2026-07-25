import { useState } from 'react'
import WishlistCard from '../../components/shopping/WishlistCard'
import ShoppingEmptyState from '../../components/shopping/ShoppingEmptyState'
import useCart from '../../hooks/useCart'
import useWishlist from '../../hooks/useWishlist'

export default function WishlistPage() {
  const { wishlist, removeItem } = useWishlist()
  const { addItem } = useCart()
  const [busyId, setBusyId] = useState(null)
  const [error, setError] = useState('')
  const perform = async (id, action) => {
    setBusyId(id); setError('')
    try { await action() } catch (requestError) { setError(requestError.response?.data?.message || 'We could not update your wishlist. Please try again.') } finally { setBusyId(null) }
  }

  return <section className="container py-4 py-lg-5">
    <div className="mb-4"><p className="eyebrow">CURATED FOR LATER</p><h1 className="h2 mb-0">Wishlist</h1></div>
    {error && <div className="alert alert-danger">{error}</div>}
    {!wishlist.items.length ? <ShoppingEmptyState type="wishlist" /> : <div className="row g-3">{wishlist.items.map((item) => <div className="col-sm-6 col-lg-4" key={item.id}><WishlistCard item={item} busy={busyId === item.id} onRemove={(id) => perform(id, () => removeItem(id))} onMoveToCart={(saved) => perform(saved.id, async () => { await addItem({ product_id: saved.product_id, product_variant_id: saved.product_variant_id, quantity: 1 }); await removeItem(saved.id) })} /></div>)}</div>}
  </section>
}
