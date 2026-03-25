<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class WilayahBoundarySearchRequest extends FormRequest
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
            'q' => 'required|string|min:2|max:100',
            'level' => 'sometimes|in:prov,kab,kec,kel',
            'limit' => 'sometimes|integer|min:1|max:50',
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
            'q.required' => 'Search query is required',
            'q.min' => 'Search query must be at least 2 characters',
            'q.max' => 'Search query must not exceed 100 characters',
            'level.in' => 'Level must be one of: prov, kab, kec, kel',
            'limit.min' => 'Limit must be at least 1',
            'limit.max' => 'Limit must not exceed 50',
        ];
    }
}