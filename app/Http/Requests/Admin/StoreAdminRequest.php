<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreAdminRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'name'      => ['required', 'string', 'max:255'],
            'email'     => ['required', 'email', 'unique:admins,email'],
            'password'  => ['required', 'string', 'min:8', 'confirmed'],
            'active'    => ['boolean'],
            'branch_id' => ['nullable', 'exists:branches,id'],
            'roles'     => ['nullable', 'array'],
            'roles.*'   => ['exists:roles,id'],
        ];
    }
}
