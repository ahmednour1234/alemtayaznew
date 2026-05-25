<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateAdminRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'email', Rule::unique('admins', 'email')->ignore($this->route('admin'))],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'active'   => ['boolean'],
            'roles'    => ['nullable', 'array'],
            'roles.*'  => ['exists:roles,id'],
        ];
    }
}
