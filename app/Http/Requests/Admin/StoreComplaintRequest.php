<?php

namespace App\Http\Requests\Admin;

use App\Models\Complaint;
use Illuminate\Foundation\Http\FormRequest;

class StoreComplaintRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'contract_id'       => ['nullable', 'integer', 'exists:recruitment_contracts,id'],
            'contract_type'     => ['nullable', 'string', 'max:50'],
            'client_id'         => ['nullable', 'integer', 'exists:clients,id'],
            'worker_id'         => ['nullable', 'integer', 'exists:workers,id'],
            'branch_id'         => ['nullable', 'integer', 'exists:branches,id'],

            'problem_type'      => ['required', 'in:' . implode(',', array_keys(Complaint::problemTypes()))],
            'description'       => ['required', 'string', 'max:5000'],
            'phone'             => ['nullable', 'string', 'max:30'],

            'assigned_admin_id' => ['nullable', 'integer', 'exists:admins,id'],
            'priority'          => ['nullable', 'in:' . implode(',', array_keys(Complaint::priorities()))],
            'status'            => ['nullable', 'in:' . implode(',', array_keys(Complaint::statuses()))],

            'on_musaned'        => ['nullable', 'boolean'],
            'musaned_number'    => ['nullable', 'string', 'max:100'],

            'resolution'        => ['nullable', 'string', 'max:5000'],
            'processed_at'      => ['nullable', 'date'],
            'resolved_at'       => ['nullable', 'date'],

            'attachments'       => ['nullable', 'array', 'max:10'],
            'attachments.*'     => ['file', 'max:10240'],
        ];
    }
}
