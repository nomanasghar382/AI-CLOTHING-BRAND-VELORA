import { useState } from 'react'
import { Link, NavLink } from 'react-router-dom'
import { FiMenu, FiUser, FiX } from 'react-icons/fi'
import useAuth from '../../hooks/useAuth'

const navItems = [{ label: 'Foundation', to: '/' }, { label: 'Catalog', to: '/catalog' }, { label: 'Our Vision', to: '/about' }]

export default function Navbar() {
  const [open, setOpen] = useState(false)
  const { isAuthenticated, user, signOut } = useAuth()

  return (
    <header className="site-header">
      <nav className="container py-3 d-flex align-items-center justify-content-between" aria-label="Main navigation">
        <Link className="brand-mark" to="/">VELORA<span>.</span></Link>
        <div className="d-none d-lg-flex align-items-center gap-4">
          {navItems.map((item) => <NavLink key={item.to} className="nav-link-velora" to={item.to}>{item.label}</NavLink>)}
        </div>
        <div className="d-none d-lg-flex align-items-center gap-3">
          {isAuthenticated ? (
            <>
              <span className="small text-slate-300">Hello, {user?.first_name || user?.name}</span>
              <button type="button" className="btn btn-link nav-link-velora p-0" onClick={signOut}>Sign out</button>
            </>
          ) : <Link className="nav-link-velora d-flex gap-2 align-items-center" to="/login"><FiUser /> Sign in</Link>}
        </div>
        <button type="button" className="btn btn-icon d-lg-none" aria-label="Toggle menu" onClick={() => setOpen(!open)}>
          {open ? <FiX /> : <FiMenu />}
        </button>
      </nav>
      {open && <div className="container pb-3 d-lg-none mobile-nav">
        {navItems.map((item) => <NavLink key={item.to} className="nav-link-velora d-block py-2" onClick={() => setOpen(false)} to={item.to}>{item.label}</NavLink>)}
        <NavLink className="nav-link-velora d-block py-2" onClick={() => setOpen(false)} to={isAuthenticated ? '/account' : '/login'}>{isAuthenticated ? 'Account' : 'Sign in'}</NavLink>
      </div>}
    </header>
  )
}
