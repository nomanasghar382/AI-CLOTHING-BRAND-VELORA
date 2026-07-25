import { memo } from 'react'
import { Link } from 'react-router-dom'
import Card from '../common/Card'
import CloudinaryImage from '../common/CloudinaryImage'

function ProductCard({ product }) {
  const image = product.images?.find((item) => item.is_primary)?.thumbnail_url || product.images?.[0]?.thumbnail_url
  return (
    <Card className="product-card h-100 overflow-hidden">
      <Link to={`/catalog/${product.slug}`} className="product-image-wrap">
        {image ? <CloudinaryImage src={image} alt={product.name} className="product-image" width={360} sizes="(max-width: 768px) 50vw, 20vw" /> : <div className="product-image product-image-fallback" aria-hidden="true" />}
        {product.discount_percent > 0 && <span className="badge product-badge">-{product.discount_percent}%</span>}
      </Link>
      <div className="p-3">
        <p className="eyebrow mb-1">{product.gender === 'men' ? "Men's" : "Women's"} · {product.brand?.name}</p>
        <Link className="product-name" to={`/catalog/${product.slug}`}>{product.name}</Link>
        <div className="mt-2">
          <span className="product-price">${Number(product.sale_price || product.price).toFixed(2)}</span>
          {product.sale_price && <del className="ms-2 text-slate-300">${Number(product.price).toFixed(2)}</del>}
        </div>
      </div>
    </Card>
  )
}

export default memo(ProductCard)
