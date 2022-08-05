<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegionRequest extends FormRequest
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
            'region_code' => 'required|string|min:2|max:15',
            'region_name' => 'required|string|min:1|max:80',
            'parent_code' => 'required|string|min:2|max:15',
            'keterangan' => 'max:250',
        ];
    }
}
