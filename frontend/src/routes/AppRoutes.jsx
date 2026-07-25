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
import CartPage from '../pages/shopping/CartPage'
import WishlistPage from '../pages/shopping/WishlistPage'
import CheckoutPage from '../pages/shopping/CheckoutPage'
import OrdersPage from '../pages/shopping/OrdersPage'
import OrderDetailPage from '../pages/shopping/OrderDetailPage'
import OperationsPage from '../pages/admin/OperationsPage'
import AiShell from '../components/ai/AiShell'
import AiWelcomePage from '../pages/ai/AiWelcomePage'
import StyleQuizPage from '../pages/ai/StyleQuizPage'
import StyleProfilePage from '../pages/ai/StyleProfilePage'
import StylistChatPage from '../pages/ai/StylistChatPage'
import OccasionFormPage from '../pages/ai/OccasionFormPage'
import BudgetFormPage from '../pages/ai/BudgetFormPage'
import SavedOutfitsPage from '../pages/ai/SavedOutfitsPage'
import RecommendationHistoryPage from '../pages/ai/RecommendationHistoryPage'

export default function AppRoutes() {
  return <Routes>
    <Route element={<PublicLayout />}><Route index element={<HomePage />} /><Route path="about" element={<AboutPage />} /><Route path="catalog" element={<CatalogPage />} /><Route path="catalog/:slug" element={<ProductDetailPage />} />
      <Route element={<ProtectedRoute />}><Route path="account" element={<StatusPage code="ACCOUNT" title="Account foundation ready." description="Future profile modules plug in here." />} /><Route path="cart" element={<CartPage />} /><Route path="wishlist" element={<WishlistPage />} /><Route path="checkout" element={<CheckoutPage />} /><Route path="orders" element={<OrdersPage />} /><Route path="orders/:id" element={<OrderDetailPage />} />
        <Route path="ai" element={<AiShell />}><Route index element={<AiWelcomePage />} /><Route path="quiz" element={<StyleQuizPage />} /><Route path="profile" element={<StyleProfilePage />} /><Route path="chat" element={<StylistChatPage />} /><Route path="occasion" element={<OccasionFormPage />} /><Route path="budget" element={<BudgetFormPage />} /><Route path="saved" element={<SavedOutfitsPage />} /><Route path="history" element={<RecommendationHistoryPage />} /></Route>
      </Route>
      <Route element={<ProtectedRoute roles={['super-admin', 'admin', 'supplier']} />}><Route path="admin/orders" element={<OperationsPage />} /><Route path="admin/coupons" element={<OperationsPage />} /><Route path="admin/shipping" element={<OperationsPage />} /><Route path="admin/taxes" element={<OperationsPage />} /><Route path="admin/payments" element={<OperationsPage />} /><Route path="admin/returns" element={<OperationsPage />} /></Route>
      <Route path="forbidden" element={<StatusPage code="403" title="Access restricted." description="Your account does not have permission to view this page." />} />
      <Route path="error" element={<StatusPage code="500" title="An unexpected error occurred." description="Our team has been notified. Please try again shortly." />} />
      <Route path="maintenance" element={<StatusPage code="MAINTENANCE" title="Refining the experience." description="Velora will return shortly." />} />
      <Route path="*" element={<StatusPage code="404" title="This look is unavailable." description="The page you requested does not exist." />} />
    </Route>
    <Route element={<GuestLayout />}><Route path="login" element={<LoginPage />} /><Route path="register" element={<RegisterPage />} /></Route>
  </Routes>
}
