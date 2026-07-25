import { Link } from 'react-router-dom'
import Seo from '../../components/system/Seo'
import Button from '../../components/common/Button'

export default function OfflinePage() {
  return (
  <>
    <Seo title="Offline" description="You are currently offline. VELORA will reconnect when your connection returns." />
    <section className="container py-5 text-center">
      <p className="eyebrow">OFFLINE</p>
      <h1 className="display-hero fs-1">You are offline.</h1>
      <p className="hero-copy mx-auto">Saved cart, wishlist, and recent orders remain available when cached by your device.</p>
      <Button onClick={() => window.location.reload()}>Try again</Button>
      <div className="mt-3"><Link className="nav-link-velora" to="/">Return home</Link></div>
    </section>
  </>
  )
}
