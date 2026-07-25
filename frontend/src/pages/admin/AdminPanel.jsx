import { useEffect, useMemo, useState } from 'react'
import { useLocation } from 'react-router-dom'
import { FiArrowUpRight, FiBox, FiCheckCircle, FiDollarSign, FiFileText, FiGlobe, FiPackage, FiPlus, FiShoppingBag } from 'react-icons/fi'
import AdminDataTable from '../../components/admin/AdminDataTable'
import AdminExportButton from '../../components/admin/AdminExportButton'
import AdminFilters from '../../components/admin/AdminFilters'
import AdminModal from '../../components/admin/AdminModal'
import AdminSidebar from '../../components/admin/AdminSidebar'
import AdminTopbar from '../../components/admin/AdminTopbar'
import Loader from '../../components/feedback/Loader'
import { adminShoppingService as api } from '../../services/adminShoppingService'
import { catalogService } from '../../services/catalogService'
import { PortalBars, PortalExport, PortalFilters, PortalTable } from '../../components/portal/PortalPrimitives'

const money = (value, currency = 'USD') => new Intl.NumberFormat('en-US', { style: 'currency', currency }).format(Number(value || 0))
const statusClass = (status) => `admin-status admin-status-${String(status || '').replaceAll('_', '-')}`
const pageNames = { '': 'Command center', products: 'Product catalog', orders: 'Order management', customers: 'Customers', analytics: 'Analytics', forecasting: 'Trend forecasting', reports: 'Reports', coupons: 'Coupons', support: 'Support desk', reviews: 'Reviews', suppliers: 'Suppliers', creators: 'Creators', notifications: 'Notifications', loyalty: 'Loyalty & VIP', 'gift-cards': 'Gift cards', alerts: 'Product alerts', activity: 'Activity logs', settings: 'Settings', international: 'International commerce', system: 'System health', operations: 'Operations center' }

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

function AnalyticsPage({ orders, analytics }) {
  const summary = analytics?.summary || {}; const daily = analytics?.daily || []
  const revenue = orders.reduce((sum, row) => sum + Number(row.grand_total || 0), 0)
  return <><section className="admin-metrics"><Metric icon={FiDollarSign} label="Paid revenue" value={money(summary.paid_revenue ?? revenue)} tone="violet" /><Metric icon={FiPackage} label="Orders" value={summary.orders ?? orders.length} tone="gold" /><Metric icon={FiBox} label="New customers" value={summary.new_customers ?? '—'} tone="blue" /><Metric icon={FiCheckCircle} label="Avg. order value" value={money(summary.average_order_value)} tone="green" /></section>{daily.length ? <PortalBars points={daily.map((row) => ({ label: new Date(row.date).toLocaleDateString(undefined, { month: 'short', day: 'numeric' }), value: row.revenue }))} label={`Revenue trend · ${analytics.range?.from || ''} — ${analytics.range?.to || ''}`} /> : <RevenueChart orders={orders} />}</>
}

function ForecastingPage({ analytics, products }) {
  const [search, setSearch] = useState('')
  const daily = analytics?.daily || []
  const average = daily.reduce((sum, row) => sum + Number(row.revenue || 0), 0) / Math.max(daily.length, 1)
  const rows = daily.map((row) => ({ ...row, forecast: Number(row.revenue || 0) + average })).filter((row) => row.date.includes(search))
  const columns = [{ label: 'Date', key: 'date' }, { label: 'Recorded revenue', render: (row) => money(row.revenue) }, { label: 'Trend baseline', render: () => money(average) }, { label: 'Projected signal', render: (row) => money(row.forecast) }]
  return <><section className="admin-panel"><div className="admin-panel-head"><div><p className="admin-kicker">EXPLAINABLE FORECAST</p><h2>Revenue trend forecast</h2></div><PortalExport rows={rows} columns={columns} filename="velora-trend-forecast" /></div><p className="admin-settings-copy">Projected signal is a transparent rolling baseline derived from the selected live analytics range; it is not a server-side predictive model.</p><PortalFilters search={search} onSearch={setSearch} /><PortalTable columns={columns} rows={rows} empty="No daily analytics are available for this range." /></section><section className="admin-panel mt-4"><p className="admin-kicker">INVENTORY WATCH</p><h2>Catalog tracked in trend planning</h2><p className="admin-settings-copy">{products.length} live catalog records are available for replenishment review.</p></section></>
}

