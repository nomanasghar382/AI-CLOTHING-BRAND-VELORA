import { useEffect, useState } from 'react'
import { Link } from 'react-router-dom'
import { RETAIL_CAMPAIGNS } from '../../constants/gymToStreet'

export default function CampaignCarousel() {
  const [active, setActive] = useState(0)
  const slide = RETAIL_CAMPAIGNS[active]

  useEffect(() => {
    const timer = window.setInterval(() => {
      setActive((index) => (index + 1) % RETAIL_CAMPAIGNS.length)
    }, 6000)
    return () => window.clearInterval(timer)
  }, [])

  return (
    <section className="retail-campaign" aria-label="Featured campaigns">
      <div
        className="retail-campaign-bg"
        style={{ backgroundImage: `linear-gradient(90deg, rgba(0,0,0,.72) 0%, rgba(0,0,0,.35) 55%, rgba(0,0,0,.15) 100%), url(${slide.image})` }}
      />
      <div className="container retail-campaign-inner">
        <p className="retail-campaign-eyebrow">{slide.eyebrow}</p>
        <h1 className="retail-campaign-title">{slide.title.split('\n').map((line) => <span key={line}>{line}<br /></span>)}</h1>
        <p className="retail-campaign-copy">{slide.copy}</p>
        <div className="d-flex flex-wrap gap-2 mt-4">
          <Link className="btn btn-retail-primary btn-lg" to={slide.to}>{slide.cta}</Link>
          <Link className="btn btn-retail-secondary btn-lg" to={slide.secondaryTo}>{slide.secondaryCta}</Link>
        </div>
      </div>
      <div className="retail-campaign-dots" role="tablist" aria-label="Campaign slides">
        {RETAIL_CAMPAIGNS.map((item, index) => (
          <button
            key={item.id}
            type="button"
            role="tab"
            aria-selected={index === active}
            aria-label={item.title.replace('\n', ' ')}
            className={index === active ? 'active' : ''}
            onClick={() => setActive(index)}
          />
        ))}
      </div>
    </section>
  )
}
