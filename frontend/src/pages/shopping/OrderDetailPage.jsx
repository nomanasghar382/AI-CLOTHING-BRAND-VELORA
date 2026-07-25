import { useEffect, useState } from 'react'
import { Link, useLocation, useParams } from 'react-router-dom'
import Loader from '../../components/feedback/Loader'
import ErrorState from '../../components/feedback/ErrorState'
import PriceSummary from '../../components/shopping/PriceSummary'
import { orderService } from '../../services/orderService'

const money = (value, currency = 'USD') => new Intl.NumberFormat('en-US', { style: 'currency', currency }).format(Number(value))

function Address({ address }) {
  if (!address) return null
  return <address className="text-slate-300 mb-0">{address.first_name} {address.last_name}<br />{address.line1}{address.line2 && <><br />{address.line2}</>}<br />{address.city}{address.state && `, ${address.state}`} {address.postal_code}<br />{address.country}<br />{address.phone}</address>
}

export default function OrderDetailPage() {
  const { id } = useParams()
  const location = useLocation()
  const [order, setOrder] = useState(null)
  const [failed, setFailed] = useState(false)
  useEffect(() => { orderService.get(id).then(({ data }) => setOrder(data.data)).catch(() => setFailed(true)) }, [id])
  if (failed) return <section className="container py-5"><ErrorState title="We could not find that order." /></section>
  if (!order) return <section className="container py-5"><Loader label="Opening your order..." /></section>
  return <section className="container py-4 py-lg-5"><Link className="nav-link-velora" to="/orders">← All orders</Link>{location.state?.created && <div className="alert alert-success mt-3">Your order has been created. We will keep you updated as it progresses.</div>}<div className="d-flex flex-wrap justify-content-between align-items-end gap-3 my-4"><div><p className="eyebrow">ORDER {order.number}</p><h1 className="h2 mb-0">Thank you for choosing Velora.</h1></div><span className="status-pill">{order.status}</span></div><div className="row g-4"><div className="col-lg-8"><div className="velora-card p-4"><h2 className="h5 mb-3">Items</h2>{order.items?.map((item) => <div className="order-line py-3" key={item.id}><div><strong>{item.product_name}</strong><p className="small text-slate-300 mb-0">{item.sku} · Quantity {item.quantity}</p></div><strong>{money(item.line_total, order.currency)}</strong></div>)}</div><div className="velora-card p-4 mt-4"><h2 className="h5">Delivery address</h2><Address address={order.shipping_address} /></div></div><div className="col-lg-4"><PriceSummary subtotal={order.subtotal} discount={order.discount_total} shipping={order.shipping_total} tax={order.tax_total} total={order.grand_total} currency={order.currency} /><div className="velora-card p-4 mt-3"><p className="eyebrow mb-1">PAYMENT</p><p className="mb-0 text-capitalize">{order.payment_status}</p></div></div></div></section>
}
