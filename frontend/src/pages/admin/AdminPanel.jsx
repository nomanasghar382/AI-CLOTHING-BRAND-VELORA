import { useEffect, useMemo, useState } from 'react'
import { useLocation } from 'react-router-dom'
import { FiArrowUpRight, FiBox, FiCheckCircle, FiDollarSign, FiFileText, FiPackage, FiPlus, FiShoppingBag } from 'react-icons/fi'
import AdminDataTable from '../../components/admin/AdminDataTable'
import AdminExportButton from '../../components/admin/AdminExportButton'
import AdminFilters from '../../components/admin/AdminFilters'
import AdminModal from '../../components/admin/AdminModal'
import AdminSidebar from '../../components/admin/AdminSidebar'
import AdminTopbar from '../../components/admin/AdminTopbar'
import Loader from '../../components/feedback/Loader'
import { adminShoppingService as api } from '../../services/adminShoppingService'
import { catalogService } from '../../services/catalogService'

const money = (value, currency = 'USD') => new Intl.NumberFormat('en-US', { style: 'currency', currency }).format(Number(value || 0))
const statusClass = (status) => `admin-status admin-status-${String(status || '').replaceAll('_', '-')}`
const pageNames = { '': 'Command center', products: 'Product catalog', orders: 'Order management', customers: 'Customers', analytics: 'Analytics', reports: 'Reports', coupons: 'Coupons', support: 'Support desk', reviews: 'Reviews', suppliers: 'Suppliers', creators: 'Creators', notifications: 'Notifications', activity: 'Activity logs', settings: 'Settings' }

function extract(response) {
  const payload = response?.data?.data
  return payload?.data ? payload : { data: Array.isArray(payload) ? payload : [], meta: payload?.meta || {} }
}

function Metric({ icon: Icon, label, value, change, tone }) {
  return <article className={`admin-metric admin-metric-${tone}`}><div className="admin-metric-icon"><Icon /></div><p>{label}</p><strong>{value}</strong>{change && <span><FiArrowUpRight /> {change}</span>}</article>
}

function RevenueChart({ orders }) {
  const points = useMemo(() => {
    const values = Array.from({ length: 7 }, () => 0)
    orders.forEach((order) => { const day = new Date(order.created_at).getDay(); values[day] += Number(order.grand_total || 0) })
    const max = Math.max(...values, 1)
    return values.map((value, index) => ({ day: ['S', 'M', 'T', 'W', 'T', 'F', 'S'][index], height: Math.max(7, (value / max) * 100), value }))
  }, [orders])
  return <div className="admin-chart"><div className="admin-chart-head"><div><p className="admin-kicker">SALES PERFORMANCE</p><h2>Revenue this week</h2></div><span>Live order data</span></div><div className="admin-bars">{points.map((point, index) => <div className="admin-bar-column" key={`${point.day}-${index}`}><div className="admin-bar-track"><div className="admin-bar" style={{ height: `${point.height}%` }} title={money(point.value)} /></div><small>{point.day}</small></div>)}</div></div>
}

function UnavailablePage({ title, capability }) {
  return <section className="admin-unavailable"><FiFileText /><p className="admin-kicker">API CAPABILITY</p><h2>{title} is not connected yet.</h2><p>The current backend does not expose an admin endpoint for {capability}. This panel intentionally shows no invented records or controls.</p></section>
}

