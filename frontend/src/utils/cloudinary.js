function unsplashUrl(url, width, quality) {
  const parsed = new URL(url)
  parsed.searchParams.set('auto', 'format')
  parsed.searchParams.set('fit', 'crop')
  parsed.searchParams.set('w', String(width))
  parsed.searchParams.set('q', String(quality))

  return parsed.toString()
}

export function buildCloudinarySrc(url, width = 640) {
  if (!url) return url

  if (url.includes('res.cloudinary.com')) {
    return url.replace('/upload/', `/upload/f_auto,q_auto,w_${width},c_fill/`)
  }

  if (url.includes('images.unsplash.com')) {
    const quality = width <= 240 ? 60 : width <= 400 ? 65 : 72
    return unsplashUrl(url, width, quality)
  }

  return url
}

export function buildCloudinarySrcSet(url, widths = [240, 360, 480, 640]) {
  if (!url) return undefined

  if (url.includes('res.cloudinary.com') || url.includes('images.unsplash.com')) {
    return widths.map((width) => `${buildCloudinarySrc(url, width)} ${width}w`).join(', ')
  }

  return undefined
}
