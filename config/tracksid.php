<?php

return [
    'sandi' => [
        'dev_token' => env('TRACKSID_DEV_TOKEN'),
        'mapbox_token' => env('TRACKSID_MAPBOX_TOKEN'),
        'sanctum_token' => env('TRACKSID_SANCTUM_TOKEN')
    ],
    'abaikan' => env('TRACKSID_ABAIKAN', 'devpremium.opendesa.id|devumum.opendesa.id|dev.opendesa.id|demosid.opendesa.id|beta.opendesa.id|berputar.opendesa.id|demo.opensid.my.id|demo.opensid.or.id|opensid.id|beta.opensid.my.id|berputar.opensid.my.id|beta.opensid.or.id|berputar.opensid.or.id|opensid.my.id|sistemdesa.sunshinecommunity.id'),
    'abaikan_opendk' => env('TRACKSID_ABAIKAN_OPENDK', 'demodk.opendesa.id'),
    'desa_contoh' => [
        'kode_desa' => '52.01.14.2006',
        'lat' => '-8.483832804795249',
        'lng' => '116.08523368835449',
    ],
    'pantau_provinsi' => [
        '52' => 'Nusa Tenggara Barat',
    ],
];