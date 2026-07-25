import { useState } from 'react'
import { Link } from 'react-router-dom'
import CartItemCard from '../../components/shopping/CartItemCard'
import PriceSummary from '../../components/shopping/PriceSummary'
import ShoppingEmptyState from '../../components/shopping/ShoppingEmptyState'
import useCart from '../../hooks/useCart'

export default function CartPage() {
  const { cart, updateItem, removeItem } = useCart()
  const [busy, setBusy] = useState(false)
  const [error, setError] = useState('')
  const perform = async (action) => {
    setBusy(true); setError('')
    try { await action() } catch (requestError) { setError(requestError.response?.data?.message || 'We could not update your bag. Please try again.') } finally { setBusy(false) }
  }

  return <section className="container py-4 py-lg-5">
    <div className="d-flex align-items-end justify-content-between mb-4"><div><p className="eyebrow">YOUR SELECTION</p><h1 className="h2 mb-0">Shopping bag</h1></div><Link className="nav-link-velora" to="/catalog">Continue shopping</Link></div>
    {error && <div className="alert alert-danger">{error}</div>}
    {!cart.items.length ? <ShoppingEmptyState type="cart" /> : <div className="row g-4"><div className="col-lg-8 d-grid gap-3">{cart.items.map((item) => <CartItemCard key={item.id} item={item} currency={cart.currency} busy={busy} onQuantityChange={(id, quantity) => perform(() => updateItem(id, quantity))} onRemove={(id) => perform(() => removeItem(id))} />)}</div><div className="col-lg-4"><PriceSummary subtotal={cart.subtotal} currency={cart.currency}><Link className="btn btn-velora-primary w-100" to="/checkout">Proceed to checkout</Link></PriceSummary></div></div>}
  </section>
}
