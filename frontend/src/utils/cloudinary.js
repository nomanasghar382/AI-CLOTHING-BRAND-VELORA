function unsplashUrl(url, width, quality) {
  const parsed = new URL(url)
  parsed.searchParams.set('auto', 'format')
  parsed.searchParams.set('fit', 'crop')
  parsed.searchParams.set('w', String(width))
  parsed.searchParams.set('q', String(quality))
  parsed.searchParams.set('dpr', '2')

  return parsed.toString()
}

function pexelsUrl(url, width, quality) {
  const parsed = new URL(url)
  parsed.searchParams.set('auto', 'compress')
  parsed.searchParams.set('fit', 'crop')
  parsed.searchParams.set('w', String(width))
  parsed.searchParams.set('q', String(quality))
  parsed.searchParams.set('dpr', '2')

  return parsed.toString()
}

export function buildCloudinarySrc(url, width = 720) {
  if (!url) return url
  if (url.startsWith('/catalog/') || url.startsWith('/assets/') || url.startsWith('/free-catalog/')) return url

  if (url.includes('res.cloudinary.com')) {
    return url.replace('/upload/', `/upload/f_auto,q_auto:good,w_${width},c_fill,dpr_2.0/`)
  }

  if (url.includes('images.unsplash.com') || url.includes('plus.unsplash.com')) {
    const quality = width <= 400 ? 80 : width <= 720 ? 82 : 85
    return unsplashUrl(url, width, quality)
  }

  if (url.includes('images.pexels.com')) {
    const quality = width <= 400 ? 80 : width <= 720 ? 82 : 85
    return pexelsUrl(url, width, quality)
  }

  return url
}

export function buildCloudinarySrcSet(url, widths = [360, 540, 720, 1080]) {
  if (!url) return undefined

  if (
    url.includes('res.cloudinary.com')
    || url.includes('images.unsplash.com')
    || url.includes('plus.unsplash.com')
    || url.includes('images.pexels.com')
  ) {
    return widths.map((width) => `${buildCloudinarySrc(url, width)} ${width}w`).join(', ')
  }

  return undefined
}
