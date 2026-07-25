/** Demo imagery for VELORA — modest / Islamic fashion with full coverage. */
const photo = (id, width = 900) =>
  `https://images.unsplash.com/${id}?auto=format&fit=crop&w=${width}&q=80`

const PHOTO_IDS = [
  'photo-1770964211782-013475eacc3f', // black abaya & hijab, park
  'photo-1561442748-c50715dc32f6', // beige abaya & black hijab
  'photo-1770367358711-b42cf1a6c2b1', // black abaya outdoors
  'photo-1750190321796-c749877df841', // flowing black abaya
  'photo-1560350530-a12ec1414cf5', // brown abaya & orange hijab
  'photo-1752794674886-fb12817a5e96', // modest gray abaya
  'photo-1630735988694-12186aedd73d', // black hijab & abaya
  'photo-1588594509615-62de3570d696', // white hijab & black abaya
  'photo-1771162766051-c330f1d664ea', // mannequin, modest dress & hijab
  'photo-1545266241-3516e2a6e016', // white dress & white hijab
  'photo-1585728748176-455ac5eed962', // white hijab & long sleeve
  'photo-1542380841-5eef57349ca1', // blue hijab scarf
]

export const MODEST_FASHION_IMAGES = PHOTO_IDS.map((id) => photo(id))

export const modestFashionImage = (index, width = 900) => photo(PHOTO_IDS[index % PHOTO_IDS.length], width)