function ProductModal({ product, categories, onClose, onSaved }) {
  const [form, setForm] = useState(product || { name: '', slug: '', sku: '', category_id: '', price: '', stock_quantity: 0, status: 'draft' })
  const [error, setError] = useState('')
  const save = async (event) => {
    event.preventDefault(); setError('')
    try { await api.saveProduct({ ...form, category_id: Number(form.category_id), price: Number(form.price), stock_quantity: Number(form.stock_quantity) }); onSaved() } catch (err) { setError(err.response?.data?.message || 'Product could not be saved. Check the required fields.') }
  }
  const set = (key, value) => setForm((current) => ({ ...current, [key]: value }))
  return <AdminModal title={product?.id ? 'Edit product' : 'Add product'} onClose={onClose}><form className="admin-form" onSubmit={save}>
    {error && <p className="admin-form-error">{error}</p>}
    <label>Name<input required value={form.name} onChange={(e) => { set('name', e.target.value); if (!product) set('slug', e.target.value.toLowerCase().trim().replace(/[^a-z0-9]+/g, '-').replace(/^-|-$/g, '')) }} /></label>
    <div className="admin-form-grid"><label>SKU<input required value={form.sku} onChange={(e) => set('sku', e.target.value)} /></label><label>Slug<input required value={form.slug} onChange={(e) => set('slug', e.target.value)} /></label></div>
    <div className="admin-form-grid"><label>Category<select required value={form.category_id} onChange={(e) => set('category_id', e.target.value)}><option value="">Select category</option>{categories.map((category) => <option key={category.id} value={category.id}>{category.name}</option>)}</select></label><label>Status<select value={form.status} onChange={(e) => set('status', e.target.value)}><option value="draft">Draft</option><option value="published">Published</option><option value="archived">Archived</option></select></label></div>
    <div className="admin-form-grid"><label>Price<input required type="number" min="0" step="0.01" value={form.price} onChange={(e) => set('price', e.target.value)} /></label><label>Stock quantity<input type="number" min="0" value={form.stock_quantity} onChange={(e) => set('stock_quantity', e.target.value)} /></label></div>
    <div className="admin-modal-actions"><button type="button" className="admin-button admin-button-secondary" onClick={onClose}>Cancel</button><button className="admin-button admin-button-primary" type="submit">Save product</button></div>
  </form></AdminModal>
}

function Dashboard({ orders, products, coupons, payments }) {
  const revenue = orders.reduce((total, order) => total + Number(order.grand_total || 0), 0)
  const paid = payments.filter((payment) => payment.status === 'paid').length
  return <><section className="admin-metrics">
    <Metric icon={FiDollarSign} label="Gross revenue" value={money(revenue)} change={`${orders.length} recent orders`} tone="violet" />
    <Metric icon={FiShoppingBag} label="Orders" value={orders.length} change={`${orders.filter((order) => order.status === 'pending').length} awaiting action`} tone="gold" />
    <Metric icon={FiBox} label="Catalog items" value={products.length} change={`${products.filter((product) => product.status === 'published').length} published`} tone="blue" />
    <Metric icon={FiCheckCircle} label="Captured payments" value={paid} change={`${coupons.length} active offers`} tone="green" />
  </section><div className="admin-dashboard-grid"><RevenueChart orders={orders} /><section className="admin-panel"><div className="admin-panel-head"><div><p className="admin-kicker">FULFILMENT</p><h2>Recent orders</h2></div></div><OrderTable rows={orders.slice(0, 5)} /></section></div></>
}

function OrderTable({ rows, onSelect }) {
  const columns = [{ label: 'Order', key: 'number' }, { label: 'Customer', render: (row) => row.customer?.name || row.user?.name || '—' }, { label: 'Date', render: (row) => new Date(row.created_at).toLocaleDateString() }, { label: 'Total', render: (row) => money(row.grand_total, row.currency) }, { label: 'Status', render: (row) => <span className={statusClass(row.status)}>{row.status}</span> }]
  return <AdminDataTable columns={columns} rows={rows} onRowClick={onSelect} emptyMessage="No orders have been returned by the API." />
}

