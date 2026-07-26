import { memo } from 'react'
import { buildCloudinarySrc, buildCloudinarySrcSet } from '../../utils/cloudinary'
import { resolveMediaUrl } from '../../utils/mediaUrl'

function CloudinaryImage({ src, alt, className, width = 640, sizes = '(max-width: 768px) 50vw, 25vw', fetchPriority }) {
  const resolved = resolveMediaUrl(src)
  const optimized = buildCloudinarySrc(resolved, width)
  const srcSet = buildCloudinarySrcSet(resolved)

  return (
    <img
      src={optimized}
      srcSet={srcSet}
      sizes={srcSet ? sizes : undefined}
      alt={alt}
      loading={fetchPriority === 'high' ? 'eager' : 'lazy'}
      decoding="async"
      fetchPriority={fetchPriority}
      referrerPolicy="no-referrer"
      className={className}
      onError={(event) => {
        if (resolved && event.currentTarget.src !== resolved) {
          event.currentTarget.src = resolved
          event.currentTarget.removeAttribute('srcset')
        }
      }}
    />
  )
}

export default memo(CloudinaryImage)
