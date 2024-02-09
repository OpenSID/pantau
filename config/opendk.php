<?php

return [


    'title' => [
        'title' => 'PantauDK',
        'title_prefix' => 'DK',
        'title_postfix' => '',

        'logo' => '<b>Pantau</b>DK',
        'logo_img' => 'assets/img/opendk_logo.png',
        'logo_img_alt' => 'PantauDK',
    ],

    'menu' => [
        [
            'text'         => 'Dasbor',
            'url'          => 'opendk',
            'icon'         => 'fas fa-tachometer-alt',
        ],
        [
            'text'         => 'Peta OpenDK',
            'url'          => 'opendk/peta',
            'icon'         => 'fas fa-map-marked-alt',
        ],
        [
            'text'         => 'Laporan',
            'icon'         => 'fas fa-file-alt',
            'submenu'      => [
                [
                    'text' => 'VERSI OPENDK',
                    'url'  => 'opendk/versi',
                ],
                [
                    'text' => 'Kecamatan Opendk ',
                    'url'  => 'opendk/kecamatan',
                ],
            ],
        ],

    ],
];
