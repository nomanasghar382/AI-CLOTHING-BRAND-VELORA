import { useEffect, useState } from 'react'
import { FiHeart, FiShoppingBag } from 'react-icons/fi'
import { Link, useNavigate, useParams } from 'react-router-dom'
import Breadcrumb from '../../components/common/Breadcrumb'
import Loader from '../../components/feedback/Loader'
import ErrorState from '../../components/feedback/ErrorState'
import { catalogService } from '../../services/catalogService'
import useAuth from '../../hooks/useAuth'
import useCart from '../../hooks/useCart'
import useWishlist from '../../hooks/useWishlist'

export default function ProductDetailPage() {
  const { slug } = useParams(); const [product, setProduct] = useState(null); const [error, setError] = useState(false)
  const [actionError, setActionError] = useState(''); const [busy, setBusy] = useState(false)
  const { isAuthenticated } = useAuth(); const { addItem: addToCart } = useCart(); const { addItem: addToWishlist } = useWishlist(); const navigate = useNavigate()
  useEffect(() => { catalogService.product(slug).then(({ data }) => setProduct(data.data)).catch(() => setError(true)) }, [slug])
  if (error) return <section className="container py-5"><ErrorState title="This piece is unavailable." /></section>
  if (!product) return <section className="container py-5"><Loader label="Opening the collection..." /></section>
  const primary = product.images?.find((image) => image.is_primary) || product.images?.[0]
  const perform = async (action) => { if (!isAuthenticated) return navigate('/login', { state: { from: { pathname: `/catalog/${slug}` } } }); setBusy(true); setActionError(''); try { await action() } catch (requestError) { setActionError(requestError.response?.data?.message || 'We could not update your selection. Please try again.') } finally { setBusy(false) } }
  const item = { product_id: product.id, quantity: 1 }
  return <section className="container py-4 py-lg-5"><Breadcrumb items={[{ label: 'Catalog', to: '/catalog' }, { label: product.name }]} /><div className="row g-4"><div className="col-lg-7"><div className="detail-image-wrap">{primary && <img src={primary.url} alt={product.name} className="detail-image" />}</div><div className="d-flex gap-2 mt-3">{product.images?.slice(0, 5).map((image, index) => <img key={index} className="detail-thumbnail" src={image.thumbnail_url} alt="" />)}</div></div><div className="col-lg-5"><p className="eyebrow">{product.brand?.name} / {product.category?.name}</p><h1 className="display-6">{product.name}</h1><div className="my-3"><span className="product-price fs-3">${Number(product.sale_price || product.price).toFixed(2)}</span>{product.sale_price && <del className="ms-2 text-slate-300">${Number(product.price).toFixed(2)}</del>}</div><p className="text-slate-300">{product.description}</p><div className="spec-grid my-4"><span>Fabric <strong>{product.fabric}</strong></span><span>Fit <strong>{product.fit_type}</strong></span><span>Coverage <strong>{product.coverage_level}</strong></span><span>SKU <strong>{product.sku}</strong></span></div><p className="small text-slate-300 mb-2">AVAILABLE COLORS</p><div className="d-flex gap-2 mb-4">{product.colors?.map((color) => <span key={color.name} title={color.name} className="color-swatch" style={{ background: color.hex_code }} />)}</div><p className="small text-slate-300 mb-2">SIZES</p><div className="d-flex gap-2 flex-wrap">{product.sizes?.map((size) => <button className="btn btn-velora-secondary" type="button" key={size.name}>{size.name}</button>)}</div>{actionError && <div className="alert alert-danger mt-3 mb-0">{actionError}</div>}<div className="d-flex gap-2 mt-4"><button className="btn btn-velora-primary flex-grow-1" type="button" disabled={busy} onClick={() => perform(() => addToCart(item))}><FiShoppingBag /> Add to bag</button><button className="btn btn-velora-secondary" type="button" disabled={busy} onClick={() => perform(() => addToWishlist(item))} aria-label="Add to wishlist"><FiHeart /></button></div><Link className="btn btn-velora-ghost mt-3" to="/catalog">Continue exploring</Link></div></div></section>
}
