import { modestFashionImage } from '../constants/modestFashionImages'

const creators = [
  { id: 'amara', name: 'Amara Idris', handle: '@amaraedits', specialty: 'Modest essentials', followers: '18.4K', image: modestFashionImage(0, 700), bio: 'Building thoughtful wardrobes with movement, texture, and purpose.' },
  { id: 'nora', name: 'Nora Kim', handle: '@noraspace', specialty: 'Color stories', followers: '12.1K', image: modestFashionImage(1, 700), bio: 'A visual diary of soft tailoring and unexpected color.' },
  { id: 'marin', name: 'Marin Cole', handle: '@marinmakes', specialty: 'City uniforms', followers: '9.8K', image: modestFashionImage(6, 700), bio: 'Practical pieces, styled with a little theatre.' },
]

const looks = [
  { id: 'look-1', title: 'After-hours linen', creator: creators[0], image: modestFashionImage(0), likes: 284, tags: ['Abaya', 'Neutral'] },
  { id: 'look-2', title: 'Soft power palette', creator: creators[1], image: modestFashionImage(3), likes: 196, tags: ['Hijab', 'Layered'] },
  { id: 'look-3', title: 'Weekend volume', creator: creators[2], image: modestFashionImage(5), likes: 347, tags: ['Modest', 'Black'] },
  { id: 'look-4', title: 'Olive on olive', creator: creators[0], image: modestFashionImage(4), likes: 228, tags: ['Earth tones', 'Everyday'] },
]

const products = [
  { id: 'look-product-1', name: 'Flowing black abaya', brand: 'Velora Studio', price: 148, image: modestFashionImage(3, 500) },
  { id: 'look-product-2', name: 'Wide-leg modest pant', brand: 'Velora Studio', price: 92, image: modestFashionImage(5, 500) },
  { id: 'look-product-3', name: 'Silk hijab scarf', brand: 'Velora Studio', price: 76, image: modestFashionImage(11, 500) },
]

export const communityService = {
  creators: () => Promise.resolve(creators),
  looks: () => Promise.resolve(looks),
  products: () => Promise.resolve(products),
}
