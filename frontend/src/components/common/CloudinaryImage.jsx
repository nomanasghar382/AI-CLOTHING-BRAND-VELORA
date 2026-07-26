import { useState } from 'react'
import { buildCloudinarySrc, buildCloudinarySrcSet } from '../../utils/cloudinary'
import { resolveMediaUrl } from '../../utils/mediaUrl'
import { PLACEHOLDER_APPAREL } from '../../utils/productPlaceholder'

function CloudinaryImage({ src, alt, className, width = 640, sizes = '(max-width: 768px) 50vw, 25vw', fetchPriority, fallback = PLACEHOLDER_APPAREL }) {
  const resolved = resolveMediaUrl(src)
  const optimized = buildCloudinarySrc(resolved, width)
  const srcSet = buildCloudinarySrcSet(resolved)
  const [currentSrc, setCurrentSrc] = useState(optimized)
  const [currentSrcSet, setCurrentSrcSet] = useState(srcSet)

  return (
    <img
      src={currentSrc || fallback}
      srcSet={currentSrcSet || undefined}
      sizes={currentSrcSet ? sizes : undefined}
      alt={alt}
      loading={fetchPriority === 'high' ? 'eager' : 'lazy'}
      decoding="async"
      fetchPriority={fetchPriority}
      referrerPolicy="no-referrer"
      className={className}
      onError={() => {
        if (currentSrc === fallback) return
        if (resolved && currentSrc !== resolved) {
          setCurrentSrc(resolved)
          setCurrentSrcSet(undefined)
          return
        }
        setCurrentSrc(fallback)
        setCurrentSrcSet(undefined)
      }}
    />
  )
}

export default CloudinaryImage
