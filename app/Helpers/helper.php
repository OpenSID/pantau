<?php

use App\Models\PengaturanAplikasi;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

if (! function_exists('pantau_versi')) {
    /**c:\xampp\htdocs\OpenDesa\dashboard-saas\catatan_rilis.md
     * OpenKab database gabungan versi.
     */
    function pantau_versi()
    {
        return 'v2506.0.0';
    }
}

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
        } catch (Throwable $th) {
            return false;
        }
    }
}

if (! function_exists('lastrelease_opensid')) {
    /**
     * Validasi domain.
     *
     * @param  string $url
     * @return object
     */
    function lastrelease_opensid()
    {
        $version = Cache::get('opensid_premium_version', '2307.0.1');
        $versi_opensid = lastrelease('https://api.github.com/repos/OpenSID/rilis-premium/releases/latest');

        if ($versi_opensid !== false) {
            $version = str_replace('v', '', $versi_opensid->tag_name);
            Cache::forever('opensid_premium_version', $version);
        }

        return $version;
    }
}

if (! function_exists('lastrelease_pbb')) {
    /**
     * Validasi domain.
     *
     * @param  string $url
     * @return object
     */
    function lastrelease_pbb()
    {
        // Cache data until the end of the day
        $version = Cache::remember('release_pbb', now()->endOfDay(), function () {
            $version = '2401.0.0';
            $versi_pbb = lastrelease('https://api.github.com/repos/OpenSID/rilis-pbb/releases/latest');

            if ($versi_pbb !== false) {
                $version = str_replace('v', '', $versi_pbb->tag_name);
            }

            return $version;
        });

        return $version;
    }
}

if (! function_exists('pantau_wilayah_khusus')) {
    /**
     * Validasi domain.
     *
     * @param  string $url
     * @return object
     */
    function pantau_wilayah_khusus()
    {
        return Cache::get('pantau_wilayah_khusus', []);
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
        switch ($aplikasi) {
            case 'opendk':
                return Cache::get('abaikan_domain_opendk', '');
                break;
            case 'openkab':
                return Cache::get('abaikan_domain_openkab', '');
                break;
            default:
                return Cache::get('abaikan_domain_opensid', '');
        }
    }
}

if (! function_exists('cleanVersi')) {
    /**
     * Convert versi agar sama
     *  22.06 menjadi 2206, versi terbaru menggunakan YYmm bukan YY.mm
     * @param  string $url
     * @return object
     */
    function cleanVersi($version)
    {
        $version = preg_replace('/[^0-9]/', '', $version);

        return substr($version, 0, 4);
    }
}

