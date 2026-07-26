import { useEffect, useMemo, useState } from 'react'
import Button from '../../components/common/Button'
import Card from '../../components/common/Card'
import Input from '../../components/common/Input'
import Pagination from '../../components/common/Pagination'
import Sidebar from '../../components/layout/Sidebar'
import Loader from '../../components/feedback/Loader'
import useAuth from '../../hooks/useAuth'
import { adminShoppingService as api } from '../../services/adminShoppingService'

const money = (value, currency = 'USD') => new Intl.NumberFormat('en-US', { style: 'currency', currency }).format(Number(value || 0))
const blankCoupon = { code: '', type: 'percentage', value: '', minimum_order_amount: '', maximum_discount_amount: '', usage_limit: '', is_active: true }
const blankTax = { name: '', country: '', state: '', postal_code: '', rate: '', is_active: true }
const blankShipping = { name: '', code: '', description: '', is_active: true, rates: [] }

function Pager({ result, page, setPage }) {
  return <Pagination page={result?.meta?.current_page || page} pageCount={result?.meta?.last_page || 1} onPageChange={setPage} />
}

function ConfigPanel({ title, initial, fields, fetcher, saver, remover }) {
  const [result, setResult] = useState(null)
  const [form, setForm] = useState(initial)
  const [error, setError] = useState('')
  const [page, setPage] = useState(1)
  const load = () => fetcher({ page }).then(({ data }) => setResult(data.data)).catch(() => setError(`Unable to load ${title.toLowerCase()}.`))
  useEffect(() => {
    fetcher({ page }).then(({ data }) => setResult(data.data)).catch(() => setError(`Unable to load ${title.toLowerCase()}.`))
  }, [fetcher, page, title])
  const save = async (event) => {
    event.preventDefault(); setError('')
    try { await saver(form); setForm(initial); load() } catch (err) { setError(err.response?.data?.message || 'Please correct the form and try again.') }
  }
  const edit = (item) => setForm({ ...initial, ...item })
  return <section><div className="d-flex justify-content-between align-items-center mb-3"><h2 className="h4 mb-0">{title}</h2><Pager result={result} page={page} setPage={setPage} /></div>
    {error && <p className="text-danger">{error}</p>}
    <div className="row g-4"><div className="col-lg-5"><Card className="p-3"><form onSubmit={save}>{fields(form, setForm)}<div className="d-flex gap-2"><Button type="submit">{form.id ? 'Save changes' : `Add ${title.slice(0, -1)}`}</Button>{form.id && <Button variant="ghost" onClick={() => setForm(initial)}>Cancel</Button>}</div></form></Card></div>
      <div className="col-lg-7">{!result ? <Loader /> : <div className="d-grid gap-2">{result.data.map((item) => <Card className="p-3 d-flex justify-content-between align-items-start" key={item.id}><div><strong>{item.name || item.code}</strong><div className="small text-secondary">{item.code && item.name ? item.code : ''} {item.rate ? `${item.rate}%` : ''} {item.value ? `${item.type === 'percentage' ? `${item.value}%` : money(item.value)}` : ''}</div></div><div className="d-flex gap-2"><Button variant="ghost" onClick={() => edit(item)}>Edit</Button><Button variant="ghost" onClick={async () => { await remover(item.id); load() }}>Delete</Button></div></Card>)}</div>}</div></div>
  </section>
}

function OrdersPanel() {
  const [result, setResult] = useState(null)
  const [error, setError] = useState('')
  const [page, setPage] = useState(1)
  const [selected, setSelected] = useState(null)
  const load = () => api.orders({ page }).then(({ data }) => setResult(data.data)).catch(() => setError('Unable to load orders.'))
  useEffect(load, [page])
  const move = async (order, status) => { try { await api.transition(order.id, { status }); setSelected(null); load() } catch (err) { setError(err.response?.data?.message || 'This transition is unavailable.') } }
  return <section><div className="d-flex justify-content-between align-items-center mb-3"><h2 className="h4 mb-0">Order management</h2><Pager result={result} page={page} setPage={setPage} /></div>{error && <p className="text-danger">{error}</p>}
    {!result ? <Loader /> : <div className="d-grid gap-2">{result.data.map((order) => <Card className="p-3" key={order.id}><div className="d-flex justify-content-between gap-3"><div><strong>{order.number}</strong><div className="small text-secondary">{order.customer?.name || 'Customer'} · {new Date(order.created_at).toLocaleDateString()}</div></div><div className="text-end"><span className="status-pill">{order.status}</span><strong className="d-block mt-1">{money(order.grand_total, order.currency)}</strong></div></div><div className="d-flex gap-2 mt-3"><Button variant="ghost" onClick={async () => { const { data } = await api.order(order.id); setSelected(data.data) }}>View history</Button>{({ pending: ['processing', 'cancelled'], processing: ['shipped', 'cancelled'], shipped: ['delivered'], delivered: ['return_requested'], return_requested: ['returned'] }[order.status] || []).map((status) => <Button variant="secondary" key={status} onClick={() => move(order, status)}>Mark {status}</Button>)}</div></Card>)}</div>}
    {selected && <Card className="p-3 mt-4"><div className="d-flex justify-content-between"><h3 className="h5">History · {selected.number}</h3><Button variant="ghost" onClick={() => setSelected(null)}>Close</Button></div><ul className="mb-0">{selected.status_history?.map((item, index) => <li key={`${item.created_at}-${index}`}>{item.from_status || 'Created'} → {item.to_status}{item.note ? ` — ${item.note}` : ''}</li>)}</ul></Card>}
  </section>
}

