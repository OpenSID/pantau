<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegionKabupatenRequest extends FormRequest
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
            'parent_code' => 'required|string|max:10',
            'region_code' => 'required|string|max:10|unique:tbl_regions,region_code,' . $this->route('kabupaten'),
            'region_name' => 'required|string|max:100',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'parent_code.required' => 'Provinsi harus dipilih.',
            'region_code.required' => 'Kode kabupaten harus diisi.',
            'region_code.unique' => 'Kode kabupaten sudah ada.',
            'region_name.required' => 'Nama kabupaten harus diisi.',
            'region_name.max' => 'Nama kabupaten maksimal 100 karakter.',
        ];
    }
}
