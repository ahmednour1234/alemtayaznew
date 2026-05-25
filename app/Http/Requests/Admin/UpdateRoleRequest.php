<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateRoleRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'name'            => ['required', 'string', 'max:255'],
            'slug'            => ['required', 'string', 'max:100', Rule::unique('roles', 'slug')->ignore($this->route('role'))],
            'description'     => ['nullable', 'string'],
            'active'          => ['boolean'],
            'permissions'     => ['nullable', 'array'],
            'permissions.*'   => ['exists:permissions,id'],
        ];
    }
}
