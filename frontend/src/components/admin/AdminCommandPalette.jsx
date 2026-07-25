import { useCallback, useEffect, useMemo, useRef, useState } from 'react'
import { useNavigate } from 'react-router-dom'
import { FiActivity, FiBox, FiCommand, FiHome, FiSearch, FiSettings, FiShoppingBag, FiZap } from 'react-icons/fi'
import { adminShoppingService as api } from '../../services/adminShoppingService'

const STATIC_COMMANDS = [
  { id: 'dashboard', label: 'Go to dashboard', href: '/admin', icon: FiHome, keywords: 'home command center' },
  { id: 'products', label: 'Manage products', href: '/admin/products', icon: FiBox, keywords: 'catalog inventory' },
  { id: 'orders', label: 'Manage orders', href: '/admin/orders', icon: FiShoppingBag, keywords: 'fulfillment pipeline' },
  { id: 'analytics', label: 'Open analytics', href: '/admin/analytics', icon: FiActivity, keywords: 'reports metrics' },
  { id: 'operations', label: 'Operations center', href: '/admin/operations', icon: FiZap, keywords: 'queues scheduler backups' },
  { id: 'settings', label: 'Settings', href: '/admin/settings', icon: FiSettings, keywords: 'configuration' },
]

const STORAGE_KEY = 'velora_admin_saved_filters'

export function loadSavedFilters() {
  try {
    return JSON.parse(localStorage.getItem(STORAGE_KEY) || '[]')
  } catch {
    return []
  }
}

export function saveAdminFilter(filter) {
  const current = loadSavedFilters()
  const next = [{ ...filter, id: crypto.randomUUID(), saved_at: new Date().toISOString() }, ...current].slice(0, 8)
  localStorage.setItem(STORAGE_KEY, JSON.stringify(next))
  return next
}

export default function AdminCommandPalette({ open, onClose, initialQuery = '' }) {
  const navigate = useNavigate()
  const inputRef = useRef(null)
  const [query, setQuery] = useState(initialQuery)
  const [loading, setLoading] = useState(false)
  const [results, setResults] = useState([])
  const [workspace, setWorkspace] = useState(null)
  const [savedFilters] = useState(loadSavedFilters)

  useEffect(() => {
    if (!open) return
    setQuery(initialQuery)
    api.workspace().then((response) => setWorkspace(response.data.data)).catch(() => setWorkspace(null))
    const timer = window.setTimeout(() => inputRef.current?.focus(), 0)
    return () => window.clearTimeout(timer)
  }, [open, initialQuery])

  useEffect(() => {
    if (!open) return undefined
    const onKeyDown = (event) => {
      if (event.key === 'Escape') onClose()
    }
    window.addEventListener('keydown', onKeyDown)
    return () => window.removeEventListener('keydown', onKeyDown)
  }, [open, onClose])

  useEffect(() => {
    if (!open) return undefined
    const trimmed = query.trim()
    if (trimmed.length < 2) {
      setResults([])
      setLoading(false)
      return undefined
    }
    setLoading(true)
    const timer = window.setTimeout(() => {
      api.globalSearch(trimmed)
        .then((response) => setResults(response.data.data.results || []))
        .catch(() => setResults([]))
        .finally(() => setLoading(false))
    }, 220)
    return () => window.clearTimeout(timer)
  }, [open, query])

  const staticMatches = useMemo(() => {
    const trimmed = query.trim().toLowerCase()
    if (!trimmed) return STATIC_COMMANDS
    return STATIC_COMMANDS.filter((command) => `${command.label} ${command.keywords}`.toLowerCase().includes(trimmed))
  }, [query])

  const run = useCallback(async (item) => {
    if (item.action === 'clear-cache') {
      await api.clearCache()
      onClose()
      return
    }
    if (item.href) navigate(item.href)
    onClose()
  }, [navigate, onClose])

  if (!open) return null

  const quickActions = workspace?.quick_actions || []
  const pinned = workspace?.pinned_dashboards || []
  const recentActivity = workspace?.recent_activity || []

  return <div className="admin-command-backdrop" role="presentation" onClick={onClose}>
    <section className="admin-command-palette" role="dialog" aria-modal="true" aria-label="Admin command palette" onClick={(event) => event.stopPropagation()}>
      <div className="admin-command-search">
        <FiSearch aria-hidden="true" />
        <input ref={inputRef} aria-label="Search admin records and commands" placeholder="Search records, pages, and actions" value={query} onChange={(event) => setQuery(event.target.value)} />
        <kbd><FiCommand />K</kbd>
      </div>
      <div className="admin-command-body">
        {loading && <p className="admin-command-meta">Searching…</p>}
        {!loading && results.length > 0 && <div className="admin-command-group"><p className="admin-kicker">SEARCH RESULTS</p>{results.map((item) => <button key={`${item.type}-${item.id}`} className="admin-command-item" type="button" onClick={() => run(item)}><span><strong>{item.title}</strong><small>{item.subtitle}</small></span><em>{item.type}</em></button>)}</div>}
        {staticMatches.length > 0 && <div className="admin-command-group"><p className="admin-kicker">NAVIGATION</p>{staticMatches.map((item) => <button key={item.id} className="admin-command-item" type="button" onClick={() => run(item)}><item.icon aria-hidden="true" /><span>{item.label}</span></button>)}</div>}
        {quickActions.length > 0 && <div className="admin-command-group"><p className="admin-kicker">QUICK ACTIONS</p>{quickActions.map((item) => <button key={item.id} className="admin-command-item" type="button" onClick={() => run(item)}><FiZap aria-hidden="true" /><span>{item.label}</span>{item.shortcut && <kbd>{item.shortcut}</kbd>}</button>)}</div>}
        {pinned.length > 0 && <div className="admin-command-group"><p className="admin-kicker">PINNED DASHBOARDS</p>{pinned.map((item) => <button key={item.id} className="admin-command-item" type="button" onClick={() => run(item)}><FiHome aria-hidden="true" /><span>{item.label}</span></button>)}</div>}
        {savedFilters.length > 0 && <div className="admin-command-group"><p className="admin-kicker">SAVED FILTERS</p>{savedFilters.map((item) => <button key={item.id} className="admin-command-item" type="button" onClick={() => run({ href: item.href })}><FiSearch aria-hidden="true" /><span>{item.label}</span></button>)}</div>}
        {recentActivity.length > 0 && <div className="admin-command-group"><p className="admin-kicker">RECENT ACTIVITY</p>{recentActivity.map((item) => <div key={item.id} className="admin-command-static"><span><strong>{item.event}</strong><small>{item.actor?.name || 'System'} · {new Date(item.created_at).toLocaleString()}</small></span></div>)}</div>}
      </div>
    </section>
  </div>
}
