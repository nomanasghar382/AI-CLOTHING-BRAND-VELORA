import { useState } from 'react'
import { Link, NavLink } from 'react-router-dom'
import { FiHeart, FiMenu, FiShoppingBag, FiStar, FiUser, FiX } from 'react-icons/fi'
import useAuth from '../../hooks/useAuth'
import useCart from '../../hooks/useCart'
import useWishlist from '../../hooks/useWishlist'
import MarketSelector from '../international/MarketSelector'
import NotificationCenter from '../notifications/NotificationCenter'

const navItems = [{ label: 'Foundation', to: '/' }, { label: 'Marketplace', to: '/marketplace' }, { label: 'Community', to: '/community' }, { label: 'Catalog', to: '/catalog' }, { label: 'Global services', to: '/international' }, { label: 'Our Vision', to: '/about' }]

export default function Navbar() {
  const [open, setOpen] = useState(false)
  const { isAuthenticated, user, signOut } = useAuth()
  const { itemCount: cartCount } = useCart()
  const { itemCount: wishlistCount } = useWishlist()

  return (
    <header className="site-header">
      <nav className="container py-3 d-flex align-items-center justify-content-between" aria-label="Main navigation">
        <Link className="brand-mark" to="/">VELORA<span>.</span></Link>
        <div className="d-none d-lg-flex align-items-center gap-4">
          {navItems.map((item) => <NavLink key={item.to} className="nav-link-velora" to={item.to}>{item.label}</NavLink>)}
        </div>
        <div className="d-none d-lg-flex align-items-center gap-3">
          <MarketSelector />
          <Link className="nav-icon-link" to="/wishlist" aria-label={`Wishlist, ${wishlistCount} items`}><FiHeart />{wishlistCount > 0 && <span className="nav-counter">{wishlistCount}</span>}</Link>
          <Link className="nav-icon-link" to="/cart" aria-label={`Shopping bag, ${cartCount} items`}><FiShoppingBag />{cartCount > 0 && <span className="nav-counter">{cartCount}</span>}</Link>
          {isAuthenticated ? (
            <>
              <Link className="nav-link-velora" to="/ai">AI stylist</Link>
              <Link className="nav-link-velora" to="/wardrobe">Wardrobe</Link>
              <NotificationCenter />
              <Link className="nav-link-velora" to="/rewards"><FiStar /> Rewards</Link>
              <span className="small text-slate-300">Hello, {user?.first_name || user?.name}</span>
              <button type="button" className="btn btn-link nav-link-velora p-0" onClick={signOut} aria-label="Sign out">Sign out</button>
            </>
          ) : <Link className="nav-link-velora d-flex gap-2 align-items-center" to="/login"><FiUser /> Sign in</Link>}
        </div>
        <button type="button" className="btn btn-icon d-lg-none" aria-label="Toggle menu" aria-expanded={open} onClick={() => setOpen(!open)}>
          {open ? <FiX /> : <FiMenu />}
        </button>
      </nav>
      {open && <div className="container pb-3 d-lg-none mobile-nav">
        <div className="py-2"><MarketSelector /></div>
        {navItems.map((item) => <NavLink key={item.to} className="nav-link-velora d-block py-2" onClick={() => setOpen(false)} to={item.to}>{item.label}</NavLink>)}
        <NavLink className="nav-link-velora d-block py-2" onClick={() => setOpen(false)} to="/wishlist">Wishlist{wishlistCount ? ` (${wishlistCount})` : ''}</NavLink>
        <NavLink className="nav-link-velora d-block py-2" onClick={() => setOpen(false)} to="/cart">Shopping bag{cartCount ? ` (${cartCount})` : ''}</NavLink>
        {isAuthenticated && <NavLink className="nav-link-velora d-block py-2" onClick={() => setOpen(false)} to="/orders">Orders</NavLink>}
        {isAuthenticated && <NavLink className="nav-link-velora d-block py-2" onClick={() => setOpen(false)} to="/rewards">Rewards & VIP</NavLink>}
        {isAuthenticated && <NavLink className="nav-link-velora d-block py-2" onClick={() => setOpen(false)} to="/notifications">Notifications</NavLink>}
        {isAuthenticated && <NavLink className="nav-link-velora d-block py-2" onClick={() => setOpen(false)} to="/ai">AI stylist</NavLink>}
        <NavLink className="nav-link-velora d-block py-2" onClick={() => setOpen(false)} to="/visual-search">Visual search</NavLink>
        <NavLink className="nav-link-velora d-block py-2" onClick={() => setOpen(false)} to={isAuthenticated ? '/account' : '/login'}>{isAuthenticated ? 'Account' : 'Sign in'}</NavLink>
      </div>}
    </header>
  )
}
