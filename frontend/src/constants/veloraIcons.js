/** Featured Gen Z editorial shots for homepage — young modest fashion only. */
const unsplash = (id, width = 720) =>
  `https://images.unsplash.com/${id}?auto=format&fit=crop&w=${width}&h=${width <= 540 ? 900 : 1200}&q=85&dpr=2`

export const FEATURED_EDITORIAL = [
  { name: 'Hijab Street Edit', image: unsplash('photo-1774227834992-745da3b78e98'), gender: 'women', tag: 'Trending' },
  { name: 'Black Abaya Drop', image: unsplash('photo-1772474542630-5f5822ca8421'), gender: 'women', tag: 'New' },
  { name: 'Maroon Hijab Set', image: unsplash('photo-1768830985958-e8d3a93d3f14'), gender: 'women', tag: 'Icon' },
  { name: 'Pink Hijab Edit', image: unsplash('photo-1626497361649-81cc097e9bfd'), gender: 'women', tag: 'Drop' },
  { name: 'White Kurta Fit', image: unsplash('photo-1774527929835-282b1b85cd3a'), gender: 'men', tag: 'Trending' },
  { name: 'Shalwar Street', image: unsplash('photo-1759567066672-4b9f48000096'), gender: 'men', tag: 'New' },
  { name: 'Thobe Essential', image: unsplash('photo-1756412066323-a336d2becc10'), gender: 'men', tag: 'Icon' },
  { name: 'Jubba Edit', image: unsplash('photo-1774424420923-6936309c3c5e'), gender: 'men', tag: 'Drop' },
]
