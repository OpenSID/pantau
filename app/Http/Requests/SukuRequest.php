<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SukuRequest extends FormRequest
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
            'tbl_region_id' => [
                'required',
                'numeric',
                'exists:tbl_regions,region_code',
            ],
            'adat_id' => [
                'required',
                'numeric',
                'exists:adats,id',
            ],
            'name' => 'required|string|min:1|max:100',
        ];
    }
}
