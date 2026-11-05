<?php

/**
 * Test manual perubahan domain desa — disimulasikan seolah-olah dikirim dari OpenSID.
 *
 * File ini mem-bootstrap aplikasi Laravel lalu mengirim request HTTP sungguhan
 * ke route `POST /api/track/desa` (endpoint yang dipakai crawler OpenSID),
 * lengkap dengan middleware `tracksid` (Bearer token) dan seluruh validasi
 * TrackRequest. Jadi alur yang teruji sama persis dengan kondisi produksi.
 *
 * Skenario:
 *   1. OpenSID mengirim data desa dengan domain LAMA.
 *   2. OpenSID mengirim data desa dengan domain BARU (kode_desa sama).
 *   3. Verifikasi:
 *      - hanya ada 1 entri desa untuk kode_desa tersebut (tidak duplikat)
 *      - domain_histories berisi 2 baris (lama + baru)
 *      - hanya 1 baris dengan is_active = true
 *      - desa.domain berisi domain terakhir (baru)
 *
 * Cara pakai:
 *   php scripts/test-perubahan-domain.php
 *   php scripts/test-perubahan-domain.php --desa=11.01.01.2001
 *   php scripts/test-perubahan-domain.php --rollback
 *   php scripts/test-perubahan-domain.php --keep     (jangan bersihkan data uji)
 *
 * Exit code: 0 bila semua pemeriksaan lulus, 1 bila ada yang gagal.
 */

use Illuminate\Contracts\Http\Kernel;
use Illuminate\Http\Request;

// ---------------------------------------------------------------------------
// Bootstrap aplikasi Laravel
// ---------------------------------------------------------------------------

define('LARAVEL_START', microtime(true));

require __DIR__.'/../vendor/autoload.php';

/* @var \Illuminate\Foundation\Application $app */
$app = require __DIR__.'/../bootstrap/app.php';

/* @var \Illuminate\Contracts\Console\Kernel $console */
$console = $app->make(Illuminate\Contracts\Console\Kernel::class);
$console->bootstrap();

/* @var Kernel $kernel */
$kernel = $app->make(Kernel::class);

// ---------------------------------------------------------------------------
// Helper tampilan
// ---------------------------------------------------------------------------

function bold(string $text): string
{
    return "\033[1m".$text."\033[0m";
}

function ok(string $text): string
{
    return "\033[32m[OK]\033[0m   ".$text;
}

function fail(string $text): string
{
    return "\033[31m[GAGAL]\033[0m ".$text;
}

function info(string $text): string
{
    return "\033[36m[INFO]\033[0m ".$text;
}

function warn(string $text): string
{
    return "\033[33m[WARN]\033[0m ".$text;
}

/**
 * Cetak tabel sederhana.
 */
function table(array $headers, array $rows): void
{
    $widths = [];
    foreach ($headers as $i => $header) {
        $widths[$i] = mb_strlen($header);
    }
    foreach ($rows as $row) {
        foreach ($row as $i => $cell) {
            $widths[$i] = max($widths[$i], mb_strlen((string) $cell));
        }
    }

    $line = '+';
    foreach ($widths as $width) {
        $line .= str_repeat('-', $width + 2).'+';
    }

    echo $line."\n";
    echo '|';
    foreach ($headers as $i => $header) {
        echo ' '.str_pad($header, $widths[$i]).' |';
    }
    echo "\n".$line."\n";

    foreach ($rows as $row) {
        echo '|';
        foreach ($row as $i => $cell) {
            echo ' '.str_pad((string) $cell, $widths[$i]).' |';
        }
        echo "\n";
    }

    echo $line."\n";
}

// ---------------------------------------------------------------------------
// Opsi command line
// ---------------------------------------------------------------------------

$options = getopt('', ['desa::', 'rollback', 'keep', 'help']);

