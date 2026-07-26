<?php

/**
 * Gen Z modest fashion — STRICT garment-matched photos.
 * Images live in public/free-catalog/{gender}/{familyKey}/ folders.
 * Unsplash IDs here are fallback only for the SAME garment family.
 */
return [
    'blacklist' => [
        'photo-1578507435314-e39e7852eddd',
        'photo-1507003211169-0a1dd7228f2d',
        'photo-1552374196-c4e7ffc6e126',
        'photo-1472099645785-5658abf4ff4e',
        'photo-1519345182560-3f2917c472ef',
        'photo-1557862921-37829c790f19',
        'photo-1560250097-0b93528c311a',
        'photo-1573496359142-b8d87734a5a2',
        'photo-1500648767791-00dcc994a43e',
        'photo-1594938298603-c8148c4dae35',
        'photo-1625728273079-27996db5e7f9',
        'photo-1666162174698-21b2f82f7ee9',
    ],

    'family_keys' => [
        'women' => [
            'Niqab' => 'niqab',
            'Hijab' => 'hijab',
            'Khimar' => 'khimar',
            'Abaya' => 'abaya',
            'Jilbab' => 'jilbab',
            'Burqa' => 'burqa',
            'Maxi Dress' => 'dress',
            'Modest Top' => 'top',
            'Islamic Trouser' => 'bottoms',
            'Wide Leg Pant' => 'bottoms',
            'Modest Skirt' => 'bottoms',
            'Scarf' => 'scarf',
        ],
        'men' => [
            'Thobe' => 'thobe',
            'Jubba' => 'thobe',
            'Kandura' => 'thobe',
            'Shalwar Kameez' => 'shalwar_kameez',
            'Kurta' => 'kurta',
            'Islamic T-Shirt' => 'top',
            'Modest Shirt' => 'top',
            'Islamic Trouser' => 'bottoms',
            'Chino Pant' => 'bottoms',
            'Kufi Cap' => 'accessory',
            'Waistcoat' => 'top',
            'Prayer Set' => 'prayer',
        ],
    ],

    'women_pools' => [
        'niqab' => ['photo-1744727811425-e1c0af8b4022', 'photo-1618297655311-ab851e7045d6'],
        'burqa' => ['photo-1744727811425-e1c0af8b4022'],
        'hijab' => ['photo-1561442748-c50715dc32f6', 'photo-1536814294574-df49a3cc97bd'],
        'khimar' => ['photo-1561442748-c50715dc32f6'],
        'abaya' => ['photo-1772474542630-5f5822ca8421', 'photo-1668028554854-245f8ccae15b'],
        'jilbab' => ['photo-1668028554854-245f8ccae15b'],
        'dress' => ['photo-1768830985958-e8d3a93d3f14', 'photo-1546246380-70f857cf5310'],
        'top' => ['photo-1708151729075-89f1fc21e19e'],
        'scarf' => ['photo-1585728748176-455ac5eed962'],
        'bottoms' => ['photo-1771162766051-c330f1d664ea', 'photo-1651828855150-ba40f6870a53'],
    ],

    'men_pools' => [
        'shalwar_kameez' => ['photo-1734418050767-1d0f3d98b3d9', 'photo-1756412066323-a336d2becc10'],
        'kurta' => ['photo-1774527929835-282b1b85cd3a', 'photo-1723932179257-4d92f7c54a4b'],
        'thobe' => ['photo-1759567066672-4b9f48000096', 'photo-1774424420923-6936309c3c5e'],
        'top' => ['photo-1561313021-9c964ccfc7e2', 'photo-1550546094-9835463f9f71'],
        'prayer' => ['photo-1757143137159-316220046829'],
        'accessory' => ['photo-1588594509615-62de3570d696'],
        'bottoms' => ['photo-1539584853854-e196bc0ec4e2', 'photo-1547527392-bd5d50305ca0'],
    ],

    'crop_variants' => [
        '',
        '&crop=entropy&h=1350',
        '&crop=top&h=1280',
        '&crop=entropy&h=1200&fp-x=0.35',
        '&crop=entropy&h=1200&fp-x=0.65',
    ],

    'gen_z_adjectives' => ['Midnight', 'Cloud', 'Noor', 'Street', 'Soft', 'Urban', 'Eid', 'Desert', 'Velvet', 'Satin', 'Core', 'Archive', 'Daily', 'Layer', 'Clean', 'Oversized', 'Tailored', 'Heritage', 'Modern', 'Essential'],
    'gen_z_suffixes' => ['Drop', 'Edit', 'Set', 'Fit', 'Capsule', 'Release', 'Essential', 'Studio', 'Collective', 'Line'],
];
