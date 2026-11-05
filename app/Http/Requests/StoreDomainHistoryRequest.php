<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Validator;

class StoreDomainHistoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Gate::allows('is-admin');
    }

    public function rules(): array
    {
        return [
            'domain' => ['required', 'string', 'max:255'],
            'note' => ['nullable', 'string'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $domain = fixDomainName($this->input('domain', ''));

            if ($domain === '') {
                $validator->errors()->add('domain', 'Domain tidak valid.');

                return;
            }

            // Tolak domain lokal/IP sesuai permintaan issue (§8).
            if (is_local($domain) || filter_var($domain, FILTER_VALIDATE_IP)) {
                $validator->errors()->add('domain', 'Domain tidak valid (tidak boleh IP lokal atau alamat IP).');

                return;
            }

            // Validasi format hostname sederhana
            if (! preg_match('/^(?!-)(?:[a-z0-9-]{1,63}(?<!-)\.)+[a-z]{2,63}$/i', $domain)) {
                $validator->errors()->add('domain', 'Domain tidak valid.');
            }
        });
    }

    public function getNormalizedDomain(): string
    {
        return fixDomainName($this->input('domain', ''));
    }
}
