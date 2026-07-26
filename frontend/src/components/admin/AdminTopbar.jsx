import { useEffect, useState } from 'react'
import { FiBell, FiMenu, FiSearch } from 'react-icons/fi'
import useAuth from '../../hooks/useAuth'
import AdminCommandPalette from './AdminCommandPalette'

export default function AdminTopbar({ onMenu, title, subtitle }) {
  const { user } = useAuth()
  const [paletteOpen, setPaletteOpen] = useState(false)
  const [searchQuery, setSearchQuery] = useState('')

  useEffect(() => {
    const onKeyDown = (event) => {
      if ((event.metaKey || event.ctrlKey) && event.key.toLowerCase() === 'k') {
        event.preventDefault()
        setPaletteOpen(true)
      }
    }
    window.addEventListener('keydown', onKeyDown)
    return () => window.removeEventListener('keydown', onKeyDown)
  }, [])

  const openSearch = () => {
    setSearchQuery('')
    setPaletteOpen(true)
  }

  const submitSearch = (event) => {
    event.preventDefault()
    setPaletteOpen(true)
  }

  return <>
    <header className="admin-topbar">
      <button aria-label="Open navigation" className="admin-icon-button d-lg-none" onClick={onMenu} type="button"><FiMenu /></button>
      <div><p className="admin-kicker">{subtitle || 'VELORA OPERATIONS'}</p><h1>{title}</h1></div>
      <div className="admin-topbar-actions">
        <form className="admin-search" onSubmit={submitSearch}>
          <FiSearch aria-hidden="true" />
          <input aria-label="Search administration" placeholder="Search records (⌘K)" value={searchQuery} onChange={(event) => setSearchQuery(event.target.value)} onFocus={openSearch} />
        </form>
        <button className="admin-icon-button" aria-label="Open command palette" type="button" onClick={openSearch}><FiSearch /></button>
        <button className="admin-icon-button" aria-label="Notifications" type="button"><FiBell /></button>
        <div className="admin-avatar" aria-hidden="true">{(user?.name || 'A').slice(0, 1).toUpperCase()}</div>
      </div>
    </header>
    <AdminCommandPalette open={paletteOpen} onClose={() => setPaletteOpen(false)} initialQuery={searchQuery} />
  </>
}