function ReportsPage({ orders, products, analytics }) {
  const reports = [{ report: 'Order register', export: 'orders', records: orders.length, source: 'Orders API' }, { report: 'Customer register', export: 'customers', records: analytics?.summary?.new_customers ?? '—', source: 'Analytics API' }, { report: 'Inventory catalog', export: 'products', records: products.length, source: 'Products API' }]
  const download = async (report) => {
    const response = await api.reportCsv(report)
    const url = URL.createObjectURL(response.data); const anchor = document.createElement('a')
    anchor.href = url; anchor.download = `velora-${report}.csv`; anchor.click(); URL.revokeObjectURL(url)
  }
  return <section className="admin-panel"><div className="admin-panel-head"><div><p className="admin-kicker">LIVE DATA EXPORTS</p><h2>Reports</h2></div><AdminExportButton rows={reports} columns={[{ label: 'Report', key: 'report' }, { label: 'Records', key: 'records' }, { label: 'Source', key: 'source' }]} filename="velora-report-index" /></div><AdminDataTable columns={[{ label: 'Report', key: 'report' }, { label: 'Available records', key: 'records' }, { label: 'Data source', key: 'source' }, { label: 'Server export', render: (row) => <button className="admin-text-button" onClick={() => download(row.export)}>Download CSV</button> }]} rows={reports} /></section>
}

function SettingsPage() {
  return <section className="admin-panel"><div className="admin-panel-head"><div><p className="admin-kicker">COMMERCE CONFIGURATION</p><h2>Operations settings</h2></div></div>
    <p className="admin-settings-copy">These settings use the existing protected admin operations endpoints.</p>
    <div className="admin-settings-links"><a href="/admin/shipping"><FiPackage /><span><strong>Shipping methods</strong><small>Manage available methods and rates</small></span><FiArrowUpRight /></a><a href="/admin/taxes"><FiDollarSign /><span><strong>Tax rules</strong><small>Configure regional tax calculations</small></span><FiArrowUpRight /></a><a href="/admin/payments"><FiCheckCircle /><span><strong>Payments & refunds</strong><small>Review captures and refund actions</small></span><FiArrowUpRight /></a><a href="/admin/returns"><FiShoppingBag /><span><strong>Return requests</strong><small>Process submitted merchandise returns</small></span><FiArrowUpRight /></a><a href="/admin/international"><FiGlobe /><span><strong>International commerce</strong><small>Review regional readiness and fulfillment</small></span><FiArrowUpRight /></a></div>
  </section>
}

