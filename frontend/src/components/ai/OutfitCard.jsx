import { FiBookmark, FiCheck, FiX } from 'react-icons/fi'
import Button from '../common/Button'

export default function OutfitCard({ outfit, saved, onSave, onRemove }) {
  return (
    <article className="velora-card p-4 outfit-card">
      <div className="d-flex justify-content-between gap-3 mb-3"><div><p className="eyebrow mb-1">{outfit.occasion} / {outfit.budget}</p><h3 className="h5 mb-0">{outfit.title}</h3></div>{outfit.fallback && <span className="ai-fallback-pill"><FiCheck /> Catalog fallback</span>}</div>
      <p className="text-slate-300">{outfit.description}</p>
      <ul className="outfit-pieces mb-4">{(outfit.pieces || []).map((piece) => <li key={piece}>{piece}</li>)}</ul>
      {onSave && <Button variant="secondary" onClick={() => onSave(outfit)} disabled={saved}>{saved ? <><FiCheck /> Saved</> : <><FiBookmark /> Save look</>}</Button>}
      {onRemove && <Button variant="ghost" onClick={() => onRemove(outfit.id)}><FiX /> Remove</Button>}
    </article>
  )
}
