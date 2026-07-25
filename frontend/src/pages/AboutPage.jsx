import Card from '../components/common/Card'

export default function AboutPage() {
  return (
    <section className="container py-5 py-lg-6">
      <p className="eyebrow">THE VELORA STANDARD</p>
      <div className="row align-items-center g-5">
        <div className="col-lg-7"><h1 className="display-hero fs-1">Built for expressive, modern modest fashion.</h1><p className="hero-copy">The foundation deliberately excludes commerce and AI modules. It establishes the security, interfaces, visual system and role model those capabilities require.</p></div>
        <div className="col-lg-5"><Card className="p-4"><p className="text-slate-300 small mb-2">NEXT MODULES</p><ul className="mb-0 ps-3 text-slate-200"><li>Catalog & discovery</li><li>AI stylist & wardrobe</li><li>Creator commerce</li><li>International fulfilment</li></ul></Card></div>
      </div>
    </section>
  )
}
