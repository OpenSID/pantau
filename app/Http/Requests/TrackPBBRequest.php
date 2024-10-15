<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TrackPBBRequest extends FormRequest
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
            'url' => ['required', 'url', "not_regex:/{$this->listAbaikanDomain()}/"],
            'versi' => 'required',
        ];
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
     * Request data attribute.
     *
     * @return array
     */
    public function requestData()
    {
        $this->merge(['url' => fixDomainName($this->url)]);

        return $this->only([
            'kode_desa',
            'nama_desa',
            'kode_kecamatan',
            'kode_kabupaten',
            'kode_provinsi',
            'nama_kecamatan',
            'nama_kabupaten',
            'nama_provinsi',
            'url',
            'versi',
        ]);
    }
}
