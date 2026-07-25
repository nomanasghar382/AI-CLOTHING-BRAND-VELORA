import { useCallback, useEffect, useState } from 'react'
import { Link } from 'react-router-dom'
import Loader from '../../components/feedback/Loader'
import EmptyState from '../../components/feedback/EmptyState'
import ErrorState from '../../components/feedback/ErrorState'
import { formatMoney } from '../../utils/format'
import { orderService } from '../../services/orderService'

export default function OrdersPage() {
  const [orders, setOrders] = useState(null)
  const [failed, setFailed] = useState(false)
  const load = useCallback(() => {
    setFailed(false)
    setOrders(null)
    orderService.list()
      .then(({ data }) => setOrders(data.data?.data || data.data || []))
      .catch(() => setFailed(true))
  }, [])

  useEffect(() => { load() }, [load])

  return <section className="container py-4 py-lg-5"><div className="mb-4"><p className="eyebrow">YOUR VELORA HISTORY</p><h1 className="h2 mb-0">Orders</h1></div>
    {failed ? <ErrorState title="We could not load your orders." onRetry={load} /> : !orders ? <Loader label="Gathering your orders..." /> : !orders.length ? <EmptyState title="No orders yet." message="Your completed orders will appear here." /> : <div className="d-grid gap-3">{orders.map((order) => <Link className="velora-card p-3 order-row" to={`/orders/${order.id}`} key={order.id}><div><p className="eyebrow mb-1">{new Date(order.created_at).toLocaleDateString()}</p><h2 className="h5 mb-0">{order.number}</h2></div><div className="text-lg-end"><span className="status-pill">{order.status}</span><strong className="d-block mt-2">{formatMoney(order.grand_total, order.currency)}</strong></div></Link>)}</div>}
  </section>
}
