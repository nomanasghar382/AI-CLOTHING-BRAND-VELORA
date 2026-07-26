FREE CATALOG PHOTOS — GARMENT-MATCHED
======================================

Each product shows ONLY photos from its garment folder.
Trouser listing → trousers photo. Thobe listing → thobe photo.
Never the same kurta model on every men's item.

FOLDER STRUCTURE
----------------
  backend/public/free-catalog/men/kurta/          ← kurta photos only
  backend/public/free-catalog/men/shalwar_kameez/
  backend/public/free-catalog/men/thobe/
  backend/public/free-catalog/men/bottoms/        ← trousers/chinos flat-lay ONLY
  backend/public/free-catalog/men/top/
  backend/public/free-catalog/men/prayer/
  backend/public/free-catalog/men/accessory/

  backend/public/free-catalog/women/niqab/
  backend/public/free-catalog/women/abaya/
  backend/public/free-catalog/women/hijab/
  backend/public/free-catalog/women/bottoms/
  ... (one folder per garment type)

YOUR KURTA BRAND PHOTO
----------------------
Save your portrait as:
  backend/public/free-catalog/men/kurta/01-brand-model.jpg

It appears on KURTA products only — not thobe, not trousers.

YOUR WOMEN'S PHOTOS
-------------------
Put each look in the matching folder:
  women/niqab/   women/abaya/   women/jilbab/   etc.

AFTER ADDING PHOTOS
-------------------
  php artisan catalog:free-photos
  php artisan db:seed --class=CatalogSeeder

Hard refresh: Ctrl+Shift+R
