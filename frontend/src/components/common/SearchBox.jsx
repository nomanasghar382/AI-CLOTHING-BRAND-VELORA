import { FiSearch } from 'react-icons/fi'

export default function SearchBox({ value, onChange, placeholder = 'Search Velora' }) {
  return <div className="input-group"><span className="input-group-text velora-input border-end-0"><FiSearch /></span><input className="form-control velora-input border-start-0" value={value} onChange={onChange} placeholder={placeholder} aria-label={placeholder} /></div>
}
