import { FEATURED_EDITORIAL } from '../../constants/veloraIcons'

const sportImage = (index, width = 550) => FEATURED_EDITORIAL[index % FEATURED_EDITORIAL.length].image.replace(/w=\d+/, `w=${width}`)

const wardrobePieces = [
  { id: 1, name: 'Nike Tech Fleece Hoodie', category: 'Hoodies', image: sportImage(2) },
  { id: 2, name: 'Gymshark Vital Seamless Tee', category: 'Training tops', image: sportImage(2) },
  { id: 3, name: 'Jordan 1 Mid Heat', category: 'Sneakers', image: sportImage(3) },
  { id: 4, name: 'Adidas Tiro Track Pants', category: 'Track pants', image: sportImage(1) },
]

export default function WardrobePage() {
  return (
    <section className="container py-5 feature-page">
      <p className="eyebrow">YOUR SPORT WARDROBE</p>
      <h1>Track what you already own.</h1>
      <p className="text-slate-300">Upload gym fits and sneakers — your AI designer uses them to build better outfits.</p>
      <div className="row g-3 mt-4">
        {wardrobePieces.map((piece) => (
          <div className="col-6 col-md-3" key={piece.id}>
            <article className="wardrobe-piece velora-card overflow-hidden h-100">
              <img src={piece.image} alt={piece.name} />
              <div className="p-3">
                <p className="eyebrow mb-1">{piece.category}</p>
                <h2 className="h6 mb-0">{piece.name}</h2>
              </div>
            </article>
          </div>
        ))}
      </div>
    </section>
  )
}

export function WardrobeDetailPage() {
  const piece = wardrobePieces[0]
  return (
    <section className="container py-5 feature-page">
      <button className="back-link" type="button" onClick={() => window.history.back()}>← Back to wardrobe</button>
      <div className="row g-4 mt-1">
        <div className="col-md-6"><img className="wardrobe-detail-image" src={piece.image} alt={piece.name} /></div>
        <div className="col-md-6">
          <p className="eyebrow">YOUR WARDROBE / {piece.category}</p>
          <h1>{piece.name}</h1>
          <p className="text-slate-300">Added May 2026 · Worn 12 times</p>
          <div className="velora-card p-4 mt-4">
            <p className="eyebrow">AI STYLING NOTES</p>
            <p className="mb-0">Pair with your Jordan 1 Mid Heat for court days, or layer under a Nike track jacket for outdoor runs.</p>
          </div>
        </div>
      </div>
    </section>
  )
}

export function WardrobeUploadPage() {
  return (
    <section className="container py-5 feature-page narrow-page">
      <p className="eyebrow">UPLOAD PIECE</p>
      <h1>Add to your sport wardrobe.</h1>
      <p className="text-slate-300">Snap a photo of your hoodie, joggers, or sneakers to get better AI outfit recommendations.</p>
    </section>
  )
}
