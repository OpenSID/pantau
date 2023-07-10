<?php

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

if (! function_exists('kode_wilayah')) {
    /**
     * Kode wilayah dengan titik dari 5201142005 --> 52.01.14.2005
     *
     * @param mixed $kode_wilayah
     * @return string
     */
    function kode_wilayah($kode_wilayah)
    {
        $kode_prov_kab_kec = str_split(substr($kode_wilayah, 0, 6), 2);
        $kode_desa = (strlen($kode_wilayah) > 6) ? '.'.substr($kode_wilayah, 6) : '';
        $kode_standar = implode('.', $kode_prov_kab_kec).$kode_desa;

        return $kode_standar;
    }
}

if (! function_exists('kode_kecamatan')) {
    /**
     * Kode wilayah dengan titik dari 520114 --> 52.01.14
     *
     * @param mixed $kode_wilayah
     * @return string
     */
    function kode_kecamatan($kode_wilayah)
    {
        $kode_prov_kab_kec = str_split(substr($kode_wilayah, 0, 6), 2);
        $kode_standar = implode('.', $kode_prov_kab_kec);

        return $kode_standar;
    }
}

if (! function_exists('is_local')) {
    /**
     * Validasi local ip.
     *
     * @param mixed $url
     * @return bool
     */
    function is_local($url)
    {
        if (preg_match('/localhost|192\.168|^127\.|\/10\.|^10\./i', $url)) {
            return true;
        } else {
            return false;
        }
    }
}

if (! function_exists('parent_code')) {
    /**
     * Parent Code
     *
     * @param mixed $region_code
     * @return string
     */
    function parent_code($region_code)
    {
        $panjang = strlen($region_code);

        if ($panjang > 8) {
            // Desa => Kecamatan

            return substr($region_code, 0, 8);
        } elseif ($panjang > 5) {
            // Kecamatan => Kabupaten

            return substr($region_code, 0, 5);
        } elseif ($panjang > 2) {
            // Kabupaten => Provinsi

            return substr($region_code, 0, 2);
        } else {
            return 0;
        }
    }
}

if (! function_exists('fixDomainName')) {
    /**
     * Validasi domain.
     *
     * @param  string $url
     * @return string
     */
    function fixDomainName($url = '')
    {
        $strToLower = strtolower(trim($url));
        $httpPregReplace = preg_replace('/^http:\/\//i', '', $strToLower);
        $httpsPregReplace = preg_replace('/^https:\/\//i', '', $httpPregReplace);
        $wwwPregReplace = preg_replace('/^www\./i', '', $httpsPregReplace);
        $explodeToArray = explode('/', $wwwPregReplace);
        $finalDomainName = trim($explodeToArray[0]);

        return $finalDomainName;
    }
}

if (! function_exists('lastrelease')) {
    /**
     * Validasi domain.
     *
     * @param  string $url
     * @return object
     */
    function lastrelease($url)
    {
        try {
            $response = Http::withHeaders([
                'Accept' => 'application/vnd.github.v3+json',
            ])
            ->get($url)
            ->throw();

            return json_decode($response->body());
        } catch (\Throwable $th) {
            return false;
        }
    }
}

if (! function_exists('abaikan_domain')) {
    /**
     * Validasi domain.
     *
     * @param  string $url
     * @return object
     */
    function abaikan_domain($aplikasi)
    {
        switch($aplikasi){
            case 'opendk':
                return Cache::get('abaikan_domain_opendk', '');
                break;
            default:
                return Cache::get('abaikan_domain_opensid', '');
        }
    }
}
