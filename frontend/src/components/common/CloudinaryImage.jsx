import { memo } from 'react'
import { buildCloudinarySrc, buildCloudinarySrcSet } from '../../utils/cloudinary'

function CloudinaryImage({ src, alt, className, width = 640, sizes = '(max-width: 768px) 50vw, 25vw' }) {
  const optimized = buildCloudinarySrc(src, width)
  const srcSet = buildCloudinarySrcSet(src)

  return (
    <img
      src={optimized}
      srcSet={srcSet}
      sizes={srcSet ? sizes : undefined}
      alt={alt}
      loading="lazy"
      decoding="async"
      className={className}
    />
  )
}

export default memo(CloudinaryImage)
