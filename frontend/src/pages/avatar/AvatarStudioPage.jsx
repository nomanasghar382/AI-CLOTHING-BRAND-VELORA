import { useCallback, useEffect, useMemo, useState } from 'react'
import { Link, useSearchParams } from 'react-router-dom'
import { FiArrowUpRight, FiSave, FiUser } from 'react-icons/fi'
import Seo from '../../components/system/Seo'
import Button from '../../components/common/Button'
import Input from '../../components/common/Input'
import Loader from '../../components/feedback/Loader'
import useAuth from '../../hooks/useAuth'
import { catalogService } from '../../services/catalogService'
import { styleService } from '../../services/styleService'

const skinTones = ['#f5d0b5', '#e8b796', '#c68642', '#8d5524', '#5c3a21']
const hairStyles = ['fade', 'curly', 'waves', 'buzz', 'locs']
const fits = ['slim', 'regular', 'oversized', 'athletic']

export default function AvatarStudioPage() {
  const { isAuthenticated } = useAuth()
  const [searchParams] = useSearchParams()
  const [loading, setLoading] = useState(true)
  const [saving, setSaving] = useState(false)
  const [products, setProducts] = useState([])
  const [selectedTop, setSelectedTop] = useState(null)
  const [selectedShoes, setSelectedShoes] = useState(null)
  const [avatar, setAvatar] = useState({
    height: 178,
    height_unit: 'cm',
    fit_preference: 'athletic',
    size_preferences: { tops: 'M', bottoms: 'M', shoes: 'US 10' },
    measurements: { skin_tone: skinTones[1], hair_style: hairStyles[0] },
  })

  useEffect(() => {
    const load = async () => {
      try {
        const [{ data: apparel }, { data: footwear }] = await Promise.all([
          catalogService.products({ gender: 'men', line: 'apparel', per_page: 12 }),
          catalogService.products({ gender: 'men', line: 'footwear', per_page: 12 }),
        ])
        const tops = apparel.data.items || []
        const shoes = footwear.data.items || []
        setProducts([...tops, ...shoes])
        const presetSlug = searchParams.get('product')
        const preset = presetSlug ? [...tops, ...shoes].find((item) => item.slug === presetSlug) : tops[0]
        if (preset?.catalog_line === 'footwear') setSelectedShoes(preset)
        else if (preset) {
          setSelectedTop(preset)
          if (preset.matched_product) setSelectedShoes(preset.matched_product)
        }
        if (isAuthenticated) {
          const { data } = await styleService.getBodyProfile()
          if (data.data) {
            setAvatar((current) => ({
              ...current,
              ...data.data,
              size_preferences: data.data.size_preferences || current.size_preferences,
              measurements: data.data.measurements || current.measurements,
            }))
          }
        }
      } finally {
        setLoading(false)
      }
    }
    load()
  }, [isAuthenticated, searchParams])

  const apparel = useMemo(() => products.filter((item) => item.catalog_line === 'apparel'), [products])
  const footwear = useMemo(() => products.filter((item) => item.catalog_line === 'footwear'), [products])

  const saveAvatar = useCallback(async () => {
    if (!isAuthenticated) return
    setSaving(true)
    try {
      await styleService.updateBodyProfile(avatar)
    } finally {
      setSaving(false)
    }
  }, [avatar, isAuthenticated])

  const tryOnTop = (product) => {
    setSelectedTop(product)
    if (product.matched_product) setSelectedShoes(product.matched_product)
  }

  if (loading) return <div className="container py-5"><Loader label="Building your avatar studio..." /></div>

  return (
    <>
      <Seo title="Avatar & Virtual Try-On" description="Create your sport avatar and virtually try on Nike, Adidas, Puma and more." />
      <section className="container py-5 feature-page">
        <div className="row g-4 align-items-start">
          <div className="col-lg-5">
            <p className="eyebrow">AVATAR STUDIO</p>
            <h1 className="h2">Your sport avatar.<br />Virtual try-on.</h1>
            <p className="text-slate-300">Build a body profile, pick a fit, and preview men&apos;s sportswear + matching kicks — built for Gen Z athletes 16–35.</p>

            <div className="velora-card p-4 avatar-preview mt-4">
              <div className="avatar-stage" style={{ '--skin-tone': avatar.measurements?.skin_tone || skinTones[1] }}>
                <div className="avatar-head" data-hair={avatar.measurements?.hair_style || 'fade'} />
                <div className="avatar-torso">
                  {selectedTop?.images?.[0]?.url && <img src={selectedTop.images[0].url} alt={selectedTop.name} className="avatar-garment avatar-garment-top" />}
                </div>
                <div className="avatar-legs" />
                <div className="avatar-feet">
                  {selectedShoes?.images?.[0]?.url && <img src={selectedShoes.images[0].url} alt={selectedShoes.name} className="avatar-garment avatar-garment-shoes" />}
                </div>
              </div>
              <p className="small text-slate-300 mt-3 mb-0 text-center">
                {selectedTop ? selectedTop.name : 'Pick apparel'} · {selectedShoes ? selectedShoes.name : 'Pick sneakers'}
              </p>
            </div>
          </div>

          <div className="col-lg-7">
            <div className="velora-card p-4 mb-4">
              <h2 className="h5 d-flex align-items-center gap-2"><FiUser /> Avatar builder</h2>
              <div className="row g-3 mt-1">
                <div className="col-md-4">
                  <Input label="Height (cm)" type="number" value={avatar.height} onChange={(e) => setAvatar((c) => ({ ...c, height: Number(e.target.value), height_unit: 'cm' }))} />
                </div>
                <div className="col-md-4">
                  <label className="form-label text-slate-200" htmlFor="avatar-fit">Fit</label>
                  <select id="avatar-fit" className="form-select velora-input" value={avatar.fit_preference} onChange={(e) => setAvatar((c) => ({ ...c, fit_preference: e.target.value }))}>
                    {fits.map((fit) => <option key={fit} value={fit}>{fit}</option>)}
                  </select>
                </div>
                <div className="col-md-4">
                  <label className="form-label text-slate-200" htmlFor="avatar-top-size">Top size</label>
                  <select id="avatar-top-size" className="form-select velora-input" value={avatar.size_preferences?.tops || 'M'} onChange={(e) => setAvatar((c) => ({ ...c, size_preferences: { ...c.size_preferences, tops: e.target.value } }))}>
                    {['XS', 'S', 'M', 'L', 'XL', 'XXL'].map((size) => <option key={size} value={size}>{size}</option>)}
                  </select>
                </div>
                <div className="col-md-6">
                  <label className="form-label text-slate-200">Skin tone</label>
                  <div className="d-flex gap-2 flex-wrap">
                    {skinTones.map((tone) => (
                      <button key={tone} type="button" className={`avatar-swatch ${avatar.measurements?.skin_tone === tone ? 'active' : ''}`} style={{ background: tone }} onClick={() => setAvatar((c) => ({ ...c, measurements: { ...c.measurements, skin_tone: tone } }))} aria-label="Skin tone" />
                    ))}
                  </div>
                </div>
                <div className="col-md-6">
                  <label className="form-label text-slate-200" htmlFor="avatar-hair">Hair style</label>
                  <select id="avatar-hair" className="form-select velora-input" value={avatar.measurements?.hair_style || 'fade'} onChange={(e) => setAvatar((c) => ({ ...c, measurements: { ...c.measurements, hair_style: e.target.value } }))}>
                    {hairStyles.map((style) => <option key={style} value={style}>{style}</option>)}
                  </select>
                </div>
              </div>
              {isAuthenticated ? (
                <Button className="mt-3" variant="secondary" onClick={saveAvatar} disabled={saving}><FiSave /> {saving ? 'Saving…' : 'Save avatar'}</Button>
              ) : (
                <p className="small text-slate-300 mt-3 mb-0"><Link to="/login">Sign in</Link> to save your avatar across devices.</p>
              )}
            </div>

            <div className="velora-card p-4 mb-4">
              <h2 className="h5">Try on apparel</h2>
              <div className="row g-2 mt-2">
                {apparel.map((product) => (
                  <div className="col-4 col-md-3" key={product.id}>
                    <button type="button" className={`tryon-thumb ${selectedTop?.id === product.id ? 'active' : ''}`} onClick={() => tryOnTop(product)}>
                      <img src={product.images?.[0]?.url} alt={product.name} />
                      <span>{product.brand?.name}</span>
                    </button>
                  </div>
                ))}
              </div>
            </div>

            <div className="velora-card p-4">
              <h2 className="h5">Try on sneakers</h2>
              <div className="row g-2 mt-2">
                {footwear.map((product) => (
                  <div className="col-4 col-md-3" key={product.id}>
                    <button type="button" className={`tryon-thumb ${selectedShoes?.id === product.id ? 'active' : ''}`} onClick={() => setSelectedShoes(product)}>
                      <img src={product.images?.[0]?.url} alt={product.name} />
                      <span>{product.brand?.name}</span>
                    </button>
                  </div>
                ))}
              </div>
              {selectedTop && (
                <Link className="btn btn-velora-primary mt-3" to={`/catalog/${selectedTop.slug}`}>
                  Shop this fit <FiArrowUpRight />
                </Link>
              )}
            </div>
          </div>
        </div>
      </section>
    </>
  )
}
