/** High-quality demo imagery — Gen Z Islamic modest fashion. */
const photo = (id, width = 720) =>
  `https://images.unsplash.com/${id}?auto=format&fit=crop&w=${width}&q=${width <= 540 ? 82 : 85}&dpr=2`

/** Women: niqab + mannequin only (fully covered, no open-face portraits). */
export const WOMEN_PHOTO_IDS = [
  'photo-1744727811425-e1c0af8b4022',
  'photo-1559730775-67f621597262',
  'photo-1618297655311-ab851e7045d6',
  'photo-1771162766051-c330f1d664ea',
]

/** Men: young kurta, thobe, and modest streetwear. */
export const MEN_PHOTO_IDS = [
  'photo-1774527929835-282b1b85cd3a',
  'photo-1759567066672-4b9f48000096',
  'photo-1756412066323-a336d2becc10',
  'photo-1774424420923-6936309c3c5e',
  'photo-1578507435314-e39e7852eddd',
  'photo-1757143137159-316220046829',
]

const poolFor = (gender) => (gender === 'men' ? MEN_PHOTO_IDS : WOMEN_PHOTO_IDS)

export const WOMEN_MODEST_IMAGES = WOMEN_PHOTO_IDS.map((id) => photo(id))
export const MEN_MODEST_IMAGES = MEN_PHOTO_IDS.map((id) => photo(id))
export const MODEST_FASHION_IMAGES = [...WOMEN_MODEST_IMAGES, ...MEN_MODEST_IMAGES]

export const womenModestImage = (index, width = 720) => photo(WOMEN_PHOTO_IDS[index % WOMEN_PHOTO_IDS.length], width)
export const menModestImage = (index, width = 720) => photo(MEN_PHOTO_IDS[index % MEN_PHOTO_IDS.length], width)

export const modestFashionImage = (index, width = 720, gender = 'women') =>
  photo(poolFor(gender)[index % poolFor(gender).length], width)