function SystemHealthPage({ health, onRefresh, onClearCache, clearing }) {
  if (!health) return <UnavailablePage title="System health" capability="system health monitoring" />
  const metrics = health.metrics || {}
  return (
    <>
      <section className="admin-metrics">
        <Metric icon={FiCheckCircle} label="Database" value={metrics.database?.connected ? 'Connected' : 'Unavailable'} tone={metrics.database?.connected ? 'green' : 'gold'} />
        <Metric icon={FiActivity} label="Cache driver" value={metrics.cache?.driver || '—'} change={metrics.cache?.healthy ? 'Healthy' : 'Check cache'} tone="blue" />
        <Metric icon={FiPackage} label="Pending jobs" value={metrics.queue?.pending_jobs ?? 0} change={`${metrics.queue?.failed_jobs ?? 0} failed`} tone="violet" />
        <Metric icon={FiDollarSign} label="Memory" value={`${metrics.memory?.usage_mb ?? 0} MB`} change={`Peak ${metrics.memory?.peak_mb ?? 0} MB`} tone="gold" />
      </section>
      <section className="admin-panel">
        <div className="admin-panel-head">
          <div><p className="admin-kicker">APPLICATION HEALTH</p><h2>Runtime diagnostics</h2></div>
          <div className="admin-actions">
            <button className="admin-button admin-button-secondary" type="button" onClick={onRefresh}>Refresh</button>
            <button className="admin-button admin-button-primary" type="button" onClick={onClearCache} disabled={clearing}>{clearing ? 'Clearing...' : 'Clear cache'}</button>
          </div>
        </div>
        <AdminDataTable
          columns={[
            { label: 'Component', key: 'component' },
            { label: 'Status', key: 'status' },
            { label: 'Details', key: 'details' },
          ]}
          rows={[
            { component: 'Laravel', status: health.application?.laravel || '—', details: `Debug ${health.application?.debug ? 'on' : 'off'}` },
            { component: 'Queue connection', status: metrics.queue?.connection || '—', details: `${metrics.queue?.pending_jobs ?? 0} pending` },
            { component: 'Storage free', status: `${metrics.storage?.disk_free_mb ?? 0} MB`, details: health.application?.environment },
            { component: 'Maintenance', status: health.application?.maintenance_mode ? 'Enabled' : 'Disabled', details: 'Application mode' },
          ]}
        />
      </section>
    </>
  )
}
function OperationsPage({ operations, onRunBackup, onToggleFlag }) {
  if (!operations) return <UnavailablePage title="Operations center" capability="enterprise operations monitoring" />
  const { metrics, queues, scheduler, backups, flags, webhooks } = operations
  return <>
    <section className="admin-metrics">
      <Metric icon={FiActivity} label="Pending jobs" value={queues?.pending_jobs ?? 0} change={`${queues?.failed_jobs ?? 0} failed`} tone="violet" />
      <Metric icon={FiPackage} label="Memory usage" value={`${metrics?.snapshot?.memory?.usage_mb ?? 0} MB`} tone="blue" />
      <Metric icon={FiCheckCircle} label="Backups" value={backups?.length ?? 0} change="Retention managed" tone="green" />
      <Metric icon={FiDollarSign} label="Webhooks" value={webhooks?.length ?? 0} change="Recent events" tone="gold" />
    </section>
    <section className="admin-panel"><div className="admin-panel-head"><div><p className="admin-kicker">QUEUE DASHBOARD</p><h2>Background processing</h2></div><button className="admin-button admin-button-primary" type="button" onClick={onRunBackup}>Queue backup</button></div><AdminDataTable columns={[{ label: 'Connection', render: () => queues?.connection }, { label: 'Pending', render: () => queues?.pending_jobs }, { label: 'Failed', render: () => queues?.failed_jobs }]} rows={[queues || {}]} emptyMessage="Queue metrics unavailable." /></section>
    <section className="admin-panel mt-4"><div className="admin-panel-head"><div><p className="admin-kicker">SCHEDULER</p><h2>Recent task runs</h2></div></div><AdminDataTable columns={[{ label: 'Command', key: 'command' }, { label: 'Status', key: 'status' }, { label: 'Duration', render: (row) => `${row.duration_ms || 0} ms` }, { label: 'Finished', render: (row) => row.finished_at ? new Date(row.finished_at).toLocaleString() : '—' }]} rows={scheduler || []} emptyMessage="No scheduler runs recorded yet." /></section>
    <section className="admin-panel mt-4"><div className="admin-panel-head"><div><p className="admin-kicker">FEATURE FLAGS</p><h2>Runtime controls</h2></div></div><AdminDataTable columns={[{ label: 'Flag', key: 'key' }, { label: 'Name', key: 'name' }, { label: 'Enabled', render: (row) => <button className="admin-text-button" type="button" onClick={() => onToggleFlag(row)}>{row.enabled ? 'On' : 'Off'}</button> }]} rows={flags || []} /></section>
  </>
}
function InternationalPage() {
  const regions = [{ market: 'United States', currency: 'USD', tax: 'Existing tax rules' }, { market: 'United Kingdom', currency: 'GBP', tax: 'Configure tax rule' }, { market: 'Gulf region', currency: 'AED / SAR', tax: 'Configure tax rule' }, { market: 'European Union', currency: 'EUR', tax: 'Configure tax rule' }]
  return <section className="admin-panel international-admin"><div className="admin-panel-head"><div><p className="admin-kicker">GLOBAL OPERATIONS</p><h2>International commerce</h2></div><FiGlobe /></div><p className="admin-settings-copy">Regional shipping and tax configuration uses the existing protected operations APIs. Tracking, duty and warehouse inventory endpoints are not yet exposed by the backend, so this view does not fabricate operational data.</p><AdminDataTable columns={[{ label: 'Market', key: 'market' }, { label: 'Settlement currency', key: 'currency' }, { label: 'Tax readiness', key: 'tax' }, { label: 'Next action', render: () => <a href="/admin/taxes">Configure tax rules</a> }]} rows={regions} /><div className="international-admin-note"><FiPackage /><span><strong>Carrier tracking & warehouse allocation</strong><small>Frontend ready; connect international API endpoints to enable live operational records.</small></span></div></section>
}

