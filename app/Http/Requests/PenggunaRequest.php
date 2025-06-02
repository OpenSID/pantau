<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;
use Tests\Feature\Auth\PasswordConfirmationTest;

class PenggunaRequest extends FormRequest
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
        $rules = [
            'id_grup' => 'required',
            'name' => 'required|max:255',
            'username' => 'required|max:255|unique:users,username',
            'email' => 'required|max:255|email|unique:users,email',
            'password' => Password::min(8)
                ->letters()
                ->numbers()
                ->symbols()
                ->mixedCase()
                ->uncompromised(),
            'password_confirmation' => 'required_with:password|same:password',
        ];
        if ($this->isMethod('put') || $this->isMethod('patch')) {
            $id = $this->route('akun_pengguna');
            $rules['username'] = 'required|max:255|unique:users,username,' . $id;
            $rules['email'] = 'required|max:255|email|unique:users,email,' . $id;
            unset($rules['password_confirmation']);
            unset($rules['password']);
        }
        return $rules;
    }

    public function attributes()
    {
        return [
            'password_confirmation' => 'Konfirmasi Password',
        ];
    }
}
