import { useState } from 'react'
import { useNavigate } from 'react-router-dom'
import Loader from '../../components/feedback/Loader'
import PriceSummary from '../../components/shopping/PriceSummary'
import ShoppingEmptyState from '../../components/shopping/ShoppingEmptyState'
import useCart from '../../hooks/useCart'
import { orderService } from '../../services/orderService'

const fields = [
  ['first_name', 'First name'], ['last_name', 'Last name'], ['phone', 'Phone'], ['line1', 'Address'], ['line2', 'Apartment, suite, etc. (optional)'],
  ['city', 'City'], ['state', 'State / region'], ['postal_code', 'Postal code'], ['country', 'Country code (e.g. US)'],
]

export default function CheckoutPage() {
  const { cart, isLoading, refreshCart } = useCart()
  const navigate = useNavigate()
  const [address, setAddress] = useState({ country: 'US' })
  const [notes, setNotes] = useState('')
  const [saving, setSaving] = useState(false)
  const [error, setError] = useState('')
  const update = (event) => setAddress((current) => ({ ...current, [event.target.name]: event.target.value }))
  const submit = async (event) => {
    event.preventDefault()
    setSaving(true); setError('')
    try {
      const { data } = await orderService.checkout({ shipping_address: address, payment_provider: 'cod', notes })
      await refreshCart()
      navigate(`/orders/${data.data.id}`, { state: { created: true } })
    } catch (requestError) {
      const errors = requestError.response?.data?.errors
      setError(errors ? Object.values(errors).flat().join(' ') : requestError.response?.data?.message || 'We could not place your order. Please try again.')
    } finally { setSaving(false) }
  }

  if (isLoading) return <section className="container py-4 py-lg-5"><Loader label="Preparing checkout..." /></section>
  if (!cart.items.length) return <section className="container py-4 py-lg-5"><ShoppingEmptyState type="cart" /></section>
  return <section className="container py-4 py-lg-5"><div className="mb-4"><p className="eyebrow">FINAL DETAILS</p><h1 className="h2 mb-0">Checkout</h1><p className="text-slate-300 mt-2 mb-0">Orders are placed with payment on delivery. No payment details are collected here.</p></div>
    <div className="row g-4"><div className="col-lg-8"><form className="velora-card p-4" onSubmit={submit}><h2 className="h5 mb-4">Delivery address</h2>{error && <div className="alert alert-danger" role="alert">{error}</div>}<div className="row g-3">{fields.map(([name, label]) => <div className={['line1', 'line2'].includes(name) ? 'col-12' : 'col-md-6'} key={name}><label className="form-label" htmlFor={name}>{label}</label><input className="form-control velora-input" id={name} name={name} value={address[name] || ''} onChange={update} required={!['line2', 'state'].includes(name)} maxLength={name === 'country' ? 2 : undefined} /></div>)}<div className="col-12"><label className="form-label" htmlFor="notes">Order notes (optional)</label><textarea className="form-control velora-input" id="notes" value={notes} onChange={(event) => setNotes(event.target.value)} rows="3" /></div></div><button className="btn btn-velora-primary mt-4" disabled={saving}>{saving ? 'Placing order…' : 'Place order'}</button></form></div><div className="col-lg-4"><PriceSummary subtotal={cart.subtotal} currency={cart.currency} /></div></div>
  </section>
}
