/** VELORA Sport — Gen Z men's athletic editorial. */
const unsplash = (id, width = 720) =>
  `https://images.unsplash.com/${id}?auto=format&fit=crop&w=${width}&h=${width <= 540 ? 900 : 1200}&q=85&dpr=2`

export const FEATURED_EDITORIAL = [
  { name: 'Nike Volt Run', image: unsplash('photo-1542291026-7eec264c27ff'), gender: 'men', tag: 'Kicks' },
  { name: 'Adidas Court Heat', image: unsplash('photo-1595950653106-6c9ebd614d3a'), gender: 'men', tag: 'New' },
  { name: 'Gymshark Pro', image: unsplash('photo-1571019614242-c5c5dee9f50b'), gender: 'men', tag: 'Trending' },
  { name: 'Jordan Game Day', image: unsplash('photo-1546519638-68e109498ffc'), gender: 'men', tag: 'Icon' },
  { name: 'Puma Street Sprint', image: unsplash('photo-1518611012118-696072aa579a'), gender: 'men', tag: 'Drop' },
  { name: 'Under Armour Train', image: unsplash('photo-1534438327276-14e5300c3a48'), gender: 'men', tag: 'Pro' },
  { name: 'New Balance Flow', image: unsplash('photo-1460353589841-049ca37d260b'), gender: 'men', tag: 'Run' },
  { name: 'Y-3 Sport Luxe', image: unsplash('photo-1556821840-3a63f95609a7'), gender: 'men', tag: 'Elite' },
]

export const CATALOG_DISPLAY_TOTAL = '1,000,000+'
