<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Title
    |--------------------------------------------------------------------------
    |
    | Here you can change the default title of your admin panel.
    |
    | For detailed instructions you can look the title section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Basic-Configuration
    |
    */

    'title' => 'PantauSID',
    'title_prefix' => 'SID',
    'title_postfix' => '',

    /*
    |--------------------------------------------------------------------------
    | Menu Untuk Pantau OpenSID
    |--------------------------------------------------------------------------
    |
    | Here we can modify the menu filters of the admin panel.
    |
    | For detailed instructions you can look the menu filters section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Menu-Configuration
    |
    */
    'menu' => [
        [
            'text'         => 'Dashboard',
            'url'          => '/',
            'icon'         => 'fas fa-tachometer-alt',
        ],
        [
            'text'         => 'Peta OpenSID',
            'url'          => 'peta',
            'icon'         => 'fas fa-map-marked-alt',
        ],
        [
            'text'         => 'Laporan',
            'icon'         => 'fas fa-file-alt',
            'submenu'      => [
                [
                    'text' => 'Desa OpenSID',
                    'url'  => 'laporan/desa',
                ],
                [
                    'text' => 'Kabupaten OpenSID',
                    'url'  => 'laporan/kabupaten',
                ],
                [
                    'text' => 'Versi OpenSID',
                    'url'  => 'laporan/versi',
                ],
            ],
        ],
    ],
];
