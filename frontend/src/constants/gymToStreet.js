/** VELORA — men's sport retail. Nike-style: white, black, clean. */

export const NICHE_TAGLINE = 'Just Do It.'
export const NICHE_PROMISE = 'Men\'s training + streetwear from Nike, Adidas, Jordan & more.'
export const CURATED_FIT_COUNT = '2,000+'

export const PRIORITY_BRANDS = ['Nike', 'Adidas', 'Jordan', 'Gymshark', 'Puma', 'Under Armour', 'New Balance']

/** Local images in /public/catalog — always load */
export const RETAIL_CAMPAIGNS = [
  {
    id: 'training-heat',
    eyebrow: 'NEW SEASON',
    title: 'TRAIN HARD.\nDRESS CLEAN.',
    copy: 'Full training fits + matching sneakers.',
    image: '/catalog/hero-gym.jpg',
    cta: 'Shop Men',
    to: '/catalog?gender=men',
    secondaryCta: 'Shop Shoes',
    secondaryTo: '/catalog?line=footwear',
  },
  {
    id: 'street-kicks',
    eyebrow: 'FOOTWEAR',
    title: 'STREET-READY\nKICKS',
    copy: 'Running, basketball, lifestyle sneakers.',
    image: '/catalog/shoe-nike.jpg',
    cta: 'Shop Shoes',
    to: '/catalog?line=footwear',
    secondaryCta: 'Shop Nike',
    secondaryTo: '/brands/nike',
  },
  {
    id: 'post-gym',
    eyebrow: 'CLOTHING',
    title: 'HOODIE.\nJOGGERS.\nHEAT.',
    copy: 'Post-gym fits that look intentional.',
    image: '/catalog/apparel-hoodie.jpg',
    cta: 'Shop Clothing',
    to: '/catalog?line=apparel',
    secondaryCta: 'New arrivals',
    secondaryTo: '/catalog?new=1',
  },
]

export const RETAIL_CATEGORIES = [
  { label: 'Sneakers', slug: 'mens-sport-lifestyle-sneakers', image: '/catalog/shoe-nike.jpg' },
  { label: 'Running Shoes', slug: 'mens-sport-running-shoes', image: '/catalog/shoe-runner.jpg' },
  { label: 'Hoodies', slug: 'mens-sport-hoodie', image: '/catalog/apparel-hoodie.jpg' },
  { label: 'Joggers', slug: 'mens-sport-joggers', image: '/catalog/apparel-joggers.jpg' },
  { label: 'Training Tees', slug: 'mens-sport-training-tee', image: '/catalog/apparel-train.jpg' },
  { label: 'Shop All', slug: '', image: '/catalog/hero-gym.jpg' },
]

export const RETAIL_NAV = [
  { label: 'New', to: '/catalog?new=1' },
  { label: 'Men', to: '/catalog?gender=men' },
  { label: 'Shoes', to: '/catalog?line=footwear' },
  { label: 'Clothing', to: '/catalog?line=apparel' },
  { label: 'Sale', to: '/catalog?sale=1' },
]

export const GYM_MOMENTS = [
  { id: 'leg-day', label: 'Leg day', copy: 'Compression, shorts & training shoes' },
  { id: 'post-gym', label: 'Post-gym street', copy: 'Hoodie, joggers & lifestyle sneakers' },
  { id: 'training', label: 'Full training', copy: 'Tees, layers & gym essentials' },
]

export const CATALOG_TABS = [
  { id: 'featured', label: 'Featured', params: { featured: '1' } },
  { id: 'shoes', label: 'Shoes', params: { line: 'footwear' } },
  { id: 'clothing', label: 'Clothing', params: { line: 'apparel' } },
  { id: 'new', label: 'New', params: { new: '1' } },
]

export const FEATURED_EDITORIAL = RETAIL_CAMPAIGNS.map((slide) => ({
  name: slide.title.replace('\n', ' '),
  image: slide.image,
  tag: slide.eyebrow,
  moment: slide.id,
}))

export const FIT_STEPS = [
  { step: '01', title: 'Pick your sport', copy: 'Running, basketball, training, or post-gym street.' },
  { step: '02', title: 'See it on you', copy: 'Avatar preview before you buy — like Nike Fit, but for full fits.' },
  { step: '03', title: 'Bag the look', copy: 'Top + matching kicks. One checkout.' },
]
