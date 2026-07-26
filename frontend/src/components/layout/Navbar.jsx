import { useState } from 'react'
import { Link, NavLink } from 'react-router-dom'
import { FiMenu, FiShoppingBag, FiX } from 'react-icons/fi'
import { PRIORITY_BRANDS, RETAIL_NAV } from '../../constants/gymToStreet'
import useCart from '../../hooks/useCart'

const brandSlug = (name) => name.toLowerCase().replace(/ & /g, '-').replace(/\s+/g, '-')

export default function Navbar() {
  const [open, setOpen] = useState(false)
  const [brandsOpen, setBrandsOpen] = useState(false)
  const { itemCount: cartCount } = useCart()

  return (
    <header className="site-header retail-header">
      <nav className="container py-3 d-flex align-items-center justify-content-between" aria-label="Main navigation">
        <Link className="brand-mark" to="/">VELORA</Link>
        <div className="d-none d-lg-flex align-items-center gap-4">
          {RETAIL_NAV.map((item) => (
            <NavLink key={item.to} className="nav-link-velora" to={item.to}>{item.label}</NavLink>
          ))}
          <div className="retail-nav-dropdown" onMouseEnter={() => setBrandsOpen(true)} onMouseLeave={() => setBrandsOpen(false)}>
            <button type="button" className="nav-link-velora retail-nav-dropdown-trigger" aria-expanded={brandsOpen}>Brands</button>
            {brandsOpen && (
              <div className="retail-nav-dropdown-menu">
                {PRIORITY_BRANDS.map((brand) => (
                  <Link key={brand} to={`/brands/${brandSlug(brand)}`} onClick={() => setBrandsOpen(false)}>{brand}</Link>
                ))}
              </div>
            )}
          </div>
          <Link className="nav-icon-link" to="/cart" aria-label={`Bag, ${cartCount} items`}>
            <FiShoppingBag />{cartCount > 0 && <span className="nav-counter">{cartCount}</span>}
          </Link>
        </div>
        <button type="button" className="btn btn-icon d-lg-none" aria-label="Menu" aria-expanded={open} onClick={() => setOpen(!open)}>
          {open ? <FiX /> : <FiMenu />}
        </button>
      </nav>
      {open && (
        <div className="container pb-3 d-lg-none mobile-nav">
          {RETAIL_NAV.map((item) => <NavLink key={item.to} className="nav-link-velora d-block py-2" onClick={() => setOpen(false)} to={item.to}>{item.label}</NavLink>)}
          <p className="small text-slate-300 mb-1 mt-2">Brands</p>
          {PRIORITY_BRANDS.map((brand) => (
            <NavLink key={brand} className="nav-link-velora d-block py-2" onClick={() => setOpen(false)} to={`/brands/${brandSlug(brand)}`}>{brand}</NavLink>
          ))}
          <NavLink className="nav-link-velora d-block py-2" onClick={() => setOpen(false)} to="/cart">Bag{cartCount ? ` (${cartCount})` : ''}</NavLink>
        </div>
      )}
    </header>
  )
}
