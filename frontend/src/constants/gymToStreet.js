/** VELORA — men's sport retail (Nike-style IA for Gen Z). */

export const NICHE_TAGLINE = 'Just Train It.'
export const NICHE_PROMISE = 'Training fits + matching street kicks from Nike, Adidas, Gymshark & more.'
export const CURATED_FIT_COUNT = '2,000+'

export const PRIORITY_BRANDS = ['Nike', 'Adidas', 'Jordan', 'Gymshark', 'Puma', 'Under Armour', 'New Balance']

const unsplash = (id, width = 1600) =>
  `https://images.unsplash.com/${id}?auto=format&fit=crop&w=${width}&h=${Math.round(width * 0.65)}&q=88&dpr=2`

/** Nike-style full-bleed campaign slides */
export const RETAIL_CAMPAIGNS = [
  {
    id: 'training-heat',
    eyebrow: 'NEW SEASON',
    title: 'TRAIN HARD.\nDRESS CLEAN.',
    copy: 'Full training fits + matching sneakers. Built for gym, court, and street.',
    image: unsplash('photo-1571019614242-c5c5dee9f50b'),
    cta: 'Shop Men',
    to: '/catalog?gender=men',
    secondaryCta: 'Build my fit',
    secondaryTo: '/ai/occasion',
  },
  {
    id: 'street-kicks',
    eyebrow: 'FOOTWEAR',
    title: 'STREET-READY\nKICKS',
    copy: 'Running, basketball, lifestyle — every outfit gets the right shoe.',
    image: unsplash('photo-1542291026-7eec264c27ff'),
    cta: 'Shop Shoes',
    to: '/catalog?line=footwear',
    secondaryCta: 'Shop Nike',
    secondaryTo: '/brands/nike',
  },
  {
    id: 'post-gym',
    eyebrow: 'POST-GYM',
    title: 'HOODIE.\nJOGGERS.\nHEAT.',
    copy: 'Leave the gym looking like you meant to be seen.',
    image: unsplash('photo-1556821840-3a63f95609a7'),
    cta: 'Shop Clothing',
    to: '/catalog?line=apparel',
    secondaryCta: 'Leg day fits',
    secondaryTo: '/catalog?moment=leg-day',
  },
  {
    id: 'sale',
    eyebrow: 'MEMBER DROP',
    title: 'UP TO 25% OFF\nSELECT STYLES',
    copy: 'Training tees, hoodies, and kicks — limited time.',
    image: unsplash('photo-1534438327276-14e5300c3a48'),
    cta: 'Shop Sale',
    to: '/catalog?sale=1',
    secondaryCta: 'View all brands',
    secondaryTo: '/catalog?gender=men',
  },
]

/** Nike-style category discovery tiles */
export const RETAIL_CATEGORIES = [
  { label: 'Running Shoes', slug: 'mens-sport-running-shoes', image: unsplash('photo-1542291026-7eec264c27ff', 900) },
  { label: 'Lifestyle Sneakers', slug: 'mens-sport-lifestyle-sneakers', image: unsplash('photo-1460353589841-049ca37d260b', 900) },
  { label: 'Hoodies', slug: 'mens-sport-hoodie', image: unsplash('photo-1556821840-3a63f95609a7', 900) },
  { label: 'Joggers', slug: 'mens-sport-joggers', image: unsplash('photo-1518611012118-696072aa579a', 900) },
  { label: 'Training Tees', slug: 'mens-sport-training-tee', image: unsplash('photo-1571019614242-c5c5dee9f50b', 900) },
  { label: 'Shorts', slug: 'mens-sport-training-shorts', image: unsplash('photo-1620799139839-3bcdc4bf0a35', 900) },
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
