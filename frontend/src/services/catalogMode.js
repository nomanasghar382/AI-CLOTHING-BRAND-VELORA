let catalogMode = 'checking'

export function getCatalogMode() {
  return catalogMode
}

export function setCatalogMode(mode) {
  catalogMode = mode
  window.dispatchEvent(new CustomEvent('velora:catalog-mode', { detail: mode }))
}
