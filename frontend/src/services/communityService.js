import { menModestImage, womenModestImage } from '../constants/modestFashionImages'

const creators = [
  { id: 'amara', name: 'Amara Idris', handle: '@amaraedits', specialty: 'Women\'s abaya & niqab', followers: '18.4K', image: womenModestImage(0, 700), bio: 'Curating fully-covered edits for everyday elegance.' },
  { id: 'omar', name: 'Omar Hassan', handle: '@omarstyle', specialty: 'Men\'s thobe & kandura', followers: '14.2K', image: menModestImage(0, 700), bio: 'Classic Islamic menswear with modern tailoring.' },
  { id: 'nora', name: 'Nora Kim', handle: '@noraspace', specialty: 'Hijab & khimar sets', followers: '12.1K', image: womenModestImage(2, 700), bio: 'Layered modest looks with complete coverage.' },
]

const looks = [
  { id: 'look-1', title: 'Evening abaya edit', creator: creators[0], image: womenModestImage(0), likes: 284, tags: ['Niqab', 'Abaya'] },
  { id: 'look-2', title: 'Friday thobe look', creator: creators[1], image: menModestImage(1), likes: 196, tags: ['Thobe', 'White'] },
  { id: 'look-3', title: 'Desert niqab style', creator: creators[2], image: womenModestImage(4), likes: 347, tags: ['Niqab', 'Black'] },
  { id: 'look-4', title: 'Eid kandura set', creator: creators[1], image: menModestImage(2), likes: 228, tags: ['Kandura', 'Eid'] },
]

const products = [
  { id: 'look-product-1', name: 'Flowing black abaya', brand: 'Velora Studio', price: 148, image: womenModestImage(4, 500) },
  { id: 'look-product-2', name: 'Premium white thobe', brand: 'Velora Studio', price: 92, image: menModestImage(1, 500) },
  { id: 'look-product-3', name: 'Silk hijab & niqab set', brand: 'Velora Studio', price: 76, image: womenModestImage(0, 500) },
]

export const communityService = {
  creators: () => Promise.resolve(creators),
  looks: () => Promise.resolve(looks),
  products: () => Promise.resolve(products),
}
