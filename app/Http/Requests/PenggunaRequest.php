<?php

namespace App\Http\Requests;

use Spatie\Permission\Models\Role;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

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
            'role_id' => 'required',
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
        // tambahkan required pada provinsi_akses dan kabupaten_akses jika role adalah admin wilayah
        $adminWilayah = Role::where('name', 'Admin Wilayah')->first();
        if($adminWilayah && $adminWilayah->id == $this->input('role_id')) {
            $rules['provinsi_akses'] = 'required';
            $rules['kabupaten_akses'] = 'required';
        } else {
            $rules['provinsi_akses'] = 'nullable';
            $rules['kabupaten_akses'] = 'nullable';
            $this->request->remove('kabupaten_akses');
            $this->request->remove('provinsi_akses');
        }
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
