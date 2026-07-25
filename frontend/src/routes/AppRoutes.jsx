import { lazy, Suspense } from 'react'
import { Route, Routes } from 'react-router-dom'
import PublicLayout from '../layouts/PublicLayout'
import GuestLayout from '../layouts/GuestLayout'
import HomePage from '../pages/HomePage'
import AboutPage from '../pages/AboutPage'
import LoginPage from '../pages/auth/LoginPage'
import RegisterPage from '../pages/auth/RegisterPage'
import StatusPage from '../pages/errors/StatusPage'
import OfflinePage from '../pages/errors/OfflinePage'
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
import MarketplacePage, { AnalyticsPage, CreatorDashboardPage, CreatorProfilePage } from '../pages/community/MarketplacePage'
import CommunityPage from '../pages/community/CommunityPage'
import { FollowersPage, SavedLooksPage, ShopTheLookPage } from '../pages/community/LookCollectionsPage'
import WardrobePage, { WardrobeDetailPage, WardrobeUploadPage } from '../pages/wardrobe/WardrobePage'
import VisualSearchPage from '../pages/search/VisualSearchPage'
import InternationalPage from '../pages/shopping/InternationalPage'
import { AchievementsPage, GiftCardsPage, NotificationsPage, ProductAlertsPage, ReferralPage, RewardsPage, VipPage, WalletPage } from '../pages/loyalty/LoyaltyPages'
import ErrorBoundary from '../components/system/ErrorBoundary'
import Loader from '../components/feedback/Loader'

const AdminPanel = lazy(() => import('../pages/admin/AdminPanel'))
const SupplierPortal = lazy(() => import('../pages/supplier/SupplierPortal'))

function LazyPanel({ children }) {
  return <Suspense fallback={<div className="container py-5"><Loader label="Loading workspace..." /></div>}>{children}</Suspense>
}

