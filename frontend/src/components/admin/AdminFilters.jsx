import { FiFilter, FiSearch, FiX } from 'react-icons/fi'

export default function AdminFilters({ search, onSearch, status, onStatus, statuses = [], onClear }) {
  return <div className="admin-filters">
    <label className="admin-filter-search"><FiSearch /><input value={search} onChange={(event) => onSearch(event.target.value)} placeholder="Search records" /></label>
    {onStatus && <label className="admin-filter-select"><FiFilter /><select value={status} onChange={(event) => onStatus(event.target.value)}>
      <option value="">All statuses</option>{statuses.map((item) => <option key={item} value={item}>{item}</option>)}
    </select></label>}
    <button className="admin-text-button" onClick={onClear}><FiX /> Clear</button>
  </div>
}
