<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TrackMobileRequest extends FormRequest
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
            'id' => ['required'],
            'kode_desa' => [
                'required',
                'exists:kode_wilayah,kode_desa',
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    protected function prepareForValidation()
    {
        // Merge request attribute.
        $this->merge([
            'tgl_akses' => now(),
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
            'kode_desa', 'id',
        ]);
    }

    /**
     * Request data attribute.
     *
     * @return array
     */
    public function requestData()
    {
        return $this->only([
            'id',
            'kode_desa',
            'tgl_akses',
        ]);
    }
}
