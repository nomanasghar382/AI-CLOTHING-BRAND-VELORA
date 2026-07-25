import { Route, Routes } from 'react-router-dom'
import PublicLayout from '../layouts/PublicLayout'
import GuestLayout from '../layouts/GuestLayout'
import HomePage from '../pages/HomePage'
import AboutPage from '../pages/AboutPage'
import LoginPage from '../pages/auth/LoginPage'
import RegisterPage from '../pages/auth/RegisterPage'
import StatusPage from '../pages/errors/StatusPage'
import CatalogPage from '../pages/catalog/CatalogPage'
import ProductDetailPage from '../pages/catalog/ProductDetailPage'
import ProtectedRoute from './ProtectedRoute'

export default function AppRoutes() {
  return <Routes>
    <Route element={<PublicLayout />}><Route index element={<HomePage />} /><Route path="about" element={<AboutPage />} /><Route path="catalog" element={<CatalogPage />} /><Route path="catalog/:slug" element={<ProductDetailPage />} />
      <Route element={<ProtectedRoute />}><Route path="account" element={<StatusPage code="ACCOUNT" title="Account foundation ready." description="Future profile modules plug in here." />} /></Route>
      <Route path="forbidden" element={<StatusPage code="403" title="Access restricted." description="Your account does not have permission to view this page." />} />
      <Route path="error" element={<StatusPage code="500" title="An unexpected error occurred." description="Our team has been notified. Please try again shortly." />} />
      <Route path="maintenance" element={<StatusPage code="MAINTENANCE" title="Refining the experience." description="Velora will return shortly." />} />
      <Route path="*" element={<StatusPage code="404" title="This look is unavailable." description="The page you requested does not exist." />} />
    </Route>
    <Route element={<GuestLayout />}><Route path="login" element={<LoginPage />} /><Route path="register" element={<RegisterPage />} /></Route>
  </Routes>
}
