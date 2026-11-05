<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class SetActiveDomainHistoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Gate::allows('is-admin');
    }

    public function rules(): array
    {
        return [];
    }
}
