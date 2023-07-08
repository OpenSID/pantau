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
            'jml_desa' => 'sometimes',
            'jumlah_penduduk' => 'sometimes',
            'jumlah_keluarga' => 'sometimes',
            'peta_wilayah' => 'sometimes',
            'url' => ['required', 'url', "not_regex:/{$this->listAbaikanDomain()}/"],
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
            'desa',
            'jml_desa',
            'jumlah_penduduk',
            'jumlah_keluarga',
            'peta_wilayah',
            'sebutan_wilayah',
            'batas_wilayah',
            'jumlahdesa_sinkronisasi',
            'alamat',
            'jumlah_bantuan',
            'lat',
            'lng'
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
