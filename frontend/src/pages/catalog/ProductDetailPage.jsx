import { useCallback, useEffect, useMemo, useState } from 'react'
import { FiAlertCircle, FiFeather, FiHeart, FiShoppingBag } from 'react-icons/fi'
import { Link, useNavigate, useParams } from 'react-router-dom'
import Breadcrumb from '../../components/common/Breadcrumb'
import CloudinaryImage from '../../components/common/CloudinaryImage'
import Loader from '../../components/feedback/Loader'
import ErrorState from '../../components/feedback/ErrorState'
import Seo from '../../components/system/Seo'
import useAsyncAction from '../../hooks/useAsyncAction'
import { formatMoney } from '../../utils/format'
import { catalogService } from '../../services/catalogService'
import useAuth from '../../hooks/useAuth'
import useCart from '../../hooks/useCart'
import useWishlist from '../../hooks/useWishlist'

export default function ProductDetailPage() {
  const { slug } = useParams()
  const [product, setProduct] = useState(null)
  const [error, setError] = useState(false)
  const [selectedSize, setSelectedSize] = useState('')
  const { isAuthenticated } = useAuth()
  const { addItem: addToCart } = useCart()
  const { addItem: addToWishlist } = useWishlist()
  const navigate = useNavigate()
  const { busy, error: actionError, run } = useAsyncAction('We could not update your selection. Please try again.')

  const loadProduct = useCallback(() => {
    setError(false)
    setProduct(null)
    return catalogService.product(slug)
      .then(({ data }) => {
        setProduct(data.data)
        setSelectedSize(data.data?.sizes?.[0]?.name || '')
      })
      .catch(() => setError(true))
  }, [slug])

  useEffect(() => { loadProduct() }, [loadProduct])

  const jsonLd = useMemo(() => product ? {
    '@context': 'https://schema.org',
    '@type': 'Product',
    name: product.name,
    description: product.short_description || product.description,
    sku: product.sku,
    brand: product.brand?.name,
    offers: {
      '@type': 'Offer',
      priceCurrency: 'USD',
      price: product.sale_price || product.price,
      availability: product.stock_quantity > 0 ? 'https://schema.org/InStock' : 'https://schema.org/OutOfStock',
    },
  } : null, [product])

  if (error) {
    return <section className="container py-5"><ErrorState title="This piece is unavailable." onRetry={loadProduct} /></section>
  }
  if (!product) return <section className="container py-5"><Loader label="Opening the collection..." /></section>

  const primary = product.images?.find((image) => image.is_primary) || product.images?.[0]
  const perform = async (action) => {
    if (!isAuthenticated) {
      navigate('/login', { state: { from: { pathname: `/catalog/${slug}` } } })
      return
    }
    await run(action)
  }
  const selectedVariant = product.variants?.find((variant) => variant.size === selectedSize) || product.variants?.[0]
  const item = {
    product_id: product.id,
    quantity: 1,
    ...(selectedVariant?.id ? { product_variant_id: selectedVariant.id } : {}),
  }

  return (
    <section className="container py-4 py-lg-5">
      <Seo title={product.name} description={product.short_description || product.description} image={primary?.url} type="product" jsonLd={jsonLd} />
      <Breadcrumb items={[{ label: 'Catalog', to: '/catalog' }, { label: product.name }]} />
      <div className="row g-4">
        <div className="col-lg-7">
          <div className="detail-image-wrap">{primary && <CloudinaryImage src={primary.url} alt={product.name} className="detail-image" width={720} sizes="(max-width: 992px) 100vw, 60vw" fetchPriority="high" />}</div>
          <div className="d-flex gap-2 mt-3">{product.images?.slice(0, 5).map((image, index) => <CloudinaryImage key={index} className="detail-thumbnail" src={image.thumbnail_url || image.url} alt={image.alt_text || `${product.name} view ${index + 1}`} width={120} sizes="80px" />)}</div>
        </div>
        <div className="col-lg-5">
          <p className="eyebrow">{product.brand?.name} / Men&apos;s sport · {product.category?.name}</p>
          <h1 className="display-6">{product.name}</h1>
          <div className="my-3">
            <span className="product-price fs-3">{formatMoney(product.sale_price || product.price)}</span>
            {product.sale_price && <del className="ms-2 text-slate-300">{formatMoney(product.price)}</del>}
          </div>
          <p className="text-slate-300">{product.description}</p>
          <div className="spec-grid my-4">
            <span>Fabric <strong>{product.fabric}</strong></span>
            <span>Fit <strong>{product.fit_type}</strong></span>
            <span>Coverage <strong>{product.coverage_level}</strong></span>
            <span>SKU <strong>{product.sku}</strong></span>
          </div>
          <p className="small text-slate-300 mb-2">AVAILABLE COLORS</p>
          <div className="d-flex gap-2 mb-4">{product.colors?.map((color) => <button key={color.name} type="button" className="color-swatch border-0 p-0" style={{ background: color.hex_code }} aria-label={color.name} title={color.name} />)}</div>
          <p className="small text-slate-300 mb-2">SIZES</p>
          <div className="d-flex gap-2 flex-wrap">{product.sizes?.map((size) => <button className={`btn ${selectedSize === size.name ? 'btn-velora-primary' : 'btn-velora-secondary'}`} type="button" key={size.name} aria-pressed={selectedSize === size.name} onClick={() => setSelectedSize(size.name)}>{size.name}</button>)}</div>
          {actionError && <div className="alert alert-danger mt-3 mb-0" role="alert">{actionError}</div>}
          <div className="d-flex gap-2 mt-4 flex-wrap">
            <button className="btn btn-velora-primary flex-grow-1" type="button" disabled={busy} onClick={() => perform(() => addToCart(item))}><FiShoppingBag /> Add to bag</button>
            <button className="btn btn-velora-secondary" type="button" disabled={busy} onClick={() => perform(() => addToWishlist(item))} aria-label="Add to wishlist"><FiHeart /></button>
            <Link className="btn btn-velora-secondary" to={`/avatar?product=${product.slug}`}>Virtual try-on</Link>
          </div>
          <Link className="btn btn-velora-ghost mt-3" to="/catalog?gender=men">Continue exploring</Link>
        </div>
      </div>
      {product.matched_product && (
        <article className="product-passport mt-5">
          <div>
            <p className="eyebrow">MATCHING KICKS</p>
            <h2 className="h4">Complete the fit — same brand, same colorway.</h2>
            <p className="text-slate-300 mb-0">
              {product.catalog_line === 'footwear'
                ? 'This shoe pairs with the matching sport top below.'
                : 'Add the matching sport shoes built for this outfit.'}
            </p>
          </div>
          <div className="row g-3 align-items-center mt-3">
            <div className="col-md-3">
              <CloudinaryImage
                src={product.matched_product.images?.find((img) => img.is_primary)?.thumbnail_url || product.matched_product.images?.[0]?.thumbnail_url}
                alt={product.matched_product.name}
                className="detail-thumbnail w-100"
                width={240}
                sizes="240px"
              />
            </div>
            <div className="col-md-6">
              <p className="eyebrow mb-1">{product.matched_product.brand?.name}</p>
              <h3 className="h5 mb-2">{product.matched_product.name}</h3>
              <span className="product-price">{formatMoney(product.matched_product.sale_price || product.matched_product.price)}</span>
            </div>
            <div className="col-md-3">
              <Link className="btn btn-velora-primary w-100" to={`/catalog/${product.matched_product.slug}`}>View match</Link>
            </div>
          </div>
        </article>
      )}
      <article className="product-passport mt-5">
        <div><p className="eyebrow">DIGITAL PRODUCT PASSPORT</p><h2><FiFeather /> Made to be known.</h2><p>Trace the materials, care and afterlife of this piece in one transparent record.</p></div>
        <div className="passport-grid"><span>Materials<strong>{product.fabric || 'Details forthcoming'}</strong></span><span>Origin<strong>{product.origin || product.country_of_origin || 'Verified at fulfillment'}</strong></span><span>Care<strong>{product.care_instructions || 'Follow garment label'}</strong></span><span>End of life<strong>{product.recycling_guidance || 'Repair, resell or recycle responsibly'}</strong></span></div>
        <Link className="btn btn-velora-secondary" to="/alerts"><FiAlertCircle /> Set price or restock alert</Link>
      </article>
    </section>
  )
}
