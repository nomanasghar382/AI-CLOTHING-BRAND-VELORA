import { useEffect, useMemo, useState } from 'react'
import { NavLink, useLocation } from 'react-router-dom'
import { FiBarChart2, FiBox, FiCreditCard, FiHome, FiPackage, FiRefreshCcw, FiTruck, FiUser } from 'react-icons/fi'
import Loader from '../../components/feedback/Loader'
import useAuth from '../../hooks/useAuth'
import { PortalBars, PortalExport, PortalFilters, PortalMetric, PortalTable, PortalTimeline } from '../../components/portal/PortalPrimitives'
import { adminShoppingService as api } from '../../services/adminShoppingService'

const money = (value) => new Intl.NumberFormat('en-US', { style: 'currency', currency: 'USD' }).format(Number(value || 0))
const tabs = [['Overview', '', FiHome], ['Products', 'products', FiBox], ['Inventory', 'inventory', FiPackage], ['Purchase orders', 'purchase-orders', FiRefreshCcw], ['Shipments', 'shipments', FiTruck], ['Returns', 'returns', FiRefreshCcw], ['Payments', 'payments', FiCreditCard], ['Analytics', 'analytics', FiBarChart2], ['Profile', 'profile', FiUser]]
const extract = (result) => result?.data?.data?.data || result?.data?.data || []

function Notice({ title, copy }) {
  return <section className="portal-notice"><p className="portal-kicker">API CAPABILITY</p><h2>{title}</h2><p>{copy}</p></section>
}

