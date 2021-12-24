<?php

return [
    'sandi' => [
        'dev_token' => env('TRACKSID_DEV_TOKEN', 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpZCI6bnVsbCwidGltZXN0YW1wIjoxNjAzNDY2MjM5fQ.HVCNnMLokF2tgHwjQhSIYo6-2GNXB4-Kf28FSIeXnZw'),
        'mapbox_token' => env('TRACKSID_MAPBOX_TOKEN', 'pk.eyJ1IjoiZWRkaWVyaWR3YW4iLCJhIjoiY2tjd3dyN2VyMDNyZTJybzVvNjF1bzI1MyJ9.XaReLv4etu3mwj7f8A1y2Q'),
        'sanctum_token' => env('TRACKSID_SANCTUM_TOKEN', '1|wWOWzaYeEbrOgz35G1rSIDweeGjDuN5wYL1iSECF')
    ],
    'abaikan' => env('TRACKSID_ABAIKAN', 'demo.opensid.my.id|demo.opensid.or.id|opensid.id|beta.opensid.my.id|berputar.opensid.my.id|beta.opensid.or.id|berputar.opensid.or.id|opensid.my.id|sistemdesa.sunshinecommunity.id'),
];