if (isset($options['help'])) {
    echo <<<'TXT'
    Test perubahan domain desa (simulasi pengiriman dari OpenSID)

    Opsi:
      --desa=<kode_desa>   Kode desa yang diuji (default: ambil dari kode_wilayah)
      --rollback           Kembalikan data ke kondisi semula (hapus riwayat uji)
      --keep               Jangan bersihkan data uji setelah selesai
      --help               Tampilkan bantuan ini

    Contoh:
      php scripts/test-perubahan-domain.php
      php scripts/test-perubahan-domain.php --desa=11.01.01.2001
      php scripts/test-perubahan-domain.php --rollback

    TXT;
    echo "\n";
    exit(0);
}

$kodeDesaArg = $options['desa'] ?? null;
$rollback = isset($options['rollback']);
$keep = isset($options['keep']);

echo "\n".bold('=== Test Perubahan Domain Desa (simulasi OpenSID) ===')."\n";

// Token yang dipakai middleware tracksid.
$token = config('tracksid.sandi.dev_token');

if (empty($token)) {
    echo fail('Token `tracksid.sandi.dev_token` kosong. Isi TRACKSID_DEV_TOKEN di .env.')."\n";
    exit(1);
}

// ---------------------------------------------------------------------------
// Siapkan target uji
// ---------------------------------------------------------------------------

if ($rollback) {
    if (! $kodeDesaArg) {
        echo fail('Opsi --rollback membutuhkan --desa=<kode_desa>.')."\n";
        exit(1);
    }

    $terhapus = Illuminate\Support\Facades\DB::table('domain_histories')
        ->where('kode_desa', $kodeDesaArg)
        ->delete();

    Illuminate\Support\Facades\DB::table('desa')
        ->where('kode_desa', $kodeDesaArg)
        ->update(['domain' => null, 'domain_updated_at' => null]);

    echo ok(sprintf('Riwayat domain untuk %s dihapus (%d baris).', $kodeDesaArg, $terhapus))."\n";
    echo ok(sprintf('desa.domain untuk %s dikosongkan.', $kodeDesaArg))."\n";
    exit(0);
}

/* @var App\Models\Wilayah|null $wilayah */
$wilayah = null;

if ($kodeDesaArg) {
    $wilayah = App\Models\Wilayah::where('kode_desa', $kodeDesaArg)->first();

    if (! $wilayah) {
        echo fail(sprintf('Kode desa %s tidak ditemukan pada tabel kode_wilayah.', $kodeDesaArg))."\n";
        exit(1);
    }
} else {
    $wilayah = App\Models\Wilayah::whereNotNull('kode_desa')
        ->where('kode_desa', '<>', '')
        ->whereNotNull('nama_desa')
        ->inRandomOrder()
        ->first();
}

if (! $wilayah) {
    echo fail('Tidak ada data wilayah untuk pengujian.')."\n";
    exit(1);
}

$kodeDesa = $wilayah->kode_desa;

echo info('Target uji : '.$wilayah->nama_desa.' ('.$kodeDesa.')');

// Bersihkan sisa data uji sebelumnya agar hasil deterministik.
Illuminate\Support\Facades\DB::table('domain_histories')->where('kode_desa', $kodeDesa)->delete();
Illuminate\Support\Facades\DB::table('desa')
    ->where('kode_desa', $kodeDesa)
    ->update(['domain' => null, 'domain_updated_at' => null]);

// ---------------------------------------------------------------------------
// Kirim request seolah-olah berasal dari OpenSID
// ---------------------------------------------------------------------------

/**
 * Kirim payload tracking OpenSID ke route POST /api/track/desa.
 */
function kirimDariOpenSID(Kernel $kernel, array $payload, string $token): array
{
    $server = [
        'HTTP_AUTHORIZATION' => 'Bearer '.$token,
        'HTTP_ACCEPT' => 'application/json',
        'CONTENT_TYPE' => 'application/json',
        'REMOTE_ADDR' => '203.0.113.10',
    ];

    $request = Request::create(
        '/api/track/desa',
        'POST',
        [],
        [],
        [],
        $server,
        json_encode($payload)
    );

    $response = $kernel->handle($request);

    $status = $response->getStatusCode();
    $content = $response->getContent();
    $response->getContent();

    return [
        'status' => $status,
        'body' => $content,
    ];
}

