import { useState } from 'react'
import CloudinaryImage from '../common/CloudinaryImage'

export default function ProductImageGallery({ images = [], productName, isFootwear = false }) {
  const [activeIndex, setActiveIndex] = useState(0)
  const active = images[activeIndex] || images[0]

  if (!active) {
    return <div className="pdp-gallery-stage is-empty" aria-hidden="true" />
  }

  return (
    <div className="pdp-gallery">
      <div className={`pdp-gallery-stage ${isFootwear ? 'is-footwear' : ''}`}>
        <CloudinaryImage
          src={active.url}
          alt={active.alt_text || productName}
          className="pdp-gallery-main"
          width={900}
          sizes="(max-width: 992px) 100vw, 55vw"
          fetchPriority="high"
        />
      </div>
      {images.length > 1 && (
        <div className="pdp-gallery-thumbs">
          {images.slice(0, 6).map((image, index) => (
            <button
              key={index}
              type="button"
              className={index === activeIndex ? 'active' : ''}
              aria-label={`View image ${index + 1}`}
              aria-pressed={index === activeIndex}
              onClick={() => setActiveIndex(index)}
            >
              <CloudinaryImage
                src={image.thumbnail_url || image.url}
                alt={image.alt_text || `${productName} view ${index + 1}`}
                width={120}
                sizes="80px"
              />
            </button>
          ))}
        </div>
      )}
    </div>
  )
}