function PaymentsPanel() {
  const [result, setResult] = useState(null); const [error, setError] = useState(''); const [page, setPage] = useState(1)
  const load = () => api.payments({ page }).then(({ data }) => setResult(data.data)).catch(() => setError('Unable to load payments.'))
  useEffect(load, [page])
  const action = async (payment, type) => { try { if (type === 'capture') await api.capture(payment.id); else { const amount = window.prompt('Refund amount', payment.amount); if (!amount) return; await api.refund(payment.id, amount) } load() } catch (err) { setError(err.response?.data?.message || 'Payment action failed.') } }
  return <section><div className="d-flex justify-content-between align-items-center mb-3"><h2 className="h4 mb-0">Payments & refunds</h2><Pager result={result} page={page} setPage={setPage} /></div>{error && <p className="text-danger">{error}</p>}{!result ? <Loader /> : <div className="d-grid gap-2">{result.data.map((payment) => <Card className="p-3 d-flex justify-content-between" key={payment.id}><div><strong>{payment.order_number}</strong><div className="small text-secondary">{payment.provider} · {payment.status}</div></div><div className="text-end"><strong>{money(payment.amount, payment.currency)}</strong><div className="mt-2">{payment.status === 'pending' && <Button variant="secondary" onClick={() => action(payment, 'capture')}>Capture</Button>}{payment.status === 'paid' && <Button variant="ghost" onClick={() => action(payment, 'refund')}>Refund</Button>}</div></div></Card>)}</div>}</section>
}

function ReturnsPanel() {
  const [result, setResult] = useState(null); const [error, setError] = useState(''); const [page, setPage] = useState(1)
  const load = () => api.returns({ page }).then(({ data }) => setResult(data.data)).catch(() => setError('Unable to load returns.'))
  useEffect(load, [page])
  const update = async (item, status) => { try { await api.updateReturn(item.id, { status, admin_note: '' }); load() } catch (err) { setError(err.response?.data?.message || 'Return action failed.') } }
  return <section><div className="d-flex justify-content-between align-items-center mb-3"><h2 className="h4 mb-0">Returns</h2><Pager result={result} page={page} setPage={setPage} /></div>{error && <p className="text-danger">{error}</p>}{!result ? <Loader /> : <div className="d-grid gap-2">{result.data.map((item) => <Card className="p-3 d-flex justify-content-between" key={item.id}><div><strong>{item.order_number}</strong><div className="small text-secondary">{item.customer?.name} · {item.reason}</div></div><div className="d-flex gap-2 align-items-center"><span className="status-pill">{item.status}</span>{item.status === 'requested' && <><Button variant="secondary" onClick={() => update(item, 'approved')}>Approve</Button><Button variant="ghost" onClick={() => update(item, 'rejected')}>Reject</Button></>}{item.status === 'approved' && <Button variant="secondary" onClick={() => update(item, 'received')}>Mark received</Button>}</div></Card>)}</div>}</section>
}

