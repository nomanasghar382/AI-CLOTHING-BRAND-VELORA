import { useRef, useState } from 'react'
import { FiCamera, FiChevronRight, FiPlus, FiZap } from 'react-icons/fi'
import { menModestImage, womenModestImage } from '../../constants/modestFashionImages'

const initialPieces = [
  { id: 1, name: 'Black flowing abaya', category: 'Abayas', image: womenModestImage(4, 550) },
  { id: 2, name: 'Silk hijab & niqab set', category: 'Hijabs', image: womenModestImage(0, 550) },
  { id: 3, name: 'Premium white thobe', category: 'Thobes', image: menModestImage(1, 550) },
  { id: 4, name: 'Brown kandura', category: 'Kandura', image: menModestImage(2, 550) },
]

export function WardrobeDetailPage() {
  const piece = initialPieces[0]
  return <section className="container py-5 feature-page"><button className="back-link" onClick={() => window.history.back()}>← Back to wardrobe</button><div className="row g-4 mt-1"><div className="col-md-6"><img className="wardrobe-detail-image" src={piece.image} alt={piece.name} /></div><div className="col-md-6"><p className="eyebrow">YOUR WARDROBE / {piece.category}</p><h1>{piece.name}</h1><p className="text-slate-300">Added May 2026 · Worn 7 times</p><div className="velora-card p-4 mt-4"><p className="eyebrow">AI STYLING NOTES</p><p className="mb-0">Pair with your silk hijab & niqab set for complete coverage, or layer under a flowing abaya for a tonal silhouette.</p></div></div></div></section>
}

export function WardrobeUploadPage() {
  const input = useRef(); const [fileName, setFileName] = useState('')
  return <section className="container py-5 feature-page narrow-page"><p className="eyebrow">ADD TO WARDROBE</p><h1>Give your favorite pieces a home.</h1><p className="text-slate-300">Upload a photo and we’ll help organize the details.</p><button className="upload-dropzone mt-3" onClick={() => input.current.click()}><FiCamera /><strong>{fileName || 'Choose a garment photo'}</strong><small>JPG, PNG, or HEIC · up to 10 MB</small></button><input ref={input} type="file" accept="image/*" hidden onChange={(event) => setFileName(event.target.files?.[0]?.name || '')} /><button className="btn btn-velora-primary mt-3" disabled={!fileName}>Continue to details <FiChevronRight /></button></section>
}

export default function WardrobePage() {
  const [pieces, setPieces] = useState(initialPieces); const [suggestions, setSuggestions] = useState(false)
  return <section className="container py-5 feature-page"><div className="d-flex justify-content-between align-items-end gap-3 mb-4"><div><p className="eyebrow">YOUR DIGITAL WARDROBE</p><h1>Everything you already love.</h1><p className="text-slate-300 mb-0">{pieces.length} pieces, organized for real life.</p></div><a className="btn btn-velora-primary" href="/wardrobe/upload"><FiPlus /> Add piece</a></div><div className="wardrobe-toolbar"><button className={suggestions ? 'active' : ''} onClick={() => setSuggestions(!suggestions)}><FiZap /> AI suggestions</button>{['All pieces', 'Outerwear', 'Shirts', 'Shoes'].map((filter) => <button key={filter}>{filter}</button>)}</div>{suggestions && <div className="ai-suggestion velora-card p-4 mb-4"><FiZap /><div><strong>Wear the ink wool coat tomorrow.</strong><p className="mb-0 text-slate-300">It completes 3 unworn combinations in your closet.</p></div><button className="btn btn-velora-secondary">Build look</button></div>}<div className="wardrobe-grid">{pieces.map((piece) => <a href={`/wardrobe/${piece.id}`} className="wardrobe-piece" key={piece.id}><img src={piece.image} alt={piece.name} /><div><p>{piece.category}</p><strong>{piece.name}</strong></div></a>)}<button className="wardrobe-add" onClick={() => setPieces((current) => [...current, { id: Date.now(), name: 'New wardrobe piece', category: 'Unsorted', image: initialPieces[1].image }])}><FiPlus /> Add another piece</button></div></section>
}
