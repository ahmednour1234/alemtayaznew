<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreClientRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'name'                    => ['required', 'string', 'max:255'],
            'national_id'             => ['nullable', 'string', 'max:20', 'unique:clients,national_id'],
            'phone'                   => ['required', 'string', 'max:20'],
            'marital_status'          => ['required', 'in:single,married,divorced,widowed'],
            'classification'          => ['nullable', 'in:potential,confirmed,premium,blocked'],
            'national_id_image'       => ['nullable', 'image', 'max:5120'],
            'required_nationality_id' => ['nullable', 'exists:nationalities,id'],
            'worker_type'             => ['nullable', 'string', 'max:100'],
            'monthly_salary'          => ['nullable', 'numeric', 'min:0'],
            'branch_id'               => ['nullable', 'exists:branches,id'],
            'notes'                   => ['nullable', 'string', 'max:2000'],
        ];
    }
}