export default function OperationsPage() {
  const { user } = useAuth()
  const isAdmin = useMemo(() => user?.roles?.some((role) => ['admin', 'super-admin'].includes(role)), [user])
  const [active, setActive] = useState('orders')
  const nav = [{ label: 'Orders', to: '/admin/orders' }, ...(isAdmin ? [{ label: 'Coupons', to: '/admin/coupons' }, { label: 'Shipping', to: '/admin/shipping' }, { label: 'Tax rules', to: '/admin/taxes' }, { label: 'Payments', to: '/admin/payments' }, { label: 'Returns', to: '/admin/returns' }] : [])]
  const pathTab = window.location.pathname.split('/').pop()
  useEffect(() => setActive(pathTab === 'taxes' ? 'taxes' : pathTab === 'shipping' ? 'shipping' : pathTab === 'coupons' ? 'coupons' : pathTab === 'payments' ? 'payments' : pathTab === 'returns' ? 'returns' : 'orders'), [pathTab])
  const couponFields = (form, setForm) => <><Input label="Code" value={form.code} onChange={(e) => setForm({ ...form, code: e.target.value })} required /><label className="form-label">Type<select className="form-select velora-input" value={form.type} onChange={(e) => setForm({ ...form, type: e.target.value })}><option value="percentage">Percentage</option><option value="fixed">Fixed amount</option></select></label><Input label="Value" type="number" min="0.01" step="0.01" value={form.value} onChange={(e) => setForm({ ...form, value: e.target.value })} required /><label className="form-check mb-3"><input className="form-check-input" type="checkbox" checked={form.is_active} onChange={(e) => setForm({ ...form, is_active: e.target.checked })} /> Active</label></>
  const taxFields = (form, setForm) => <><Input label="Name" value={form.name} onChange={(e) => setForm({ ...form, name: e.target.value })} required /><Input label="Country (ISO code)" value={form.country} maxLength="2" onChange={(e) => setForm({ ...form, country: e.target.value.toUpperCase() })} required /><Input label="State (optional)" value={form.state} onChange={(e) => setForm({ ...form, state: e.target.value })} /><Input label="Rate %" type="number" min="0" max="100" step="0.01" value={form.rate} onChange={(e) => setForm({ ...form, rate: e.target.value })} required /><label className="form-check mb-3"><input className="form-check-input" type="checkbox" checked={form.is_active} onChange={(e) => setForm({ ...form, is_active: e.target.checked })} /> Active</label></>
  const shippingFields = (form, setForm) => {
    const rate = form.rates?.[0] || { country: '', amount: '', free_shipping_threshold: '', is_active: true }
    const updateRate = (changes) => setForm({ ...form, rates: [{ ...rate, ...changes }] })
    return <><Input label="Name" value={form.name} onChange={(e) => setForm({ ...form, name: e.target.value })} required /><Input label="Code" value={form.code} onChange={(e) => setForm({ ...form, code: e.target.value })} required /><Input label="Description" value={form.description || ''} onChange={(e) => setForm({ ...form, description: e.target.value })} /><Input label="Rate country (optional ISO code)" value={rate.country || ''} maxLength="2" onChange={(e) => updateRate({ country: e.target.value.toUpperCase() })} /><Input label="Rate amount" type="number" min="0" step="0.01" value={rate.amount} onChange={(e) => updateRate({ amount: e.target.value })} required /><Input label="Free shipping threshold (optional)" type="number" min="0" step="0.01" value={rate.free_shipping_threshold || ''} onChange={(e) => updateRate({ free_shipping_threshold: e.target.value })} /><label className="form-check mb-3"><input className="form-check-input" type="checkbox" checked={form.is_active} onChange={(e) => setForm({ ...form, is_active: e.target.checked })} /> Method active</label></>
  }
  return <section className="container py-4 py-lg-5"><div className="mb-4"><p className="eyebrow">{isAdmin ? 'ADMIN OPERATIONS' : 'SUPPLIER OPERATIONS'}</p><h1 className="h2 mb-0">Commerce operations</h1></div><div className="row g-4"><div className="col-lg-3"><Sidebar items={nav} /></div><main className="col-lg-9">{active === 'orders' && <OrdersPanel />}{active === 'coupons' && <ConfigPanel title="Coupons" initial={blankCoupon} fields={couponFields} fetcher={api.coupons} saver={api.saveCoupon} remover={api.removeCoupon} />}{active === 'shipping' && <ConfigPanel title="Shipping methods" initial={blankShipping} fields={shippingFields} fetcher={api.shippingMethods} saver={api.saveShippingMethod} remover={api.removeShippingMethod} />}{active === 'taxes' && <ConfigPanel title="Tax rules" initial={blankTax} fields={taxFields} fetcher={api.taxRules} saver={api.saveTaxRule} remover={api.removeTaxRule} />}{active === 'payments' && <PaymentsPanel />}{active === 'returns' && <ReturnsPanel />}</main></div></section>
}
