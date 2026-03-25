<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class WilayahBoundaryIndexRequest extends FormRequest
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
        return [
            'level' => 'sometimes|in:prov,kab,kec,kel',
            'kode' => 'sometimes|string|max:13',
            'search' => 'sometimes|string|max:100',
            'page' => 'sometimes|integer|min:1',
            'per_page' => 'sometimes|integer|min:1|max:100',
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
            'level.in' => 'Level must be one of: prov, kab, kec, kel',
            'kode.max' => 'Kode must not exceed 13 characters',
            'search.max' => 'Search term must not exceed 100 characters',
            'page.min' => 'Page must be at least 1',
            'per_page.min' => 'Per page must be at least 1',
            'per_page.max' => 'Per page must not exceed 100',
        ];
    }
}