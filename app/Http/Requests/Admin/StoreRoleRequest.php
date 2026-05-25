<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreRoleRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'name'            => ['required', 'string', 'max:255'],
            'slug'            => ['required', 'string', 'max:100', 'unique:roles,slug'],
            'description'     => ['nullable', 'string'],
            'active'          => ['boolean'],
            'permissions'     => ['nullable', 'array'],
            'permissions.*'   => ['exists:permissions,id'],
        ];
    }
}