export default function SupplierPortal() {
  const { user } = useAuth(); const location = useLocation()
  const slug = location.pathname.replace('/supplier', '').replace(/^\//, '')
  const [loading, setLoading] = useState(true); const [error, setError] = useState(''); const [data, setData] = useState({ products: [], orders: [] })
  useEffect(() => {
    let active = true
    Promise.allSettled([api.products({ per_page: 100 }), api.orders({ per_page: 100 })]).then((results) => {
      if (!active) return
      setData({ products: results[0].status === 'fulfilled' ? extract(results[0].value) : [], orders: results[1].status === 'fulfilled' ? extract(results[1].value) : [] })
      if (results.some((result) => result.status === 'rejected')) setError('Some supplier records could not be loaded.')
      setLoading(false)
    })
    return () => { active = false }
  }, [])
  const content = loading ? <Loader /> : <SupplierContent slug={slug} data={data} user={user} />
  return <div className="portal-shell"><aside className="portal-sidebar"><NavLink to="/supplier" end className="portal-brand"><span>V</span>ELORA</NavLink><p>SUPPLIER STUDIO</p>{tabs.map(([label, path, Icon]) => <NavLink key={path} end={!path} to={path ? `/supplier/${path}` : '/supplier'} className="portal-nav"><Icon />{label}</NavLink>)}</aside><main className="portal-main"><header className="portal-header"><div><p className="portal-kicker">SUPPLIER PORTAL</p><h1>{tabs.find((tab) => tab[1] === slug)?.[0] || 'Overview'}</h1></div><div className="portal-user">{(user?.name || 'S').slice(0, 1)} <span>{user?.name || 'Supplier'}</span></div></header>{error && <p className="portal-error">{error}</p>}<div className="portal-content">{content}</div></main></div>
}

function SupplierContent({ slug, data, user }) {
  const [search, setSearch] = useState(''); const [status, setStatus] = useState('')
  const products = data.products.filter((row) => `${row.name} ${row.sku}`.toLowerCase().includes(search.toLowerCase()))
  const orders = data.orders.filter((row) => (!status || row.status === status) && `${row.number} ${row.status}`.toLowerCase().includes(search.toLowerCase()))
  const productColumns = [{ label: 'Product', render: (row) => <><strong>{row.name}</strong><small>{row.sku}</small></> }, { label: 'Price', render: (row) => money(row.price) }, { label: 'Stock', key: 'stock_quantity' }, { label: 'Status', key: 'status' }]
  const orderColumns = [{ label: 'Order', key: 'number' }, { label: 'Created', render: (row) => new Date(row.created_at).toLocaleDateString() }, { label: 'Amount', render: (row) => money(row.grand_total) }, { label: 'Status', key: 'status' }]
  const lowStock = products.filter((product) => Number(product.stock_quantity) <= Number(product.minimum_stock || 0))
  const revenue = orders.reduce((sum, order) => sum + Number(order.grand_total || 0), 0)
  const points = useMemo(() => orders.slice(-7).map((order) => ({ label: new Date(order.created_at).toLocaleDateString(undefined, { weekday: 'short' }), value: order.grand_total })), [orders])
  if (slug === 'products') return <section className="portal-panel"><div className="portal-panel-head"><div><p className="portal-kicker">OWNED CATALOG</p><h2>Products</h2></div><PortalExport rows={products} columns={productColumns} filename="velora-supplier-products" /></div><PortalFilters search={search} onSearch={setSearch} /><PortalTable columns={productColumns} rows={products} /></section>
  if (slug === 'inventory') return <section className="portal-panel"><div className="portal-panel-head"><div><p className="portal-kicker">STOCK CONTROL</p><h2>Inventory health</h2></div><PortalExport rows={lowStock} columns={productColumns} filename="velora-low-stock" /></div><PortalFilters search={search} onSearch={setSearch} /><PortalTable columns={productColumns} rows={products} empty="No supplier inventory returned." /></section>
  if (slug === 'shipments') return <section className="portal-panel"><div className="portal-panel-head"><div><p className="portal-kicker">FULFILMENT</p><h2>Shipment queue</h2></div><PortalExport rows={orders} columns={orderColumns} filename="velora-shipment-queue" /></div><PortalFilters search={search} onSearch={setSearch} status={status} onStatus={setStatus} statuses={['processing', 'shipped', 'delivered']} /><PortalTable columns={orderColumns} rows={orders.filter((row) => ['processing', 'shipped', 'delivered'].includes(row.status))} empty="No supplier fulfilment records." /></section>
  if (slug === 'analytics') return <><section className="portal-metrics"><PortalMetric icon={FiBarChart2} label="Order value" value={money(revenue)} detail={`${orders.length} supplier orders`} /><PortalMetric icon={FiPackage} label="Units in catalog" value={products.length} detail={`${lowStock.length} below minimum`} /><PortalMetric icon={FiTruck} label="Delivered" value={orders.filter((order) => order.status === 'delivered').length} detail="live order status" /></section><PortalBars points={points} label="Recent order value" /></>
  if (slug === 'profile') return <section className="portal-panel portal-profile"><p className="portal-kicker">ACCOUNT</p><h2>{user?.name || 'Supplier account'}</h2><dl><div><dt>Email</dt><dd>{user?.email || '—'}</dd></div><div><dt>Role</dt><dd>{user?.roles?.join(', ') || 'supplier'}</dd></div></dl></section>
  if (['purchase-orders', 'returns', 'payments'].includes(slug)) return <Notice title={tabs.find((tab) => tab[1] === slug)?.[0]} copy="This portal is connected only to the currently available supplier catalog and owned-order APIs. A dedicated supplier API is required before showing operational records here." />
  return <><section className="portal-hero"><div><p className="portal-kicker">WELCOME BACK</p><h2>Run your collection with calm, clear signals.</h2><p>Your catalog and fully owned orders update from the protected commerce API.</p></div><span>{products.length} products</span></section><section className="portal-metrics"><PortalMetric icon={FiBox} label="Active products" value={products.filter((product) => product.status === 'published').length} detail="from your catalog" /><PortalMetric icon={FiPackage} label="Low stock" value={lowStock.length} detail="at or below minimum" /><PortalMetric icon={FiTruck} label="Open orders" value={orders.filter((order) => !['delivered', 'cancelled'].includes(order.status)).length} detail="requires attention" /></section><div className="portal-grid"><PortalBars points={points} label="Recent order value" /><section className="portal-panel"><p className="portal-kicker">ORDER TIMELINE</p><h2>Latest activity</h2><PortalTimeline items={orders.slice(0, 5).map((order) => ({ title: order.number, detail: `${order.status} · ${new Date(order.created_at).toLocaleDateString()}` }))} /></section></div></>
}
