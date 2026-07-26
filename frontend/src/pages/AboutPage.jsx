import Card from '../components/common/Card'

export default function AboutPage() {
  return (
    <section className="container py-5 py-lg-6">
      <p className="eyebrow">THE VELORA STANDARD</p>
      <div className="row align-items-center g-5">
        <div className="col-lg-7"><h1 className="display-hero fs-1">One niche: gym-to-street.</h1><p className="hero-copy">VELORA builds accurate full fits for Gen Z guys — training gear that still hits on the street, always with matching kicks. No mall. No noise. Just the grind.</p></div>
        <div className="col-lg-5"><Card className="p-4"><p className="text-slate-300 small mb-2">THE STACK</p><ul className="mb-0 ps-3 text-slate-200"><li>Build my fit (AI)</li><li>Avatar try-on</li><li>Gym-to-street catalog</li><li>Matching sneakers every look</li></ul></Card></div>
      </div>
    </section>
  )
}
