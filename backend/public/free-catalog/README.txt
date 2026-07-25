FREE PHOTOS — NO PAYMENT NEEDED
================================

STARTER IMAGES INCLUDED
-----------------------
This repo now ships with 6 women's + 1 men's starter photo in:
  women/01-olive-jilbab-niqab.jpg … 06-black-niqab-portrait.jpg
  men/brand-model.jpg

Replace any file with your own photo (same filename) to use your originals.
Then run: php artisan catalog:free-photos && php artisan db:seed --class=CatalogSeeder

WOMEN — YOUR NIQAB / ABAYA / KHIMAR PHOTOS
------------------------------------------
Save the 6 women's images you want into:
  backend/public/free-catalog/women/

Suggested names (any JPG/PNG names work):
  01-olive-jilbab-niqab.jpg      (olive green abaya + niqab)
  02-black-abaya-coral-khimar.jpg (black abaya + pink khimar)
  03-red-satin-abaya.jpg          (red satin + white hijab)
  04-maroon-niqab-tiara.jpg       (maroon niqab set)
  05-black-niqab-studio.jpg       (full black niqab white bg)
  06-black-niqab-portrait.jpg       (black niqab portrait)

Then run:
  php artisan catalog:free-photos
  php artisan db:seed --class=CatalogSeeder

Every women's product will rotate through YOUR photos only.
No stock images when your folder has photos.

Windows path:
C:\Users\EHSAN computer\OneDrive - Higher Education Commission\Desktop\VELORA\backend\public\free-catalog\women\


MEN — YOUR PHOTO ON ALL MEN'S CLOTHING
--------------------------------------
Save your portrait as:
  backend/public/free-catalog/men/brand-model.jpg

Then re-seed (same commands as above).

Hard refresh browser: Ctrl+Shift+R
