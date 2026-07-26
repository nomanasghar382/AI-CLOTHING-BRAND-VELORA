import CampaignCarousel from '../components/home/CampaignCarousel'
import CategoryTileGrid from '../components/home/CategoryTileGrid'
import RetailTrending from '../components/home/RetailTrending'
import BrandStrip from '../components/home/BrandStrip'
import Seo from '../components/system/Seo'

export default function HomePage() {
  return (
    <>
      <Seo title="Men's Sport — Nike, Adidas & more" description="Shop training fits and matching street kicks. Nike-style sport retail for Gen Z guys." />
      <CampaignCarousel />
      <CategoryTileGrid />
      <RetailTrending />
      <BrandStrip />
    </>
  )
}
