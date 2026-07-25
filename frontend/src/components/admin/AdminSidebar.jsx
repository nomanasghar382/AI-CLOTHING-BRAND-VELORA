import { NavLink } from 'react-router-dom'
import {
  FiActivity, FiBarChart2, FiBell, FiBox, FiCreditCard, FiGift, FiHelpCircle,
  FiHome, FiSettings, FiShoppingBag, FiStar, FiTruck, FiUsers,
} from 'react-icons/fi'

const items = [
  ['Dashboard', '', FiHome], ['Products', 'products', FiBox], ['Orders', 'orders', FiShoppingBag],
  ['Customers', 'customers', FiUsers], ['Analytics', 'analytics', FiBarChart2], ['Reports', 'reports', FiActivity],
  ['Coupons', 'coupons', FiGift], ['Support', 'support', FiHelpCircle], ['Reviews', 'reviews', FiStar],
  ['Suppliers', 'suppliers', FiTruck], ['Creators', 'creators', FiUsers], ['Notifications', 'notifications', FiBell],
  ['Activity logs', 'activity', FiActivity], ['Settings', 'settings', FiSettings],
]

export default function AdminSidebar() {
  return <aside className="admin-sidebar">
    <NavLink className="admin-brand" to="/admin"><span>V</span>ELORA</NavLink>
    <p className="admin-sidebar-label">CONTROL ROOM</p>
    <nav className="admin-nav">
      {items.map(([label, slug, Icon]) => <NavLink end={!slug} key={label} to={slug ? `/admin/${slug}` : '/admin'} className="admin-nav-link">
        <Icon /><span>{label}</span>
      </NavLink>)}
    </nav>
    <div className="admin-sidebar-footer"><FiCreditCard /> Secure operations</div>
  </aside>
}