function ProductsPage({ products, categories, refresh }) {
  const [query, setQuery] = useState(''); const [modal, setModal] = useState(null)
  const rows = products.filter((item) => `${item.name} ${item.sku}`.toLowerCase().includes(query.toLowerCase()))
  const columns = [{ label: 'Product', render: (row) => <div><strong>{row.name}</strong><small>{row.sku}</small></div> }, { label: 'Category', render: (row) => row.category?.name || '—' }, { label: 'Price', render: (row) => money(row.price) }, { label: 'Stock', key: 'stock_quantity' }, { label: 'Status', render: (row) => <span className={statusClass(row.status)}>{row.status}</span> }]
  return <section className="admin-panel"><div className="admin-panel-head"><div><p className="admin-kicker">CATALOG API</p><h2>Inventory</h2></div><div className="admin-actions"><AdminExportButton rows={rows} columns={columns} filename="velora-products" /><button className="admin-button admin-button-primary" onClick={() => setModal({})}><FiPlus /> Add product</button></div></div><AdminFilters search={query} onSearch={setQuery} onClear={() => setQuery('')} /><AdminDataTable columns={columns} rows={rows} onRowClick={setModal} />{modal && <ProductModal product={modal.id ? modal : null} categories={categories} onClose={() => setModal(null)} onSaved={() => { setModal(null); refresh() }} />}</section>
}

function OrdersPage({ orders }) {
  const [query, setQuery] = useState(''); const [status, setStatus] = useState('')
  const rows = orders.filter((order) => (!status || order.status === status) && `${order.number} ${order.customer?.name || order.user?.name || ''}`.toLowerCase().includes(query.toLowerCase()))
  const columns = [{ label: 'Order', key: 'number' }, { label: 'Customer', render: (row) => row.customer?.name || row.user?.name || '—' }, { label: 'Items', render: (row) => row.items?.length ?? '—' }, { label: 'Total', render: (row) => money(row.grand_total, row.currency) }, { label: 'Status', render: (row) => <span className={statusClass(row.status)}>{row.status}</span> }]
  return <section className="admin-panel"><div className="admin-panel-head"><div><p className="admin-kicker">ORDERS API</p><h2>Order pipeline</h2></div><AdminExportButton rows={rows} columns={columns} filename="velora-orders" /></div><AdminFilters search={query} onSearch={setQuery} status={status} onStatus={setStatus} statuses={['pending', 'processing', 'shipped', 'delivered', 'cancelled']} onClear={() => { setQuery(''); setStatus('') }} /><OrderTable rows={rows} /></section>
}

function CouponsPage({ coupons }) {
  const columns = [{ label: 'Code', key: 'code' }, { label: 'Type', key: 'type' }, { label: 'Value', render: (row) => row.type === 'percentage' ? `${row.value}%` : money(row.value) }, { label: 'Usage', render: (row) => `${row.usage_count || 0}${row.usage_limit ? ` / ${row.usage_limit}` : ''}` }, { label: 'State', render: (row) => <span className={statusClass(row.is_active ? 'active' : 'inactive')}>{row.is_active ? 'Active' : 'Inactive'}</span> }]
  return <section className="admin-panel"><div className="admin-panel-head"><div><p className="admin-kicker">COUPONS API</p><h2>Promotional offers</h2></div><AdminExportButton rows={coupons} columns={columns} filename="velora-coupons" /></div><AdminDataTable columns={columns} rows={coupons} emptyMessage="No coupon records have been returned by the API." /></section>
}

function AnalyticsPage({ orders, products, payments }) {
  const revenue = orders.reduce((sum, row) => sum + Number(row.grand_total || 0), 0)
  return <><section className="admin-metrics"><Metric icon={FiDollarSign} label="Order value" value={money(revenue / Math.max(orders.length, 1))} tone="violet" /><Metric icon={FiPackage} label="Units ordered" value={orders.reduce((sum, row) => sum + (row.items?.length || 0), 0)} tone="gold" /><Metric icon={FiBox} label="Products tracked" value={products.length} tone="blue" /><Metric icon={FiCheckCircle} label="Payment records" value={payments.length} tone="green" /></section><RevenueChart orders={orders} /></>
}

