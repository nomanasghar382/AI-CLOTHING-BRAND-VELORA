import { useEffect } from 'react'

export default function Seo({ title, description, image, type = 'website', jsonLd }) {
  useEffect(() => {
    document.title = title ? `${title} | VELORA` : 'VELORA — Style, Intelligently Yours.'
    const setMeta = (name, content, property = false) => {
      if (!content) return
      const selector = property ? `meta[property="${name}"]` : `meta[name="${name}"]`
      let tag = document.querySelector(selector)
      if (!tag) {
        tag = document.createElement('meta')
        if (property) tag.setAttribute('property', name)
        else tag.setAttribute('name', name)
        document.head.appendChild(tag)
      }
      tag.setAttribute('content', content)
    }
    setMeta('description', description)
    setMeta('og:title', title, true)
    setMeta('og:description', description, true)
    setMeta('og:type', type, true)
    setMeta('og:image', image, true)
    setMeta('twitter:card', 'summary_large_image')
    setMeta('twitter:title', title)
    setMeta('twitter:description', description)
    let script = document.getElementById('velora-jsonld')
    if (jsonLd) {
      if (!script) {
        script = document.createElement('script')
        script.id = 'velora-jsonld'
        script.type = 'application/ld+json'
        document.head.appendChild(script)
      }
      script.textContent = JSON.stringify(jsonLd)
    }
  }, [title, description, image, type, jsonLd])

  return null
}
