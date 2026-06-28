<?php

namespace App\Http\Requests\Admin;

use App\Models\Client;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateClientRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    /** national_id is encrypted → uniqueness is enforced against its hash column. */
    protected function prepareForValidation(): void
    {
        if (filled($this->national_id)) {
            $this->merge(['national_id_hash' => Client::hashPii($this->national_id)]);
        }
    }

    public function rules(): array
    {
        $id = $this->route('client');
        return [
            'name'                    => ['required', 'string', 'max:255'],
            'national_id'             => ['nullable', 'string', 'max:20'],
            'national_id_hash'        => ['nullable', Rule::unique('clients', 'national_id_hash')->ignore($id)],
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
