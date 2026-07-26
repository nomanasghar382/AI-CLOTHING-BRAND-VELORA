import { Outlet, useLocation } from 'react-router-dom'
import Navbar from '../components/layout/Navbar'
import BackendStatusBanner from '../components/system/BackendStatusBanner'
import Footer from '../components/layout/Footer'
import SkipLink from '../components/accessibility/SkipLink'
import PageTransition from '../components/system/PageTransition'
import ToastStack from '../components/notifications/ToastStack'
import useOfflineSync from '../hooks/useOfflineSync'

export default function PublicLayout() {
  const location = useLocation()
  useOfflineSync()

  return (
    <div className="app-shell genz-sport">
      <SkipLink />
      <BackendStatusBanner />
      <Navbar />
      <main id="main-content" className="flex-grow-1" tabIndex={-1}>
        <PageTransition key={location.pathname}>
          <Outlet />
        </PageTransition>
      </main>
      <Footer />
      <ToastStack />
    </div>
  )
}
