<?php

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
        $kode_desa = (strlen($kode_wilayah) > 6) ? '.' . substr($kode_wilayah, 6) : '';
        $kode_standar = implode('.', $kode_prov_kab_kec) . $kode_desa;

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
        if (preg_match('/localhost|192\.168|:|127\.0\.0\.1|\/10\.|^10\./i', $url)) {
            return true;
        } else {
            return false;
        }
    }
}