/**
 * Susun payload tracking OpenSID untuk sebuah wilayah.
 *
 * Catatan: OpenSID mengirim kode wilayah tanpa titik (mis. 1101012001).
 * Request class-lah yang memformatnya kembali menjadi kode bertitik.
 */
function payloadOpenSID(App\Models\Wilayah $wilayah, string $domain, string $versi = '2605.0.0'): array
{
    return [
        'nama_desa' => $wilayah->nama_desa,
        'kode_desa' => tanpaTitik($wilayah->kode_desa),
        'kode_pos' => '23111',
        'nama_kecamatan' => $wilayah->nama_kec,
        'kode_kecamatan' => tanpaTitik($wilayah->kode_kec),
        'nama_kabupaten' => $wilayah->nama_kab,
        'kode_kabupaten' => tanpaTitik($wilayah->kode_kab),
        'nama_provinsi' => $wilayah->nama_prov,
        'kode_provinsi' => tanpaTitik($wilayah->kode_prov),
        'lat' => '-0.789',
        'lng' => '113.456',
        'alamat_kantor' => 'Alamat Kantor Uji Domain',
        'url' => 'https://'.$domain,
        'ip_address' => '203.0.113.10',
        'external_ip' => '203.0.113.10',
        'version' => $versi,
        'jml_surat_tte' => 5,
        'modul_tte' => '1',
    ];
}

/**
 * Hapus titik dari kode wilayah (1101012001 <-> 11.01.01.2001).
 */
function tanpaTitik(?string $kode): string
{
    return str_replace('.', '', (string) $kode);
}

$domainLama = 'desa-lama-uji.example.id';
$domainBaru = 'desa-baru-uji.example.id';

echo "\n".bold('1. OpenSID mengirim data dengan domain LAMA')."\n";
$hasilLama = kirimDariOpenSID($kernel, payloadOpenSID($wilayah, $domainLama), $token);

if ($hasilLama['status'] !== 200) {
    echo fail('HTTP '.$hasilLama['status'].' — '.substr((string) $hasilLama['body'], 0, 300))."\n";
    exit(1);
}
echo ok('HTTP 200 — request diterima, domain "'.$domainLama.'" tercatat.')."\n";

echo "\n".bold('2. OpenSID mengirim data dengan domain BARU (kode_desa sama)')."\n";
$hasilBaru = kirimDariOpenSID($kernel, payloadOpenSID($wilayah, $domainBaru), $token);

if ($hasilBaru['status'] !== 200) {
    echo fail('HTTP '.$hasilBaru['status'].' — '.substr((string) $hasilBaru['body'], 0, 300))."\n";
    exit(1);
}
echo ok('HTTP 200 — request diterima, domain "'.$domainBaru.'" tercatat.')."\n";

// ---------------------------------------------------------------------------
// Verifikasi hasil
// ---------------------------------------------------------------------------

echo "\n".bold('3. Verifikasi hasil')."\n";

$errors = [];

$jumlahDesa = Illuminate\Support\Facades\DB::table('desa')
    ->where('kode_desa', $kodeDesa)
    ->count();

if ($jumlahDesa === 1) {
    echo ok('Hanya ada 1 entri desa untuk kode_desa '.$kodeDesa.' (tidak ada duplikasi).')."\n";
} else {
    $errors[] = 'Entri desa untuk kode_desa '.$kodeDesa.' berjumlah '.$jumlahDesa.' (harus 1).';
    echo fail('Entri desa untuk kode_desa '.$kodeDesa.' berjumlah '.$jumlahDesa.' (harus 1).');
}

$riwayat = Illuminate\Support\Facades\DB::table('domain_histories')
    ->where('kode_desa', $kodeDesa)
    ->orderBy('id')
    ->get();

if ($riwayat->count() === 2) {
    echo ok('Domain historis tersimpan 2 baris (domain lama + domain baru).')."\n";
} else {
    $errors[] = 'Riwayat domain berjumlah '.$riwayat->count().' baris (harus 2).';
    echo fail('Riwayat domain berjumlah '.$riwayat->count().' baris (harus 2).');
}

