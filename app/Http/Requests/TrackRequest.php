<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TrackRequest extends FormRequest
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
        $this->mergeRequestAttribute();

        return [
            "nama_desa" => ["required", "not_regex:/[^\.a-zA-Z\s:-]|contoh|demo\s+|sampel\s+/i"],
            "kode_desa" => [
                "required",
                "exists:kode_wilayah,kode_desa",
                "unique:desa,kode_desa,{$this->kode_desa},kode_desa",
            ],
            "kode_pos" => "required",
            "nama_kecamatan" => ["required", "not_regex:/[^\.a-zA-Z\s:-]|contoh|demo\s+|sampel\s+/i"],
            "kode_kecamatan" => [
                "required",
                "exists:kode_wilayah,kode_kec"
            ],
            "nama_kabupaten" => ["required", "not_regex:/[^\.a-zA-Z\s:-]|contoh|demo\s+|sampel\s+/i"],
            "kode_kabupaten" => [
                "required",
                "exists:kode_wilayah,kode_kab"
            ],
            "nama_provinsi" => ["required", "not_regex:/[^\.a-zA-Z\s:-]|contoh|demo\s+|sampel\s+/i"],
            "kode_provinsi" => [
                "required",
                "exists:kode_wilayah,kode_prov"
            ],
            "lat" => "required",
            "lng" => "required",
            "alamat_kantor" => "required",
            "email_desa" => "sometimes",
            "telepon" => "sometimes",
            "url" => ["required", "url", "not_regex:/{$this->listAbaikanDomain()}/"],
            "ip_address" => "required",
            "external_ip" => "sometimes",
            "version" => "required",
        ];
    }

    /**
     * List abikan domain.
     * 
     * @return string
     */
    protected function listAbaikanDomain()
    {
        return config('tracksid.abaikan');
    }

    /**
     * Merge request to attribute.
     * 
     * @return $this
     */
    protected function mergeRequestAttribute()
    {
        // Merge request attribute.
        $this->merge([
            // Request attribute for table desa.
            'ip_lokal' => [
                'url' => $this->url,
                'ip_address' => $this->ip_address,
            ],
            'ip_hosting' => [
                'url' => $this->url,
                'ip_address' => $this->ip_address,
            ],
            'versi_lokal' => [
                'url' => $this->url,
                'ip_address' => $this->ip_address,
                'version' => $this->version,
            ],
            'versi_hosting' => [
                'url' => $this->url,
                'ip_address' => $this->ip_address,
                'version' => $this->version,
            ],
            'url_lokal' => [
                'url' => $this->url,
                'ip_address' => $this->ip_address,
            ],
            'url_hosting' => [
                'url' => $this->url,
                'ip_address' => $this->ip_address,
            ],
            'tgl_rekam_lokal' => [
                'url' => $this->url,
                'ip_address' => $this->ip_address,
            ],
            'tgl_rekam_hosting' => [
                'url' => $this->url,
                'ip_address' => $this->ip_address,
            ],
            'tgl_akses_lokal' => [
                'url' => $this->url,
                'ip_address' => $this->ip_address,
            ],
            'tgl_akses_hosting' => [
                'url' => $this->url,
                'ip_address' => $this->ip_address,
            ],

            // Request attribute for table akses.
            'url_referrer' => $this->url,
            'request_uri' => $this->getRequestUri(),
            'client_ip' => $this->ip(),
            'external_ip' => $this->external_ip ?: $this->ip(),
            'opensid_version' => $this->version,
            'tgl' => now(),
        ]);

        return $this;
    }
}
