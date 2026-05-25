<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateBranchRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'name'         => ['required', 'string', 'max:255'],
            'code'         => ['required', 'string', 'max:50', Rule::unique('branches', 'code')->ignore($this->route('branch'))],
            'phone'        => ['nullable', 'string', 'max:30'],
            'address'      => ['nullable', 'string', 'max:500'],
            'city'         => ['nullable', 'string', 'max:100'],
            'manager_name' => ['nullable', 'string', 'max:255'],
            'active'       => ['boolean'],
        ];
    }
}
