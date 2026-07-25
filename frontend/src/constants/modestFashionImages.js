/** Demo imagery for VELORA — Islamic modest fashion for men and women. */
const photo = (id, width = 480) =>
  `https://images.unsplash.com/${id}?auto=format&fit=crop&w=${width}&q=${width <= 320 ? 65 : 72}`

/** Face-covered only: niqab and mannequin shots (no hijab portraits). */
const WOMEN_PHOTO_IDS = [
  'photo-1559730775-67f621597262', // black niqab
  'photo-1744727811425-e1c0af8b4022', // woman in black niqab
  'photo-1618297655311-ab851e7045d6', // niqab standing
  'photo-1771162766051-c330f1d664ea', // mannequin, modest dress & hijab
]

const MEN_PHOTO_IDS = [
  'photo-1564289851149-a9f8940f795c',
  'photo-1578507435314-e39e7852eddd',
  'photo-1756412066323-a336d2becc10',
  'photo-1761475048588-e00acbdce66f',
  'photo-1774424420923-6936309c3c5e',
  'photo-1757143137159-316220046829',
]

const poolFor = (gender) => (gender === 'men' ? MEN_PHOTO_IDS : WOMEN_PHOTO_IDS)

export const WOMEN_MODEST_IMAGES = WOMEN_PHOTO_IDS.map((id) => photo(id))
export const MEN_MODEST_IMAGES = MEN_PHOTO_IDS.map((id) => photo(id))
export const MODEST_FASHION_IMAGES = [...WOMEN_MODEST_IMAGES, ...MEN_MODEST_IMAGES]

export const womenModestImage = (index, width = 480) => photo(WOMEN_PHOTO_IDS[index % WOMEN_PHOTO_IDS.length], width)
export const menModestImage = (index, width = 480) => photo(MEN_PHOTO_IDS[index % MEN_PHOTO_IDS.length], width)

export const modestFashionImage = (index, width = 480, gender = 'women') =>
  photo(poolFor(gender)[index % poolFor(gender).length], width)