function ReportsPage({ orders, payments, products }) {
  const reports = [{ report: 'Order register', records: orders.length, source: 'Orders API' }, { report: 'Payment reconciliation', records: payments.length, source: 'Payments API' }, { report: 'Inventory catalog', records: products.length, source: 'Products API' }]
  return <section className="admin-panel"><div className="admin-panel-head"><div><p className="admin-kicker">LIVE DATA EXPORTS</p><h2>Reports</h2></div><AdminExportButton rows={reports} columns={[{ label: 'Report', key: 'report' }, { label: 'Records', key: 'records' }, { label: 'Source', key: 'source' }]} filename="velora-report-index" /></div><AdminDataTable columns={[{ label: 'Report', key: 'report' }, { label: 'Available records', key: 'records' }, { label: 'Data source', key: 'source' }]} rows={reports} /></section>
}

function SettingsPage() {
  return <section className="admin-panel"><div className="admin-panel-head"><div><p className="admin-kicker">COMMERCE CONFIGURATION</p><h2>Operations settings</h2></div></div>
    <p className="admin-settings-copy">These settings use the existing protected admin operations endpoints.</p>
    <div className="admin-settings-links"><a href="/admin/shipping"><FiPackage /><span><strong>Shipping methods</strong><small>Manage available methods and rates</small></span><FiArrowUpRight /></a><a href="/admin/taxes"><FiDollarSign /><span><strong>Tax rules</strong><small>Configure regional tax calculations</small></span><FiArrowUpRight /></a><a href="/admin/payments"><FiCheckCircle /><span><strong>Payments & refunds</strong><small>Review captures and refund actions</small></span><FiArrowUpRight /></a><a href="/admin/returns"><FiShoppingBag /><span><strong>Return requests</strong><small>Process submitted merchandise returns</small></span><FiArrowUpRight /></a></div>
  </section>
}

export default function AdminPanel() {
  const location = useLocation(); const slug = location.pathname.replace('/admin', '').replace(/^\//, '')
  const [open, setOpen] = useState(false); const [loading, setLoading] = useState(true); const [error, setError] = useState('')
  const [data, setData] = useState({ orders: [], products: [], coupons: [], payments: [], categories: [] })
  const load = async () => {
    setLoading(true); setError('')
    const results = await Promise.allSettled([api.orders({ per_page: 20 }), api.products({ per_page: 20 }), api.coupons({ per_page: 20 }), api.payments({ per_page: 20 }), catalogService.categories()])
    const [orders, products, coupons, payments] = results.map((result) => result.status === 'fulfilled' ? extract(result.value) : { data: [] })
    if (results.some((result, index) => index < 4 && result.status === 'rejected')) setError('Some live operational records could not be loaded. Check the current role and API connection.')
    const categoryResult = results[4]?.status === 'fulfilled' ? extract(results[4].value) : { data: [] }
    setData({ orders: orders.data, products: products.data, coupons: coupons.data, payments: payments.data, categories: categoryResult.data }); setLoading(false)
  }
  useEffect(() => { load() }, [])
  const title = pageNames[slug] || 'Administration'
  const content = loading ? <Loader /> : (() => {
    if (slug === 'products') return <ProductsPage products={data.products} categories={data.categories} refresh={load} />
    if (slug === 'orders') return <OrdersPage orders={data.orders} />
    if (slug === 'coupons') return <CouponsPage coupons={data.coupons} />
    if (slug === 'analytics') return <AnalyticsPage {...data} />
    if (slug === 'reports') return <ReportsPage {...data} />
    if (slug === 'settings') return <SettingsPage />
    if (!slug) return <Dashboard {...data} />
    return <UnavailablePage title={title} capability={title.toLowerCase()} />
  })()
  return <div className={`admin-shell ${open ? 'admin-nav-open' : ''}`}><AdminSidebar /><main className="admin-main"><AdminTopbar title={title} onMenu={() => setOpen((value) => !value)} />{error && <p className="admin-inline-error">{error}</p>}<div className="admin-content">{content}</div></main></div>
}
