<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegionKecamatanRequest extends FormRequest
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
            'region_code' => 'required|string|regex:/\d{2}\.\d{2}\.\d{2}/',
            'region_name' => 'required|string|min:1|max:80',
            'parent_code' => 'required|string|regex:/\d{2}\.\d{2}/',
            'keterangan' => 'max:250',
        ];
    }

    public function messages()
    {
        return [
            'region_code.regex' => 'Format kode kecamatan tidak sesuai, contoh yang benar 34.34.01',
        ];
    }
}