$jumlahAktif = Illuminate\Support\Facades\DB::table('domain_histories')
    ->where('kode_desa', $kodeDesa)
    ->where('is_active', 1)
    ->count();

if ($jumlahAktif === 1) {
    echo ok('Tepat 1 domain berstatus aktif.')."\n";
} else {
    $errors[] = 'Domain aktif berjumlah '.$jumlahAktif.' (harus 1).';
    echo fail('Domain aktif berjumlah '.$jumlahAktif.' (harus 1).');
}

$domainSekarang = Illuminate\Support\Facades\DB::table('desa')
    ->where('kode_desa', $kodeDesa)
    ->value('domain');

if ($domainSekarang === $domainBaru) {
    echo ok('desa.domain berisi domain terakhir ("'.$domainSekarang.'").')."\n";
} else {
    $errors[] = 'desa.domain berisi "'.$domainSekarang.'" (harus "'.$domainBaru.'").';
    echo fail('desa.domain berisi "'.$domainSekarang.'" (harus "'.$domainBaru.'").');
}

$domainLamaAda = Illuminate\Support\Facades\DB::table('domain_histories')
    ->where('kode_desa', $kodeDesa)
    ->where('domain_normalized', $domainLama)
    ->where('is_active', 0)
    ->exists();

if ($domainLamaAda) {
    echo ok('Domain lama tersimpan sebagai riwayat dengan is_active = false.')."\n";
} else {
    $errors[] = 'Domain lama tidak ditemukan pada riwayat dengan is_active = false.';
    echo fail('Domain lama tidak ditemukan pada riwayat dengan is_active = false.');
}

// ---------------------------------------------------------------------------
// Tampilkan tabel riwayat
// ---------------------------------------------------------------------------

echo "\n".bold('4. Isi tabel domain_histories')."\n";

$rows = [];
foreach ($riwayat as $baris) {
    $rows[] = [
        $baris->id,
        $baris->domain,
        $baris->domain_normalized,
        $baris->is_active ? 'AKTIF' : 'tidak aktif',
        $baris->source ?? '-',
        $baris->obtained_at ?? '-',
        $baris->note ?? '-',
    ];
}

table(
    ['id', 'domain', 'domain_normalized', 'status', 'source', 'obtained_at', 'note'],
    $rows
);

echo "\n".bold('5. Daftar desa untuk kode_desa '.$kodeDesa)."\n";

$rowsDesa = Illuminate\Support\Facades\DB::table('desa')
    ->where('kode_desa', $kodeDesa)
    ->get(['id', 'nama_desa', 'kode_desa', 'domain', 'domain_updated_at']);

table(
    ['id', 'nama_desa', 'kode_desa', 'domain', 'domain_updated_at'],
    $rowsDesa->map(fn ($d) => [$d->id, $d->nama_desa, $d->kode_desa, $d->domain ?? '-', $d->domain_updated_at ?? '-'])->all()
);

// ---------------------------------------------------------------------------
// Bersihkan data uji (kecuali bila --keep)
// ---------------------------------------------------------------------------

echo "\n";

if (! $keep) {
    Illuminate\Support\Facades\DB::table('domain_histories')->where('kode_desa', $kodeDesa)->delete();
    Illuminate\Support\Facades\DB::table('desa')
        ->where('kode_desa', $kodeDesa)
        ->update(['domain' => null, 'domain_updated_at' => null]);

    echo warn('Data uji dibersihkan. Jalankan dengan --keep bila ingin menyimpan hasil uji.');
} else {
    echo info('Data uji disimpan (--keep).');
}

echo "\n";

if (empty($errors)) {
    echo "\033[32m".bold('=== SEMUA PEMERIKSAAN LULUS ===')."\033[0m\n\n";
    exit(0);
}

echo "\033[31m".bold('=== ADA PEMERIKSAAN YANG GAGAL ===')."\033[0m\n";
foreach ($errors as $error) {
    echo '- '.$error;
}
echo "\n";

exit(1);
