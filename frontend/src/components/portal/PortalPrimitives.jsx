import { FiDownload, FiFilter, FiSearch } from 'react-icons/fi'

export function PortalMetric({ icon: Icon, label, value, detail }) {
  return <article className="portal-metric"><span className="portal-metric-icon"><Icon /></span><small>{label}</small><strong>{value}</strong>{detail && <em>{detail}</em>}</article>
}

export function PortalFilters({ search, onSearch, status, onStatus, statuses = [] }) {
  return <div className="portal-filters"><label><FiSearch /><input value={search} onChange={(event) => onSearch(event.target.value)} placeholder="Search records" /></label>{onStatus && <label><FiFilter /><select value={status} onChange={(event) => onStatus(event.target.value)}><option value="">All statuses</option>{statuses.map((item) => <option key={item}>{item}</option>)}</select></label>}</div>
}

export function PortalTable({ columns, rows, empty = 'No live records available.' }) {
  return <div className="portal-table-wrap"><table className="portal-table"><thead><tr>{columns.map((column) => <th key={column.label}>{column.label}</th>)}</tr></thead><tbody>{rows.length ? rows.map((row, index) => <tr key={row.id || index}>{columns.map((column) => <td key={column.label}>{column.render ? column.render(row) : row[column.key] ?? '—'}</td>)}</tr>) : <tr><td colSpan={columns.length} className="portal-empty">{empty}</td></tr>}</tbody></table></div>
}

export function PortalBars({ points, label = 'Performance' }) {
  const max = Math.max(...points.map((point) => Number(point.value || 0)), 1)
  return <section className="portal-chart"><div><p className="portal-kicker">LIVE INTELLIGENCE</p><h2>{label}</h2></div><div className="portal-bars">{points.map((point, index) => <div key={`${point.label}-${index}`}><i style={{ height: `${Math.max(5, (Number(point.value || 0) / max) * 100)}%` }} /><small>{point.label}</small></div>)}</div></section>
}

export function PortalTimeline({ items }) {
  return <ol className="portal-timeline">{items.map((item, index) => <li key={`${item.title}-${index}`}><i /><div><strong>{item.title}</strong><small>{item.detail}</small></div></li>)}</ol>
}

export function PortalExport({ rows, columns, filename }) {
  const exportRows = () => {
    const quote = (value) => `"${String(value ?? '').replaceAll('"', '""')}"`
    const csv = [columns.map((column) => quote(column.label)).join(','), ...rows.map((row) => columns.map((column) => quote(column.value ? column.value(row) : row[column.key])).join(','))].join('\n')
    const url = URL.createObjectURL(new Blob([csv], { type: 'text/csv;charset=utf-8' }))
    const anchor = document.createElement('a'); anchor.href = url; anchor.download = `${filename}.csv`; anchor.click(); URL.revokeObjectURL(url)
  }
  return <button className="portal-export" onClick={exportRows} disabled={!rows.length}><FiDownload /> Export CSV</button>
}
