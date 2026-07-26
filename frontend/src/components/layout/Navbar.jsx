import { useState } from 'react'
import { Link, NavLink } from 'react-router-dom'
import { FiMenu, FiShoppingBag, FiX } from 'react-icons/fi'
import useCart from '../../hooks/useCart'

const navItems = [
  { label: 'Shop', to: '/catalog?gender=men' },
  { label: 'Build my fit', to: '/ai/occasion' },
]

export default function Navbar() {
  const [open, setOpen] = useState(false)
  const { itemCount: cartCount } = useCart()

  return (
    <header className="site-header">
      <nav className="container py-3 d-flex align-items-center justify-content-between" aria-label="Main navigation">
        <Link className="brand-mark" to="/">VELORA<span>.</span></Link>
        <div className="d-none d-md-flex align-items-center gap-4">
          {navItems.map((item) => <NavLink key={item.to} className="nav-link-velora" to={item.to}>{item.label}</NavLink>)}
          <Link className="nav-icon-link" to="/cart" aria-label={`Bag, ${cartCount} items`}>
            <FiShoppingBag />{cartCount > 0 && <span className="nav-counter">{cartCount}</span>}
          </Link>
        </div>
        <button type="button" className="btn btn-icon d-md-none" aria-label="Menu" aria-expanded={open} onClick={() => setOpen(!open)}>
          {open ? <FiX /> : <FiMenu />}
        </button>
      </nav>
      {open && (
        <div className="container pb-3 d-md-none mobile-nav">
          {navItems.map((item) => <NavLink key={item.to} className="nav-link-velora d-block py-2" onClick={() => setOpen(false)} to={item.to}>{item.label}</NavLink>)}
          <NavLink className="nav-link-velora d-block py-2" onClick={() => setOpen(false)} to="/cart">Bag{cartCount ? ` (${cartCount})` : ''}</NavLink>
        </div>
      )}
    </header>
  )
}
