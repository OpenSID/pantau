<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class WilayahBoundaryGeojsonRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        // disable level kel, karena data terlalu besar
        return [
            //'level' => 'required|in:prov,kab,kec,kel',
            'level' => 'required|in:prov,kab,kec',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'level.required' => 'Level is required',
            'level.in' => 'Level must be one of: prov, kab, kec, kel',
        ];
    }
}
