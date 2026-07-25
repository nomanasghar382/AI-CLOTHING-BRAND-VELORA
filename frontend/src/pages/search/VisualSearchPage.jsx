import { useEffect, useRef, useState } from 'react'
import { FiCamera, FiExternalLink, FiSearch, FiUpload } from 'react-icons/fi'
import { modestFashionImage } from '../../constants/modestFashionImages'

const results = [
  { name: 'Draped modest blouse', provider: 'Velora Studio', price: '$84', image: modestFashionImage(10, 600) },
  { name: 'Wide-leg abaya pant', provider: 'Velora Studio', price: '$92', image: modestFashionImage(5, 600) },
  { name: 'Silk hijab scarf', provider: 'Velora Studio', price: '$76', image: modestFashionImage(11, 600) },
]

export default function VisualSearchPage() {
  const input = useRef(null)
  const previewUrl = useRef(null)
  const [file, setFile] = useState(null)
  const [searched, setSearched] = useState(false)
  const [provider, setProvider] = useState('All providers')

  useEffect(() => () => {
    if (previewUrl.current) URL.revokeObjectURL(previewUrl.current)
  }, [])

  const select = (event) => {
    const image = event.target.files?.[0]
    if (!image) return
    if (previewUrl.current) URL.revokeObjectURL(previewUrl.current)
    previewUrl.current = URL.createObjectURL(image)
    setFile(previewUrl.current)
    setSearched(false)
  }

  const shown = provider === 'All providers' ? results : results.filter((item) => item.provider === provider)

  return (
    <section className="container py-5 feature-page">
      <div className="narrow-page text-center mx-auto">
        <p className="eyebrow">VISUAL SEARCH</p>
        <h1>Show us what you’re looking for.</h1>
        <p className="text-slate-300">Upload an inspiration image and discover similar pieces from our providers.</p>
        <button type="button" className={`visual-upload ${file ? 'has-image' : ''}`} onClick={() => input.current?.click()}>
          {file ? <img src={file} alt="Selected inspiration" /> : <><FiCamera /><strong>Drop an image or browse</strong><small>We’ll match silhouette, color, and texture.</small></>}
        </button>
        <input ref={input} type="file" accept="image/*" hidden onChange={select} />
        <div className="d-flex justify-content-center gap-2 mt-3">
          <button type="button" className="btn btn-velora-secondary" onClick={() => input.current?.click()}><FiUpload /> Upload</button>
          <button type="button" className="btn btn-velora-primary" disabled={!file} onClick={() => setSearched(true)}><FiSearch /> Find similar</button>
        </div>
      </div>
      {searched && (
        <div className="visual-results mt-5">
          <div className="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
            <div><p className="eyebrow mb-1">MATCHES FOUND</p><h2 className="h3 mb-0">Closest visual matches</h2></div>
            <select className="form-select velora-input provider-select" value={provider} onChange={(event) => setProvider(event.target.value)} aria-label="Filter by provider">
              <option>All providers</option>
              {[...new Set(results.map((item) => item.provider))].map((item) => <option key={item}>{item}</option>)}
            </select>
          </div>
          <div className="row g-3">
            {shown.map((item) => (
              <div className="col-md-4" key={item.name}>
                <article className="velora-card search-result overflow-hidden">
                  <img src={item.image} alt={item.name} />
                  <div className="p-3">
                    <p className="eyebrow mb-1">{item.provider}</p>
                    <h3 className="h6">{item.name}</h3>
                    <div className="d-flex justify-content-between align-items-center">
                      <strong>{item.price}</strong>
                      <button type="button" className="btn btn-velora-ghost p-0">View <FiExternalLink /></button>
                    </div>
                  </div>
                </article>
              </div>
            ))}
          </div>
        </div>
      )}
    </section>
  )
}
