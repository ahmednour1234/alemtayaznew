<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateHousingRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'branch_id'   => ['required', 'integer', 'exists:branches,id'],
            'admin_id'    => ['nullable', 'integer', 'exists:admins,id'],
            'name'        => ['required', 'string', 'max:255'],
            'address'     => ['nullable', 'string', 'max:255'],
            'capacity'    => ['nullable', 'integer', 'min:1'],
            'description' => ['nullable', 'string'],
            'active'      => ['nullable', 'boolean'],
        ];
    }
}
