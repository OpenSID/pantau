<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TrackOpenkabRequest extends FormRequest
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
            'nama_kab' => ['required', "not_regex:/[^\.a-zA-Z\s:-]|contoh|demo\s+|sampel\s+/i"],
            'kode_kab' => [
                'required',
                'exists:kode_wilayah,kode_kab',
            ],
            'nama_prov' => ['required', "not_regex:/[^\.a-zA-Z\s:-]|contoh|demo\s+|sampel\s+/i"],
            'kode_prov' => [
                'required',
                'exists:kode_wilayah,kode_prov',
            ],
            'nama_aplikasi' => 'required|string',
            'sebutan_kab' => 'required|string',
            'jumlah_desa' => 'required',
            'jumlah_penduduk' => 'required',
            'jumlah_keluarga' => 'required',
            'jumlah_rtm' => 'required',
            'jumlah_bantuan' => 'required',
            'url' => ['required', 'url', "not_regex:/{$this->listAbaikanDomain()}/"],
            'versi' => 'required',
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
            'kode_kab',
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
            'kode_kab',
            'nama_kab',
            'kode_prov',
            'nama_prov',
            'nama_aplikasi',
            'sebutan_kab',
            'url',
            'versi',
            'tgl_rekam',
            'jumlah_desa',
            'jumlah_penduduk',
            'jumlah_keluarga',
            'jumlah_rtm',
            'jumlah_bantuan',
        ]);
    }

    /**
     * List abikan domain.
     *
     * @return string
     */
    protected function listAbaikanDomain()
    {
        return abaikan_domain('openkab');
    }
}
