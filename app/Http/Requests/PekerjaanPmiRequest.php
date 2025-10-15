<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PekerjaanPmiRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $id = $this->route('pekerjaan_pmi');

        return [
            'nama' => 'required|string|max:255|unique:pekerjaan_pmi,nama,'.$id,
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'nama.required' => 'Nama pekerjaan harus diisi.',
            'nama.string' => 'Nama pekerjaan harus berupa teks.',
            'nama.max' => 'Nama pekerjaan tidak boleh lebih dari 255 karakter.',
            'nama.unique' => 'Nama pekerjaan sudah ada, silakan gunakan nama yang berbeda.',
        ];
    }
}
