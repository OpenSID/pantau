<?php

namespace App\Services;

use App\Models\Desa;
use Illuminate\Support\Collection;

/**
 * Membangun GeoJSON marker peta desa dengan format popup yang seragam.
 *
 * Seluruh nilai berasal dari data kiriman instalasi OpenSID (tidak tepercaya),
 * sehingga wajib di-escape sebelum dirender sebagai HTML oleh Leaflet bindPopup().
 */
class PetaDesaService
{
    /**
     * Kolom minimal yang dibutuhkan untuk membangun marker dan popup.
     */
    public const KOLOM = [
        'id',
        'lat',
        'lng',
        'sebutan_desa',
        'nama_desa',
        'nama_kecamatan',
        'nama_kabupaten',
        'nama_provinsi',
        'alamat_kantor',
        'url_hosting',
    ];

    public static function featureCollection(iterable $desa): array
    {
        return [
            'type' => 'FeatureCollection',
            'features' => Collection::make($desa)->map(fn (Desa $item) => static::feature($item))->values(),
        ];
    }

    public static function feature(Desa $desa): array
    {
        return [
            'type' => 'Feature',
            'geometry' => [
                'type' => 'Point',
                'coordinates' => [
                    (float) $desa->lng,
                    (float) $desa->lat,
                ],
            ],
            'properties' => [
                'logo' => null,
                'popupContent' => static::popup($desa),
            ],
            'id' => $desa->id,
        ];
    }

    public static function popup(Desa $desa): string
    {
        $sebutan = ucwords((string) $desa->sebutan_desa);
        $namaDesa = trim($sebutan.' '.ucwords((string) $desa->nama_desa));

        $baris = [
            $sebutan ?: 'Desa' => $namaDesa,
            'Kecamatan' => ucwords((string) $desa->nama_kecamatan),
            'Kab/Kota' => ucwords((string) $desa->nama_kabupaten),
            'Provinsi' => ucwords((string) $desa->nama_provinsi),
            'Alamat' => (string) $desa->alamat_kantor,
        ];

        $html = '<h6 class="text-center"><b style="color:red">'.e(strtoupper($namaDesa)).'</b></h6>';
        $html .= '<table width="100%">';

        foreach ($baris as $label => $nilai) {
            $html .= '<tr><td>'.e($label).'</td><td> : '.e($nilai).'</td></tr>';
        }

        if (auth()->check() && filled($desa->url_hosting)) {
            $domain = e(strtolower($desa->url_hosting));
            $html .= '<tr><td>Website</td><td> : <a href="http://'.$domain.'" target="_blank" rel="noopener noreferrer">'.$domain.'</a></td></tr>';
        }

        return $html.'</table>';
    }
}
