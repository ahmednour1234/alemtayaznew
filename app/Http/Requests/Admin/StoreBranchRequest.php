<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreBranchRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'name'         => ['required', 'string', 'max:255'],
            'code'         => ['required', 'string', 'max:50', 'unique:branches,code'],
            'phone'        => ['nullable', 'string', 'max:30'],
            'address'      => ['nullable', 'string', 'max:500'],
            'city'         => ['nullable', 'string', 'max:100'],
            'manager_name' => ['nullable', 'string', 'max:255'],
            'active'       => ['boolean'],
            'public'       => ['boolean'],
        ];
    }
}
