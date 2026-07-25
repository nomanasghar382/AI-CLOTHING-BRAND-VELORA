import { FiBell, FiMenu, FiSearch } from 'react-icons/fi'
import useAuth from '../../hooks/useAuth'

export default function AdminTopbar({ onMenu, title, subtitle }) {
  const { user } = useAuth()
  return <header className="admin-topbar">
    <button aria-label="Open navigation" className="admin-icon-button d-lg-none" onClick={onMenu}><FiMenu /></button>
    <div><p className="admin-kicker">{subtitle || 'VELORA OPERATIONS'}</p><h1>{title}</h1></div>
    <div className="admin-topbar-actions">
      <div className="admin-search"><FiSearch /><input aria-label="Search administration" placeholder="Search records" /></div>
      <button className="admin-icon-button" aria-label="Notifications"><FiBell /></button>
      <div className="admin-avatar">{(user?.name || 'A').slice(0, 1).toUpperCase()}</div>
    </div>
  </header>
}
