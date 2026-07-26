import Card from '../components/common/Card'

export default function AboutPage() {
  return (
    <section className="container py-5 py-lg-6">
      <p className="eyebrow">THE VELORA STANDARD</p>
      <div className="row align-items-center g-5">
        <div className="col-lg-7"><h1 className="display-hero fs-1">Built to be Gen Z&apos;s #1 men&apos;s sportswear destination.</h1><p className="hero-copy">VELORA Sport brings Nike, Adidas, Puma, Jordan and 40+ brands together with avatar try-on, voice &amp; image search, and an AI fashion designer that builds full outfits with matching kicks.</p></div>
        <div className="col-lg-5"><Card className="p-4"><p className="text-slate-300 small mb-2">PLATFORM FEATURES</p><ul className="mb-0 ps-3 text-slate-200"><li>1M+ SKU sport catalog</li><li>Avatar &amp; virtual try-on</li><li>Voice, text &amp; image search</li><li>AI outfit designer + matching sneakers</li></ul></Card></div>
      </div>
    </section>
  )
}
