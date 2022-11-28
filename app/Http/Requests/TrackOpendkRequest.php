<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TrackOpendkRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'nama_kecamatan' => ['required', "not_regex:/[^\.a-zA-Z\s:-]|contoh|demo\s+|sampel\s+/i"],
            'kode_kecamatan' => [
                'required',
                'exists:kode_wilayah,kode_kec',
                "unique:opendk,kode_kecamatan,{$this->kode_kecamatan},kode_kecamatan",
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
            'jumlah_desa' => 'sometimes',
            'jumlah_penduduk' => 'sometimes',
            'jumlah_keluarga' => 'sometimes',
            'peta_wilayah' => 'sometimes',
            'url' => ['required', 'url', "not_regex:/{$this->listAbaikanDomain()}/"],
            'desa' => 'sometimes',
            'batas_wilayah' => 'sometimes',
            'alamat' => 'sometimes',
            'jumlahdesa_sinkronisasi' => 'sometimes',
            'nama_camat' => 'sometimes',
        ];
    }

    /**
     * {@inheritdoc}
     */
    protected function prepareForValidation()
    {
        // Merge request attribute.
        $this->merge([
            'url' => $this->url,
            'tgl_rekam' => now(),
        ]);
    }

    /**
     * Request where data attribute.
     *
     * @return array
     */
    public function requestWhere()
    {
        return $this->only([
            'kode_kecamatan', 'kode_kabupaten', 'kode_provinsi',
        ]);
    }

    /**
     * Request data attribute.
     *
     * @return array
     */
    public function requestData()
    {
        $this->merge(['url' => fixDomainName($this->url)]);

        return $this->only([
            'kode_kecamatan',
            'kode_kabupaten',
            'kode_provinsi',
            'nama_kecamatan',
            'nama_kabupaten',
            'nama_provinsi',
            'url',
            'versi',
            'jumlah_desa',
            'jumlah_penduduk',
            'jumlah_keluarga',
            'peta_wilayah',
            'sebutan_wilayah',
            'tgl_rekam',
            'desa',
            'batas_wilayah',
            'alamat',
            'jumlahdesa_sinkronisasi',
            'nama_camat',
            'desa',
            'batas_wilayah',
            'alamat',
            'jumlahdesa_sinkronisasi',
            'nama_camat',
        ]);
    }

    /**
     * List abikan domain.
     *
     * @return string
     */
    protected function listAbaikanDomain()
    {
        return config('tracksid.abaikan_opendk');
    }
}
