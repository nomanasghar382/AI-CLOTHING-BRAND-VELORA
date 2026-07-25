import { Outlet } from 'react-router-dom'
import Sidebar from '../layout/Sidebar'

const items = [
  { label: 'AI stylist', to: '/ai' },
  { label: 'Style quiz', to: '/ai/quiz' },
  { label: 'My profile', to: '/ai/profile' },
  { label: 'Occasion edit', to: '/ai/occasion' },
  { label: 'Budget edit', to: '/ai/budget' },
  { label: 'Saved outfits', to: '/ai/saved' },
  { label: 'Recommendation history', to: '/ai/history' },
]

export default function AiShell() {
  return (
    <section className="container py-4 py-lg-5">
      <div className="mb-4"><p className="eyebrow">VELORA / PERSONAL STYLING</p><h1 className="h2 mb-0">Your style atelier</h1></div>
      <div className="row g-4">
        <aside className="col-lg-3"><Sidebar items={items} /></aside>
        <div className="col-lg-9"><Outlet /></div>
      </div>
    </section>
  )
}
