/** Demo imagery for VELORA — Islamic modest fashion for men and women. */
const photo = (id, width = 480) =>
  `https://images.unsplash.com/${id}?auto=format&fit=crop&w=${width}&q=${width <= 320 ? 65 : 72}`

/** Women: niqab, mannequin, and back-facing abaya shots (no visible faces). */
const WOMEN_PHOTO_IDS = [
  'photo-1559730775-67f621597262', // black niqab
  'photo-1771162766051-c330f1d664ea', // mannequin, modest dress & hijab
  'photo-1770367358711-b42cf1a6c2b1', // black abaya, walking outdoors
  'photo-1588594509615-62de3570d696', // white hijab & black abaya, street
  'photo-1750190321796-c749877df841', // flowing black abaya
  'photo-1767766277273-a53443ab8639', // black abaya outdoors
  'photo-1752794674886-fb12817a5e96', // modest gray abaya
  'photo-1560350530-a12ec1414cf5', // abaya & hijab, outdoor
]

/** Men: thobe, kandura, and traditional Islamic attire. */
const MEN_PHOTO_IDS = [
  'photo-1564289851149-a9f8940f795c', // black thobe
  'photo-1578507435314-e39e7852eddd', // white thobe
  'photo-1756412066323-a336d2becc10', // brown thobe
  'photo-1761475048588-e00acbdce66f', // traditional Islamic attire
  'photo-1774424420923-6936309c3c5e', // men in traditional clothing
  'photo-1757143137159-316220046829', // group in traditional attire
]

const poolFor = (gender) => (gender === 'men' ? MEN_PHOTO_IDS : WOMEN_PHOTO_IDS)

export const WOMEN_MODEST_IMAGES = WOMEN_PHOTO_IDS.map((id) => photo(id))
export const MEN_MODEST_IMAGES = MEN_PHOTO_IDS.map((id) => photo(id))
export const MODEST_FASHION_IMAGES = [...WOMEN_MODEST_IMAGES, ...MEN_MODEST_IMAGES]

export const womenModestImage = (index, width = 480) => photo(WOMEN_PHOTO_IDS[index % WOMEN_PHOTO_IDS.length], width)
export const menModestImage = (index, width = 480) => photo(MEN_PHOTO_IDS[index % MEN_PHOTO_IDS.length], width)

export const modestFashionImage = (index, width = 480, gender = 'women') =>
  photo(poolFor(gender)[index % poolFor(gender).length], width)
