import { Link } from 'react-router-dom'
import CartItemCard from '../../components/shopping/CartItemCard'
import PriceSummary from '../../components/shopping/PriceSummary'
import ShoppingEmptyState from '../../components/shopping/ShoppingEmptyState'
import Loader from '../../components/feedback/Loader'
import useAsyncAction from '../../hooks/useAsyncAction'
import useCart from '../../hooks/useCart'

export default function CartPage() {
  const { cart, isLoading, updateItem, removeItem } = useCart()
  const { busy, error, run } = useAsyncAction('We could not update your bag. Please try again.')

  return <section className="container py-4 py-lg-5">
    <div className="d-flex align-items-end justify-content-between mb-4"><div><p className="eyebrow">YOUR SELECTION</p><h1 className="h2 mb-0">Shopping bag</h1></div><Link className="nav-link-velora" to="/catalog">Continue shopping</Link></div>
    {error && <div className="alert alert-danger" role="alert">{error}</div>}
    {isLoading ? <Loader label="Loading your bag..." /> : !cart.items.length ? <ShoppingEmptyState type="cart" /> : <div className="row g-4"><div className="col-lg-8 d-grid gap-3">{cart.items.map((item) => <CartItemCard key={item.id} item={item} currency={cart.currency} busy={busy} onQuantityChange={(id, quantity) => run(() => updateItem(id, quantity))} onRemove={(id) => run(() => removeItem(id))} />)}</div><div className="col-lg-4"><PriceSummary subtotal={cart.subtotal} currency={cart.currency}><Link className="btn btn-velora-primary w-100" to="/checkout">Proceed to checkout</Link></PriceSummary></div></div>}
  </section>
}
