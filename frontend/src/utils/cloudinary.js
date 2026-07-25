export function buildCloudinarySrc(url, width = 640) {
  if (!url || !url.includes('res.cloudinary.com')) return url
  return url.replace('/upload/', `/upload/f_auto,q_auto,w_${width},c_fill/`)
}

export function buildCloudinarySrcSet(url, widths = [320, 480, 640, 960]) {
  if (!url || !url.includes('res.cloudinary.com')) return undefined
  return widths.map((width) => `${buildCloudinarySrc(url, width)} ${width}w`).join(', ')
}