export default function AdminPanel() {
  const location = useLocation(); const slug = location.pathname.replace('/admin', '').replace(/^\//, '')
  const [open, setOpen] = useState(false); const [loading, setLoading] = useState(true); const [error, setError] = useState('')
  const [clearing, setClearing] = useState(false)
  const [data, setData] = useState({ orders: [], products: [], coupons: [], payments: [], categories: [], analytics: null, health: null, operations: null })
  const load = async () => {
    setLoading(true); setError('')
    const results = await Promise.allSettled([api.orders({ per_page: 20 }), api.products({ per_page: 20 }), api.coupons({ per_page: 20 }), api.payments({ per_page: 20 }), catalogService.categories(), api.analytics(), api.systemHealth(), api.operationsMetrics(), api.operationsQueues(), api.operationsScheduler(), api.operationsBackups(), api.featureFlags(), api.operationsWebhooks()])
    const [orders, products, coupons, payments] = results.map((result) => result.status === 'fulfilled' ? extract(result.value) : { data: [] })
    if (results.some((result, index) => index < 4 && result.status === 'rejected')) setError('Some live operational records could not be loaded. Check the current role and API connection.')
    const categoryResult = results[4]?.status === 'fulfilled' ? extract(results[4].value) : { data: [] }
    const analytics = results[5]?.status === 'fulfilled' ? results[5].value.data.data : null
    const health = results[6]?.status === 'fulfilled' ? results[6].value.data.data : null
    const operations = {
      metrics: results[7]?.status === 'fulfilled' ? results[7].value.data.data : null,
      queues: results[8]?.status === 'fulfilled' ? results[8].value.data.data : null,
      scheduler: results[9]?.status === 'fulfilled' ? results[9].value.data.data : null,
      backups: results[10]?.status === 'fulfilled' ? results[10].value.data.data : null,
      flags: results[11]?.status === 'fulfilled' ? results[11].value.data.data : null,
      webhooks: results[12]?.status === 'fulfilled' ? results[12].value.data.data : null,
    }
    setData({ orders: orders.data, products: products.data, coupons: coupons.data, payments: payments.data, categories: categoryResult.data, analytics, health, operations }); setLoading(false)
  }
  const runBackup = async () => { await api.runBackup(); await load() }
  const toggleFlag = async (flag) => { await api.updateFeatureFlag(flag.id, !flag.enabled); await load() }
  const clearCache = async () => {
    setClearing(true)
    try { await api.clearCache(); await load() } finally { setClearing(false) }
  }
  useEffect(() => { load() }, [])
  const title = pageNames[slug] || 'Administration'
  const content = loading ? <Loader /> : (() => {
    if (slug === 'products') return <ProductsPage products={data.products} categories={data.categories} refresh={load} />
    if (slug === 'orders') return <OrdersPage orders={data.orders} />
    if (slug === 'coupons') return <CouponsPage coupons={data.coupons} />
    if (slug === 'analytics') return <AnalyticsPage {...data} />
    if (slug === 'forecasting') return <ForecastingPage {...data} />
    if (slug === 'reports') return <ReportsPage {...data} />
    if (slug === 'settings') return <SettingsPage />
    if (slug === 'international') return <InternationalPage />
    if (slug === 'system') return <SystemHealthPage health={data.health} onRefresh={load} onClearCache={clearCache} clearing={clearing} />
    if (slug === 'operations') return <OperationsPage operations={data.operations} onRunBackup={runBackup} onToggleFlag={toggleFlag} />
    if (!slug) return <Dashboard {...data} />
    return <UnavailablePage title={title} capability={title.toLowerCase()} />
  })()
  return <div className={`admin-shell ${open ? 'admin-nav-open' : ''}`}><AdminSidebar /><main className="admin-main"><AdminTopbar title={title} onMenu={() => setOpen((value) => !value)} />{error && <p className="admin-inline-error">{error}</p>}<div className="admin-content">{content}</div></main></div>
}