if (! function_exists('lastrelease_opendk')) {
    /**
     * Validasi domain.
     *
     * @param  string $url
     * @return object
     */
    function lastrelease_opendk()
    {
        // Cache data until the end of the day
        $version = Cache::remember('release_opendk', now()->endOfDay(), function () {
            $version = '2404.0.0';
            $versi_api = lastrelease('https://api.github.com/repos/OpenSID/opendk/releases/latest');

            if ($versi_api !== false) {
                $version = str_replace('v', '', $versi_api->tag_name);
            }

            return $version;
        });

        return $version;
    }

    if (! function_exists('folder_backup')) {
        function folder_backup()
        {
            $folder_backup = 'backup';

            if (! file_exists($folder_backup)) {
                exec('mkdir '.$folder_backup);
            }

            return $folder_backup;
        }
    }

    if (! function_exists('folderBackupDatabase')) {
        function folderBackupDatabase()
        {
            $folder_database = folder_backup().DIRECTORY_SEPARATOR.'database';

            if (! file_exists($folder_database)) {
                exec('mkdir '.$folder_database);
            }

            return $folder_database;
        }
    }

    /** aktifkan backup menggunakan rclone syncs to cloud storage */
    if (! function_exists('rclone_syncs_storage')) {
        function rclone_syncs_storage()
        {
            return file_exists('/usr/bin/rclone') ? true : false;
        }
    }

    /** cloud storage */
    if (! function_exists('cloud_storage')) {
        function cloud_storage()
        {
            return PengaturanAplikasi::get_pengaturan()['cloud_storage'];
        }
    }

    /** waktu backup */
    if (! function_exists('waktu_backup')) {
        function waktu_backup()
        {
            return PengaturanAplikasi::get_pengaturan()['waktu_backup'];
        }
    }

    /** jumlah directory maksimal backup ke storage */
    if (! function_exists('max_backup_dir')) {
        function max_backup_dir()
        {
            return PengaturanAplikasi::get_pengaturan()['maksimal_backup'];
        }
    }

    /** tanggal backup */
    if (! function_exists('tanggal_backup')) {
        function tanggal_backup()
        {
            return PengaturanAplikasi::get_pengaturan()['akhir_backup'];
        }
    }

    /** pengecekan tanggal akhir backup database dan folder desa */
    if (! function_exists('cek_tgl_akhir_backup')) {
        function cek_tgl_akhir_backup($akhir_backup)
        {
            if ($akhir_backup) {
                $hariini = date('Y-m-d');
                $selisih = (strtotime($hariini) - strtotime($akhir_backup)) / 60 / 60 / 24;

                return $selisih;
            }
        }
    }

    if (! function_exists('formatDateTimeForHuman')) {
        function formatDateTimeForHuman($datetime)
        {
            $now = Carbon::now();
            $formattedDateTime = Carbon::parse($datetime);

            // Calculate differences
            $diff = $formattedDateTime->diff($now);

            // Determine the appropriate format based on the difference
            if ($formattedDateTime->isFuture()) {
                if ($diff->y > 0) {
                    return $diff->y.' tahun '.$diff->m.' bulan '.$diff->d.' hari lagi';
                } elseif ($diff->m > 0) {
                    return $diff->m.' bulan '.$diff->d.' hari lagi';
                } elseif ($diff->d > 0) {
                    return $diff->d.' hari lagi';
                } elseif ($diff->h > 0) {
                    return $diff->h.' jam '.$diff->i.' menit lagi';
                } elseif ($diff->i > 0) {
                    return $diff->i.' menit '.$diff->s.' detik lagi';
                } else {
                    return 'baru saja';
                }
            } else {
                if ($diff->y > 0) {
                    return $diff->y.' tahun lalu';
                } elseif ($diff->m > 0) {
                    return $diff->m.' bulan lalu';
                } elseif ($diff->d > 0) {
                    return $diff->d.' hari lalu';
                } elseif ($diff->h > 0) {
                    return $diff->h.' jam lalu';
                } elseif ($diff->i > 0) {
                    return $diff->i.' menit '.$diff->s.' detik lalu';
                } else {
                    return 'baru saja';
                }
            }
        }
    }
}

if (! function_exists('lastrelease_api_layanandesa')) {
    function lastrelease_api_layanandesa()
    {
        // Cache data until the end of the day
        $version = Cache::remember('release_layanan_desa', now()->endOfDay(), function () {
            $version = '2404.0.0';
            $versi_api = lastrelease('https://api.github.com/repos/OpenSID/rilis-opensid-api/releases/latest');

            if ($versi_api !== false) {
                $version = str_replace('v', '', $versi_api->tag_name);
            }

            return $version;
        });

        return $version;
    }
}

if (! function_exists('format_angka')) {
    function format_angka($angka, $decimals = 0)
    {
        return number_format($angka, $decimals, ',', '.');
    }
}

if (! function_exists('local_date')) {
    function local_date($date, $format = 'l, j F Y  H:i:s')
    {
        $date = Carbon::parse($date)->locale('id');
        $date->settings(['formatFunction' => 'translatedFormat']);

        return $date->format($format);
    }
}

if (! function_exists('changeLogPermissions')) {
    function changeLogPermissions($permissions = '777')
    {
        // Path ke folder logs
        $logPath = storage_path('logs');

        // Periksa apakah folder ada
        if (is_dir($logPath)) {
            // Ubah izin sesuai parameter
            exec("chmod -R $permissions $logPath", $output, $returnVar);

            // Cek hasil perintah
            return $returnVar === 0;
        }

        return false;
    }
}
