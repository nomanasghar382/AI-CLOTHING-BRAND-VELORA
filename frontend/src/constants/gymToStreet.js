/** VELORA — gym-to-street niche for Gen Z guys 16–25. */

export const NICHE_TAGLINE = 'Gym → Street'
export const NICHE_PROMISE = 'Full training fit + matching street kicks. Sized to you.'
export const CURATED_FIT_COUNT = '2,000+'

export const PRIORITY_BRANDS = ['Nike', 'Gymshark', 'Adidas', 'Under Armour', 'Puma', 'Lululemon', 'New Balance']

export const GYM_MOMENTS = [
  { id: 'leg-day', label: 'Leg day', copy: 'Compression, shorts & training shoes' },
  { id: 'post-gym', label: 'Post-gym street', copy: 'Hoodie, joggers & lifestyle sneakers' },
  { id: 'training', label: 'Full training', copy: 'Tees, layers & gym essentials' },
]

export const CATALOG_TABS = [
  { id: '', label: 'All gym-to-street' },
  { id: 'apparel', line: 'apparel', label: 'Training' },
  { id: 'footwear', line: 'footwear', label: 'Street kicks' },
]

const unsplash = (id, width = 720) =>
  `https://images.unsplash.com/${id}?auto=format&fit=crop&w=${width}&h=${width <= 540 ? 900 : 1200}&q=85&dpr=2`

export const FEATURED_EDITORIAL = [
  { name: 'Nike Training Club', image: unsplash('photo-1571019614242-c5c5dee9f50b'), tag: 'Leg day', moment: 'leg-day' },
  { name: 'Gymshark Vital', image: unsplash('photo-1534438327276-14e5300c3a48'), tag: 'Core', moment: 'training' },
  { name: 'Adidas Post-Gym', image: unsplash('photo-1556821840-3a63f95609a7'), tag: 'Street', moment: 'post-gym' },
  { name: 'UA Training Heat', image: unsplash('photo-1518611012118-696072aa579a'), tag: 'Train', moment: 'training' },
]

export const FIT_STEPS = [
  { step: '01', title: 'Tell us your grind', copy: 'Leg day, push day, or post-gym hang — budget and brands you actually wear.' },
  { step: '02', title: 'See it on your avatar', copy: 'Preview the full fit on your body profile before you spend.' },
  { step: '03', title: 'Shop the exact look', copy: 'Training piece + matching street kicks. One tap to bag.' },
]
