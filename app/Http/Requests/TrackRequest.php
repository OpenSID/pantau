<?php

namespace App\Http\Requests;

use App\Models\Region;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TrackRequest extends FormRequest
{
    /**
     * {@inheritdoc}
     */
    public function authorize()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            'nama_desa' => ['required', "not_regex:/[^\.a-zA-Z\s:-]|contoh|demo\s+|sampel\s+/i"],
            'kode_desa' => [
                'required',
                "exists:kode_wilayah,kode_desa,kode_kec,{$this->kode_kecamatan},kode_kab,{$this->kode_kabupaten},kode_prov,{$this->kode_provinsi}",
                "unique:desa,kode_desa,{$this->kode_desa},kode_desa",
            ],
            'kode_pos' => 'nullable',
            'nama_kecamatan' => ['required', "not_regex:/[^\.a-zA-Z\s:-]|contoh|demo\s+|sampel\s+/i"],
            'kode_kecamatan' => [
                'required',
                'exists:kode_wilayah,kode_kec',
            ],
            'nama_kabupaten' => ['required', "not_regex:/[^\.a-zA-Z\s:-]|contoh|demo\s+|sampel\s+/i"],
            'kode_kabupaten' => [
                'required',
                'exists:kode_wilayah,kode_kab',
            ],
            'nama_provinsi' => ['required', "not_regex:/[^\.a-zA-Z\s:-]|contoh|demo\s+|sampel\s+/i"],
            'kode_provinsi' => [
                'required',
                'exists:kode_wilayah,kode_prov',
            ],
            'lat' => 'nullable',
            'lng' => 'nullable',
            'alamat_kantor' => 'required',
            'email_desa' => 'sometimes',
            'telepon' => 'sometimes',
            'url' => ['required', 'url', "not_regex:/{$this->listAbaikanDomain()}/"],
            'ip_address' => 'required',
            'external_ip' => 'sometimes',
            'version' => 'required',
            'jml_surat_tte' => 'sometimes',
            'modul_tte' => [
                'sometimes',
                Rule::in(['0', '1']),
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    protected function prepareForValidation()
    {
        $type = $this->isLocal($this->only(['url', 'ip_address']));

        // Merge request attribute.
        $this->merge([
            // Request attribute for table desa.
            'kode_desa' => kode_wilayah($this->kode_desa),
            'kode_kecamatan' => kode_wilayah($this->kode_kecamatan),
            'kode_kabupaten' => kode_wilayah($this->kode_kabupaten),
            'kode_provinsi' => kode_wilayah($this->kode_provinsi),
            'opensid_valid' => preg_replace('/-premium.*|pasca-|-pasca/', '', $this->version),
            "url_{$type}" => fixDomainName($this->url),
            "ip_{$type}" => $this->ip_address,
            "versi_{$type}" => $this->version,
            "tgl_akses_{$type}" => now(),
            "tgl_rekam_{$type}" => now(),

            // Request attribute for table akses.
            'url_referrer' => $this->url,
            'request_uri' => $this->getRequestUri(),
            'client_ip' => $this->ip(),
            'external_ip' => $this->external_ip ?: $this->ip(),
            'opensid_version' => $this->version,
            'tgl' => now(),
        ]);
    }

    /**
     * Periksa lokal/hosting attribute.
     *
     * @param array $attributes
     * @return string
     */
    protected function isLocal(array $attributes)
    {
        return is_local($attributes['url'])
            ? 'lokal'
            : 'hosting';
    }

    /**
     * List abikan domain.
     *
     * @return string
     */
    protected function listAbaikanDomain()
    {
        return abaikan_domain('opensid');
    }

    /**
     * Request where data attribute.
     *
     * @return array
     */
    public function requestWhere()
    {
        return $this->only([
            'kode_desa', 'kode_kecamatan', 'kode_kabupaten', 'kode_provinsi',
        ]);
    }

    /**
     * Request data attribute.
     *
     * @return array
     */
    public function requestData()
    {
        $kontak = [
            'nama' => $this->nama_kontak,
            'hp' => $this->hp_kontak,
            'jabatan' => $this->jabatan_kontak
        ];
        $this->merge([
            'nama_kabupaten' => Region::where('region_code', $this->kode_kabupaten)->first()->region_name ?? $this->nama_kabupaten,
            'nama_kecamatan' => Region::where('region_code', $this->kode_kecamatan)->first()->region_name ?? $this->nama_kecamatan,
            'nama_desa' => Region::where('region_code', $this->kode_desa)->first()->region_name ?? $this->nama_desa,
            'kontak' => $kontak
        ]);

        return $this->only([
            'kode_pos',
            'nama_desa',
            'nama_kecamatan',
            'nama_kabupaten',
            'nama_provinsi',
            'lat',
            'lng',
            'alamat_kantor',
            'ip_lokal',
            'ip_hosting',
            'versi_lokal',
            'versi_hosting',
            'tgl_rekam_lokal',
            'tgl_rekam_hosting',
            'tgl_akses_lokal',
            'tgl_akses_hosting',
            'url_lokal',
            'url_hosting',
            'opensid_valid',
            'email_desa',
            'telepon',
            'jml_surat_tte',
            'modul_tte',
            'jml_penduduk',
            'jml_artikel',
            'jml_surat_keluar',
            'jml_bantuan',
            'jml_mandiri',
            'jml_pengguna',
            'jml_unsur_peta',
            'jml_persil',
            'jml_dokumen',
            'jml_keluarga',
            'anjungan',
            'kontak'
        ]);
    }
}
