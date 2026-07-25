const creators = [
  { id: 'amara', name: 'Amara Idris', handle: '@amaraedits', specialty: 'Modest essentials', followers: '18.4K', image: 'https://images.unsplash.com/photo-1490481651871-ab68de25d43d?auto=format&fit=crop&w=700&q=80', bio: 'Building thoughtful wardrobes with movement, texture, and purpose.' },
  { id: 'nora', name: 'Nora Kim', handle: '@noraspace', specialty: 'Color stories', followers: '12.1K', image: 'https://images.unsplash.com/photo-1483985988355-763728e1935b?auto=format&fit=crop&w=700&q=80', bio: 'A visual diary of soft tailoring and unexpected color.' },
  { id: 'marin', name: 'Marin Cole', handle: '@marinmakes', specialty: 'City uniforms', followers: '9.8K', image: 'https://images.unsplash.com/photo-1529139574466-a303027c1d8b?auto=format&fit=crop&w=700&q=80', bio: 'Practical pieces, styled with a little theatre.' },
]

const looks = [
  { id: 'look-1', title: 'After-hours linen', creator: creators[0], image: 'https://images.unsplash.com/photo-1509631179647-0177331693ae?auto=format&fit=crop&w=900&q=80', likes: 284, tags: ['Linen', 'Neutral'] },
  { id: 'look-2', title: 'Soft power palette', creator: creators[1], image: 'https://images.unsplash.com/photo-1539109136881-3be0616acf4b?auto=format&fit=crop&w=900&q=80', likes: 196, tags: ['Tailoring', 'Lilac'] },
  { id: 'look-3', title: 'Weekend volume', creator: creators[2], image: 'https://images.unsplash.com/photo-1515886657613-9f3515b0c78f?auto=format&fit=crop&w=900&q=80', likes: 347, tags: ['Layering', 'Black'] },
  { id: 'look-4', title: 'Olive on olive', creator: creators[0], image: 'https://images.unsplash.com/photo-1542295669297-4d352b042bca?auto=format&fit=crop&w=900&q=80', likes: 228, tags: ['Earth tones', 'Everyday'] },
]

const products = [
  { id: 'look-product-1', name: 'Sculpted linen blazer', brand: 'Aster', price: 148, image: 'https://images.unsplash.com/photo-1591047139829-d91aecb6caea?auto=format&fit=crop&w=500&q=80' },
  { id: 'look-product-2', name: 'Wide-leg trouser', brand: 'Morrow', price: 92, image: 'https://images.unsplash.com/photo-1594633312681-425c7b97ccd1?auto=format&fit=crop&w=500&q=80' },
  { id: 'look-product-3', name: 'Mini crescent bag', brand: 'Nomi', price: 76, image: 'https://images.unsplash.com/photo-1584917865442-de89df76afd3?auto=format&fit=crop&w=500&q=80' },
]

export const communityService = {
  creators: () => Promise.resolve(creators),
  looks: () => Promise.resolve(looks),
  products: () => Promise.resolve(products),
}
