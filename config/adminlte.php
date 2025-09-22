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
    | Favicon
    |--------------------------------------------------------------------------
    |
    | Here you can activate the favicon.
    |
    | For detailed instructions you can look the favicon section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Basic-Configuration
    |
    */

    'use_ico_only' => true,
    'use_full_favicon' => false,

    /*
    |--------------------------------------------------------------------------
    | Logo
    |--------------------------------------------------------------------------
    |
    | Here you can change the logo of your admin panel.
    |
    | For detailed instructions you can look the logo section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Basic-Configuration
    |
    */

    'logo' => '<b>Pantau</b>SID',
    'logo_img' => 'assets/img/opensid_logo.png',
    'logo_img_class' => 'brand-image img-circle elevation-3',
    'logo_img_xl' => null,
    'logo_img_xl_class' => 'brand-image-xs',
    'logo_img_alt' => 'PantauSID',

    /*
    |--------------------------------------------------------------------------
    | User Menu
    |--------------------------------------------------------------------------
    |
    | Here you can activate and change the user menu.
    |
    | For detailed instructions you can look the user menu section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Basic-Configuration
    |
    */

    'usermenu_enabled' => true,
    'usermenu_header' => false,
    'usermenu_header_class' => 'bg-primary',
    'usermenu_image' => true,
    'usermenu_desc' => true,
    'usermenu_profile_url' => false,

    /*
    |--------------------------------------------------------------------------
    | Layout
    |--------------------------------------------------------------------------
    |
    | Here we change the layout of your admin panel.
    |
    | For detailed instructions you can look the layout section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Layout-and-Styling-Configuration
    |
    */

    'layout_topnav' => null,
    'layout_boxed' => null,
    'layout_fixed_sidebar' => true,
    'layout_fixed_navbar' => null,
    'layout_fixed_footer' => true,
    'layout_dark_mode' => null,

    /*
    |--------------------------------------------------------------------------
    | Authentication Views Classes
    |--------------------------------------------------------------------------
    |
    | Here you can change the look and behavior of the authentication views.
    |
    | For detailed instructions you can look the auth classes section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Layout-and-Styling-Configuration
    |
    */

    'classes_auth_card' => 'card-outline',
    'classes_auth_header' => '',
    'classes_auth_body' => '',
    'classes_auth_footer' => '',
    'classes_auth_icon' => '',
    'classes_auth_btn' => 'btn-sm btn-primary',

    /*
    |--------------------------------------------------------------------------
    | Admin Panel Classes
    |--------------------------------------------------------------------------
    |
    | Here you can change the look and behavior of the admin panel.
    |
    | For detailed instructions you can look the admin panel classes here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Layout-and-Styling-Configuration
    |
    */

    'classes_body' => '',
    'classes_brand' => '',
    'classes_brand_text' => '',
    'classes_content_wrapper' => '',
    'classes_content_header' => '',
    'classes_content' => '',
    'classes_sidebar' => 'sidebar-dark-primary elevation-4',
    'classes_sidebar_nav' => '',
    'classes_topnav' => 'navbar-white navbar-light',
    'classes_topnav_nav' => 'navbar-expand',
    'classes_topnav_container' => 'container',

    /*
    |--------------------------------------------------------------------------
    | Sidebar
    |--------------------------------------------------------------------------
    |
    | Here we can modify the sidebar of the admin panel.
    |
    | For detailed instructions you can look the sidebar section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Layout-and-Styling-Configuration
    |
    */

    'sidebar_mini' => 'lg',
    'sidebar_collapse' => false,
    'sidebar_collapse_auto_size' => false,
    'sidebar_collapse_remember' => true,
    'sidebar_collapse_remember_no_transition' => true,
    'sidebar_scrollbar_theme' => 'os-theme-light',
    'sidebar_scrollbar_auto_hide' => 'l',
    'sidebar_nav_accordion' => true,
    'sidebar_nav_animation_speed' => 300,

    /*
    |--------------------------------------------------------------------------
    | Control Sidebar (Right Sidebar)
    |--------------------------------------------------------------------------
    |
    | Here we can modify the right sidebar aka control sidebar of the admin panel.
    |
    | For detailed instructions you can look the right sidebar section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Layout-and-Styling-Configuration
    |
    */

    'right_sidebar' => false,
    'right_sidebar_icon' => 'fas fa-cogs',
    'right_sidebar_theme' => 'dark',
    'right_sidebar_slide' => true,
    'right_sidebar_push' => true,
    'right_sidebar_scrollbar_theme' => 'os-theme-light',
    'right_sidebar_scrollbar_auto_hide' => 'l',

    /*
    |--------------------------------------------------------------------------
    | URLs
    |--------------------------------------------------------------------------
    |
    | Here we can modify the url settings of the admin panel.
    |
    | For detailed instructions you can look the urls section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Basic-Configuration
    |
    */

    'use_route_url' => false,
    'dashboard_url' => '/dashboard',
    'logout_url' => 'logout',
    'login_url' => 'login',
    'register_url' => false,
    'password_reset_url' => 'password/reset',
    'password_email_url' => 'password/email',
    'profile_url' => false,

    /*
    |--------------------------------------------------------------------------
    | Laravel Mix
    |--------------------------------------------------------------------------
    |
    | Here we can enable the Laravel Mix option for the admin panel.
    |
    | For detailed instructions you can look the laravel mix section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Other-Configuration
    |
    */

    'enabled_laravel_mix' => false,
    'laravel_mix_css_path' => 'css/app.css',
    'laravel_mix_js_path' => 'js/app.js',

    /*
    |--------------------------------------------------------------------------
    | Menu Utama
    |--------------------------------------------------------------------------
    |
    | Here we can modify the sidebar/top navigation of the admin panel.
    |
    | For detailed instructions you can look here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Menu-Configuration
    |
    */

    'menu' => [
        // Navbar items
        [
            'type' => 'darkmode-widget',
            'topnav_right' => true, // Or "topnav => true" to place on the left.
        ],
        [
            'type' => 'fullscreen-widget',
            'topnav_right' => true,
        ],

        // Menu
        ['header' => 'MENU UTAMA'],
        [
            'text' => 'Dasbor Web',
            'url' => '/web',
            'icon' => 'fas fa-globe',
            'can' => 'web.view',
        ],
        [
            'text' => 'OpenSID',
            'icon' => 'fas fa-file-alt',
            'can' => 'laporan.view',
            'submenu' => [
                        [
                    'text' => 'Dasbor',
                    'url' => '/dashboard',
                    'icon' => 'fas fa-tachometer-alt',
                    'can' => 'dashboard.view',
                ],
                [
                    'text' => 'Peta Sebaran',
                    'url' => 'peta',
                    'icon' => 'fas fa-map-marked-alt',
                    'can' => 'peta.view',
                ],
                [
                    'text' => 'Desa',
                    'url' => 'laporan/desa',
                    'can' => 'laporan.desa.view',
                ],
                [
                    'text' => 'Kecamatan',
                    'url' => 'laporan/kecamatan',
                    'can' => 'laporan.kecamatan.view',
                ],
                [
                    'text' => 'Kabupaten',
                    'url' => 'laporan/kabupaten',
                    'can' => 'laporan.kabupaten.view',
                ],
                [
                    'text' => 'Versi',
                    'url' => 'laporan/versi',
                    'can' => 'laporan.versi.view',
                ],
                [
                    'text' => 'Desa Aktif',
                    'url' => 'laporan/desa-aktif',
                    'can' => 'laporan.desa-aktif.view',
                ],
                [
                    'can' => 'review.non-aktif.view',
                    'text' => 'Desa Tidak Aktif',
                    'url' => 'review/non-aktif',
                ],
                [
                    'can' => 'review.desa-baru.view',
                    'text' => 'Desa Baru',
                    'url' => 'review/desa-baru',
                ],
                [
                    'text' => 'API OpenSID Terpasang',
                    'url' => 'laporan/desa-aktif',
                    'can' => 'laporan.desa-aktif.view',
                ],
            ],
        ],
        [
            'text' => 'OpenDK',
            'icon' => 'fas fa-file-alt',
            'can' => 'opendk.view',
            'submenu' => [
                [
                    'text' => 'Dasbor',
                    'url' => 'opendk',
                    'can' => 'opendk.view',
                ],
                [
                    'text' => 'Peta Sebaran',
                    'url' => 'opendk/peta',
                ],
                [
                    'text' => 'Kecamatan',
                    'url' => 'opendk/kecamatan',
                    'can' => 'opendk.kecamatan.view',
                ],
                [
                    'text' => 'Kabupaten',
                    'url' => 'opendk/kabupaten',
                    'can' => 'opendk.kabupaten.view',
                ],
                [
                    'text' => 'Versi',
                    'url' => 'opendk/versi',
                    'can' => 'opendk.versi.view',
                ],
                [
                    'text' => 'Kecamatan Aktif',
                    'url' => 'opendk/kecamatan-aktif',
                ],
            ],
        ],
        [
            'text' => 'LayananDesa',
            'icon' => 'fas fa-file-alt',
            'can' => 'mobile.view',
            'submenu' => [
                [
                    'text' => 'Dasbor',
                    'url' => 'mobile',
                    'can' => 'mobile.view',
                ],
                [
                    'text' => 'Desa',
                    'url' => 'mobile/desa',
                    'can' => 'mobile.desa.view',
                ],
                [
                    'can' => 'mobile.pengguna.view',
                    'text' => 'Pengguna',
                    'url' => 'mobile/pengguna',
                ],
            ],
        ],
        [
            'text' => 'KelolaDesa',
            'icon' => 'fas fa-file-alt',
            'can' => 'mobile.view',
            'submenu' => [
                [
                    'text' => 'Dasbor',
                    'url' => 'kelola_desa',
                    'can' => 'mobile.view',
                ],
                [
                    'can' => 'mobile.desa.view',
                    'text' => 'Desa',
                    'url' => 'kelola_desa/desa',
                ],
                [
                    'can' => 'mobile.pengguna.view',
                    'text' => 'Pengguna',
                    'url' => 'kelola_desa/pengguna',
                ],
            ]
        ],
        [
            'text' => 'OpenKab',
            'icon' => 'fas fa-file-alt',
            'can' => 'openkab.view',
            'submenu' => [
                /*[
                    'text' => 'Wilayah Kerja Sama',
                    'url' => 'openkab/kerja-sama',
                    'can' => 'openkab.kerja-sama.view',
                ],*/
                [
                    'text' => 'Dasbor',
                    'url' => 'openkab/dashboard',
                ],
                [
                    'text' => 'Dasbor',
                    'url' => 'laporan/openkab',
                    'can' => 'laporan.openkab.view',
                ],
                [
                    'text' => 'Pengguna Aktif',
                    'url' => 'laporan/openkab/pengguna',
                    'can' => 'laporan.openkab-pengguna.view',
                ],
                [
                    'text' => 'API Satu Data',
                    'url' => 'openkab/pengguna',
                ],
            ],
        ],
        [
            'text' => 'PBB',
            'icon' => 'fas fa-file-alt',
            'can' => 'pbb.view',
            'submenu' => [
                [
                    'text' => 'Desa',
                    'url' => 'pbb/kecamatan',
                    'can' => 'pbb.kecamatan.view',
                ],
                [
                    'text' => 'Kabupaten',
                    'url' => 'pbb/kabupaten',
                    'can' => 'pbb.kabupaten.view',
                ],
                [
                    'text' => 'Versi',
                    'url' => 'pbb/versi',
                    'can' => 'pbb.versi.view',
                ],
                [
                    'text' => 'Pengguna Aktif',
                    'url' => 'pbb/pengguna-aktif',
                ],
            ],
        ],
        [
            'text' => 'Pengguna Tema',
            'icon' => 'fas fa-file-alt',
            'submenu' => [
                [
                    'text' => 'Pengguna Tema Pro',
                    'url' => 'laporan/tema-pro',
                    'can' => 'laporan.tema-pro.view',
                ],
                [
                    'text' => 'Pengguna Tema Bawaan',
                    'url' => 'laporan/tema',
                    'can' => 'laporan.tema.view',
                ],
            ]
        ],
        [
            'text' => 'Data Master',
            'icon' => 'fas fa-file-alt',
            'submenu' => [
                [
                    'text' => 'Data Suku',
                    'url' => 'suku',
                    'icon' => 'fas fa-users',
                    'can' => 'suku.view',
                ],
                [
                    'text' => 'Data Marga',
                    'url' => 'marga',
                    'icon' => 'fas fa-users',
                    'can' => 'marga.view',
                ],
                [
                    'text' => 'Data Adat',
                    'url' => 'adat',
                    'icon' => 'fas fa-landmark',
                    'can' => 'adat.view',
                ],
                [
                    'text' => 'Wilayah Administratif',
                    'url' => 'wilayah',
                    'icon' => 'fas fa-map',
                    'can' => 'wilayah.view',
                ],

                [
                    'text' => 'Data Pekerjaan PMI',
                    'url' => 'pekerjaan-pmi',
                    'icon' => 'fas fa-briefcase',
                    'can' => 'pekerjaan-pmi.view',
                ],
            ]
        ],
        [
            'key' => 'khusus',
            'text' => 'Wilayah',
            'topnav_right' => true,
            'submenu' => [
                [
                    'text' => 'Semua',
                    'url' => 'sesi/hapus',
                ],
            ],
        ],

        [
            'text' => '',
            'url' => '#',
            'icon' => 'fas fa-info-circle',
            'topnav_right' => true,
            'id' => 'releaseNotesButton',
        ],
        /*[
            'can' => 'akses.bersihkan.view',
            'text' => 'Bersihkan Data Akses',
            'url' => 'akses/bersihkan',
            'icon' => 'fas fa-recycle',
        ],*/

        // Data Wilayah
        [
            'can' => 'data-wilayah.view',
            'text' => 'Data Wilayah',
            'icon' => 'fas fa-file-alt',
            'submenu' => [
                [
                    'can' => 'provinsi.view',
                    'text' => 'Provinsi',
                    'url' => 'provinsi',
                ],
                [
                    'can' => 'kabupaten.view',
                    'text' => 'Kabupaten',
                    'url' => 'kabupaten',
                ],
                [
                    'can' => 'kecamatan.view',
                    'text' => 'Kecamatan',
                    'url' => 'kecamatan',
                ],
                [
                    'can' => 'desa.view',
                    'text' => 'Desa',
                    'url' => 'desa',
                ],
            ],
        ],

        // Manajemen Pengguna
        [
            'header' => 'pengguna',
            'can' => 'pengguna.view',
        ],
        [
            'can' => 'pengguna.view',
            'text' => 'Pengguna',
            'url' => 'akun-pengguna',
            'icon' => 'fas fa-users',
        ],

        // Settings
        [
            'text' => 'Pengaturan',
            'icon' => 'fas fa-file-alt',
            'can' => 'pengaturan.view',
            'submenu' => [
                [
                    'can' => 'pengaturan.aplikasi.view',
                    'text' => 'aplikasi',
                    'url' => 'pengaturan/aplikasi',
                    'icon' => 'fas fa-fw fa-newspaper',
                ],
                [
                    'can' => 'profile.view',
                    'text' => 'profile',
                    'url' => 'profile',
                    'icon' => 'fas fa-fw fa-user',
                ],
                [
                    'can' => 'profile.change-password.view',
                    'text' => 'change_password',
                    'url' => 'profile/reset-password',
                    'icon' => 'fas fa-fw fa-lock',
                ],
            ]
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Menu Filters
    |--------------------------------------------------------------------------
    |
    | Here we can modify the menu filters of the admin panel.
    |
    | For detailed instructions you can look the menu filters section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Menu-Configuration
    |
    */

    'filters' => [
        JeroenNoten\LaravelAdminLte\Menu\Filters\GateFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\HrefFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\SearchFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\ActiveFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\ClassesFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\LangFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\DataFilter::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Plugins Initialization
    |--------------------------------------------------------------------------
    |
    | Here we can modify the plugins used inside the admin panel.
    |
    | For detailed instructions you can look the plugins section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Plugins-Configuration
    |
    */

    'plugins' => [
        'Datatables' => [
            'active' => true,
            'files' => [
                [
                    'type' => 'js',
                    'asset' => false,
                    'location' => '//cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js',
                ],
                [
                    'type' => 'js',
                    'asset' => false,
                    'location' => '//cdn.datatables.net/1.10.19/js/dataTables.bootstrap4.min.js',
                ],
                [
                    'type' => 'css',
                    'asset' => false,
                    'location' => '//cdn.datatables.net/1.10.19/css/dataTables.bootstrap4.min.css',
                ],
            ],
        ],
        'Select2' => [
            'active' => true,
            'files' => [
                [
                    'type' => 'js',
                    'asset' => false,
                    'location' => '//cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.min.js',
                ],
                [
                    'type' => 'css',
                    'asset' => false,
                    'location' => '//cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.css',
                ],
            ],
        ],
        'Chartjs' => [
            'active' => false,
            'files' => [
                [
                    'type' => 'js',
                    'asset' => false,
                    'location' => '//cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.0/Chart.bundle.min.js',
                ],
            ],
        ],
        'Sweetalert2' => [
            'active' => false,
            'files' => [
                [
                    'type' => 'js',
                    'asset' => false,
                    'location' => '//cdn.jsdelivr.net/npm/sweetalert2@8',
                ],
            ],
        ],
        'Pace' => [
            'active' => false,
            'files' => [
                [
                    'type' => 'css',
                    'asset' => false,
                    'location' => '//cdnjs.cloudflare.com/ajax/libs/pace/1.0.2/themes/blue/pace-theme-center-radar.min.css',
                ],
                [
                    'type' => 'js',
                    'asset' => false,
                    'location' => '//cdnjs.cloudflare.com/ajax/libs/pace/1.0.2/pace.min.js',
                ],
            ],
        ],
        'BsCustomFileInput' => [
            'active' => false,
            'files' => [
                [
                    'type' => 'js',
                    'asset' => true,
                    'location' => 'vendor/bs-custom-file-input/bs-custom-file-input.min.js',
                ],
            ],
        ]
    ],

    /*
    |--------------------------------------------------------------------------
    | IFrame
    |--------------------------------------------------------------------------
    |
    | Here we change the IFrame mode configuration. Note these changes will
    | only apply to the view that extends and enable the IFrame mode.
    |
    | For detailed instructions you can look the iframe mode section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/IFrame-Mode-Configuration
    |
    */

    'iframe' => [
        'default_tab' => [
            'url' => null,
            'title' => null,
        ],
        'buttons' => [
            'close' => true,
            'close_all' => true,
            'close_all_other' => true,
            'scroll_left' => true,
            'scroll_right' => true,
            'fullscreen' => true,
        ],
        'options' => [
            'loading_screen' => 1000,
            'auto_show_new_tab' => true,
            'use_navbar_items' => true,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Livewire
    |--------------------------------------------------------------------------
    |
    | Here we can enable the Livewire support.
    |
    | For detailed instructions you can look the livewire here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Other-Configuration
    |
    */

    'livewire' => false,
];
