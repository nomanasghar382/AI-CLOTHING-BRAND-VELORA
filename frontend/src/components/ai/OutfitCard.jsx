import { Link } from 'react-router-dom'
import { FiBookmark, FiCheck, FiExternalLink, FiX } from 'react-icons/fi'
import Button from '../common/Button'
import { formatCurrency } from '../../utils/format'

export default function OutfitCard({ outfit, saved, onSave, onRemove }) {
  const products = outfit.products || []

  return (
    <article className="velora-card p-4 outfit-card">
      <div className="d-flex justify-content-between gap-3 mb-3">
        <div>
          <p className="eyebrow mb-1">{outfit.occasion} / {outfit.budget}</p>
          <h3 className="h5 mb-0">{outfit.title}</h3>
        </div>
        {outfit.fallback && <span className="ai-fallback-pill"><FiCheck /> Catalog fallback</span>}
      </div>
      <p className="text-slate-300">{outfit.description}</p>
      {products.length > 0 ? (
        <div className="row g-2 mb-4">
          {products.map((product) => (
            <div className="col-6 col-md-3" key={product.product_id || product.name}>
              <div className="outfit-product-tile">
                {product.image_url && <img src={product.image_url} alt={product.name} />}
                <div className="p-2">
                  <p className="small mb-1">{product.catalog_line === 'footwear' ? 'Kicks' : 'Sport fit'}</p>
                  <strong className="small d-block">{product.name}</strong>
                  {product.price != null && <span className="small text-slate-300">{formatCurrency(product.price)}</span>}
                  {product.slug && (
                    <Link className="btn btn-velora-ghost p-0 small" to={`/catalog/${product.slug}`}>
                      View <FiExternalLink />
                    </Link>
                  )}
                </div>
              </div>
            </div>
          ))}
        </div>
      ) : (
        <ul className="outfit-pieces mb-4">{(outfit.pieces || []).map((piece) => <li key={piece}>{piece}</li>)}</ul>
      )}
      {onSave && <Button variant="secondary" onClick={() => onSave(outfit)} disabled={saved}>{saved ? <><FiCheck /> Saved</> : <><FiBookmark /> Save look</>}</Button>}
      {onRemove && <Button variant="ghost" onClick={() => onRemove(outfit.id)}><FiX /> Remove</Button>}
    </article>
  )
}
