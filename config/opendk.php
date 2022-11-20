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
        // Menu
        ['header'          => 'OPENDK'],
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
