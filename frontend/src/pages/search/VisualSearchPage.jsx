import { useEffect, useRef, useState } from 'react'
import { Link } from 'react-router-dom'
import { FiCamera, FiExternalLink, FiSearch, FiUpload } from 'react-icons/fi'
import Loader from '../../components/feedback/Loader'
import ErrorState from '../../components/feedback/ErrorState'
import { searchService } from '../../services/searchService'
import { formatCurrency } from '../../utils/format'

export default function VisualSearchPage() {
  const input = useRef(null)
  const previewUrl = useRef(null)
  const [file, setFile] = useState(null)
  const [imageFile, setImageFile] = useState(null)
  const [results, setResults] = useState([])
  const [loading, setLoading] = useState(false)
  const [error, setError] = useState(false)
  const [searched, setSearched] = useState(false)

  useEffect(() => () => {
    if (previewUrl.current) URL.revokeObjectURL(previewUrl.current)
  }, [])

  const select = (event) => {
    const image = event.target.files?.[0]
    if (!image) return
    if (previewUrl.current) URL.revokeObjectURL(previewUrl.current)
    previewUrl.current = URL.createObjectURL(image)
    setFile(previewUrl.current)
    setImageFile(image)
    setSearched(false)
    setResults([])
    setError(false)
  }

  const findSimilar = async () => {
    if (!imageFile) return
    setLoading(true)
    setError(false)
    try {
      const { data } = await searchService.visual(imageFile)
      setResults(data.data?.items || [])
      setSearched(true)
    } catch {
      setError(true)
      setSearched(true)
    } finally {
      setLoading(false)
    }
  }

  return (
    <section className="container py-5 feature-page">
      <div className="narrow-page text-center mx-auto">
        <p className="eyebrow">IMAGE SEARCH</p>
        <h1>Drop a fit. Find the heat.</h1>
        <p className="text-slate-300">Upload a sportswear photo and we&apos;ll match Nike, Adidas, Puma &amp; more from our catalog.</p>
        <button type="button" className={`visual-upload ${file ? 'has-image' : ''}`} onClick={() => input.current?.click()}>
          {file ? <img src={file} alt="Selected inspiration" /> : <><FiCamera /><strong>Drop a sportswear image or browse</strong><small>We match silhouette, colorway &amp; brand vibe.</small></>}
        </button>
        <input ref={input} type="file" accept="image/*" hidden onChange={select} />
        <div className="d-flex justify-content-center gap-2 mt-3">
          <button type="button" className="btn btn-velora-secondary" onClick={() => input.current?.click()}><FiUpload /> Upload</button>
          <button type="button" className="btn btn-velora-primary" disabled={!file || loading} onClick={findSimilar}><FiSearch /> {loading ? 'Matching…' : 'Find similar'}</button>
        </div>
      </div>
      {loading && <div className="mt-5"><Loader label="Scanning sport catalog..." /></div>}
      {searched && !loading && error && <div className="mt-5"><ErrorState onRetry={findSimilar} /></div>}
      {searched && !loading && !error && (
        <div className="visual-results mt-5">
          <div className="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
            <div><p className="eyebrow mb-1">MATCHES FOUND</p><h2 className="h3 mb-0">{results.length ? 'Closest sport matches' : 'No matches yet'}</h2></div>
          </div>
          {results.length ? (
            <div className="row g-3">
              {results.map((item) => (
                <div className="col-md-4" key={item.product_id}>
                  <article className="velora-card search-result overflow-hidden">
                    <img src={item.image_url} alt={item.name} />
                    <div className="p-3">
                      <p className="eyebrow mb-1">{item.brand} · {Math.round((item.score || 0) * 100)}% match</p>
                      <h3 className="h6">{item.name}</h3>
                      <div className="d-flex justify-content-between align-items-center">
                        <strong>{formatCurrency(item.price)}</strong>
                        <Link className="btn btn-velora-ghost p-0" to={`/catalog/${item.slug}`}>View <FiExternalLink /></Link>
                      </div>
                    </div>
                  </article>
                </div>
              ))}
            </div>
          ) : <p className="text-slate-300">Try another angle or browse the <Link to="/catalog?gender=men">sport catalog</Link>.</p>}
        </div>
      )}
    </section>
  )
}