export default function AppRoutes() {
  return (
    <ErrorBoundary>
      <Routes>
        <Route element={<PublicLayout />}>
          <Route index element={<HomePage />} />
          <Route path="about" element={<AboutPage />} />
          <Route path="catalog" element={<CatalogPage />} />
          <Route path="catalog/:slug" element={<ProductDetailPage />} />
          <Route path="marketplace" element={<MarketplacePage />} />
          <Route path="creators/:handle" element={<CreatorProfilePage />} />
          <Route path="community" element={<CommunityPage />} />
          <Route path="looks/saved" element={<SavedLooksPage />} />
          <Route path="followers" element={<FollowersPage />} />
          <Route path="shop-the-look/:id" element={<ShopTheLookPage />} />
          <Route path="wardrobe" element={<WardrobePage />} />
          <Route path="wardrobe/upload" element={<WardrobeUploadPage />} />
          <Route path="wardrobe/:id" element={<WardrobeDetailPage />} />
          <Route path="visual-search" element={<VisualSearchPage />} />
          <Route path="international" element={<InternationalPage />} />
          <Route element={<ProtectedRoute />}>
            <Route path="account" element={<StatusPage code="ACCOUNT" title="Account foundation ready." description="Future profile modules plug in here." />} />
            <Route path="rewards" element={<RewardsPage />} />
            <Route path="vip" element={<VipPage />} />
            <Route path="referrals" element={<ReferralPage />} />
            <Route path="wallet" element={<WalletPage />} />
            <Route path="gift-cards" element={<GiftCardsPage />} />
            <Route path="achievements" element={<AchievementsPage />} />
            <Route path="notifications" element={<NotificationsPage />} />
            <Route path="alerts" element={<ProductAlertsPage />} />
            <Route path="cart" element={<CartPage />} />
            <Route path="wishlist" element={<WishlistPage />} />
            <Route path="checkout" element={<CheckoutPage />} />
            <Route path="orders" element={<OrdersPage />} />
            <Route path="orders/:id" element={<OrderDetailPage />} />
            <Route path="ai" element={<AiShell />}>
              <Route index element={<AiWelcomePage />} />
              <Route path="quiz" element={<StyleQuizPage />} />
              <Route path="profile" element={<StyleProfilePage />} />
              <Route path="chat" element={<StylistChatPage />} />
              <Route path="occasion" element={<OccasionFormPage />} />
              <Route path="budget" element={<BudgetFormPage />} />
              <Route path="saved" element={<SavedOutfitsPage />} />
              <Route path="history" element={<RecommendationHistoryPage />} />
            </Route>
            <Route path="creator/dashboard" element={<CreatorDashboardPage />} />
            <Route path="creator/analytics" element={<AnalyticsPage />} />
          </Route>
          <Route element={<ProtectedRoute roles={['super-admin', 'admin', 'supplier']} />}>
            <Route path="admin" element={<LazyPanel><AdminPanel /></LazyPanel>} />
            <Route path="admin/products" element={<LazyPanel><AdminPanel /></LazyPanel>} />
            <Route path="admin/orders" element={<LazyPanel><AdminPanel /></LazyPanel>} />
            <Route path="admin/customers" element={<LazyPanel><AdminPanel /></LazyPanel>} />
            <Route path="admin/analytics" element={<LazyPanel><AdminPanel /></LazyPanel>} />
            <Route path="admin/reports" element={<LazyPanel><AdminPanel /></LazyPanel>} />
            <Route path="admin/coupons" element={<LazyPanel><AdminPanel /></LazyPanel>} />
            <Route path="admin/support" element={<LazyPanel><AdminPanel /></LazyPanel>} />
            <Route path="admin/reviews" element={<LazyPanel><AdminPanel /></LazyPanel>} />
            <Route path="admin/suppliers" element={<LazyPanel><AdminPanel /></LazyPanel>} />
            <Route path="admin/creators" element={<LazyPanel><AdminPanel /></LazyPanel>} />
            <Route path="admin/notifications" element={<LazyPanel><AdminPanel /></LazyPanel>} />
            <Route path="admin/loyalty" element={<LazyPanel><AdminPanel /></LazyPanel>} />
            <Route path="admin/gift-cards" element={<LazyPanel><AdminPanel /></LazyPanel>} />
            <Route path="admin/alerts" element={<LazyPanel><AdminPanel /></LazyPanel>} />
            <Route path="admin/activity" element={<LazyPanel><AdminPanel /></LazyPanel>} />
            <Route path="admin/system" element={<LazyPanel><AdminPanel /></LazyPanel>} />
            <Route path="admin/operations" element={<LazyPanel><AdminPanel /></LazyPanel>} />
            <Route path="admin/settings" element={<LazyPanel><AdminPanel /></LazyPanel>} />
            <Route path="admin/international" element={<LazyPanel><AdminPanel /></LazyPanel>} />
            <Route path="admin/shipping" element={<OperationsPage />} />
            <Route path="admin/taxes" element={<OperationsPage />} />
            <Route path="admin/payments" element={<OperationsPage />} />
            <Route path="admin/returns" element={<OperationsPage />} />
          </Route>
          <Route element={<ProtectedRoute roles={['supplier']} />}>
            <Route path="supplier" element={<LazyPanel><SupplierPortal /></LazyPanel>} />
            <Route path="supplier/:section" element={<LazyPanel><SupplierPortal /></LazyPanel>} />
          </Route>
          <Route path="offline" element={<OfflinePage />} />
          <Route path="forbidden" element={<StatusPage code="403" title="Access restricted." description="Your account does not have permission to view this page." />} />
          <Route path="unauthorized" element={<StatusPage code="401" title="Sign in required." description="Please sign in to continue." />} />
          <Route path="session-expired" element={<StatusPage code="419" title="Session expired." description="Please refresh and sign in again." />} />
          <Route path="rate-limited" element={<StatusPage code="429" title="Too many requests." description="Please wait a moment and try again." />} />
          <Route path="error" element={<StatusPage code="500" title="An unexpected error occurred." description="Our team has been notified. Please try again shortly." />} />
          <Route path="maintenance" element={<StatusPage code="503" title="Refining the experience." description="Velora will return shortly." />} />
          <Route path="*" element={<StatusPage code="404" title="This look is unavailable." description="The page you requested does not exist." />} />
        </Route>
        <Route element={<GuestLayout />}>
          <Route path="login" element={<LoginPage />} />
          <Route path="register" element={<RegisterPage />} />
        </Route>
      </Routes>
    </ErrorBoundary>
  )
}
