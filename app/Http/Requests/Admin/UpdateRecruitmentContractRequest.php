<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRecruitmentContractRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'client_id'            => ['nullable', 'exists:clients,id'],
            'branch_id'            => ['required', 'exists:branches,id'],
            'request_date'         => ['required', 'date'],

            'visa_image'           => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
            'visa_type'            => ['nullable', 'in:domestic,rehabilitation'],
            'visa_number'          => ['nullable', 'string', 'max:100'],
            'arrival_airport_id'   => ['nullable', 'exists:airports,id'],
            'origin_nationality_id' => ['nullable', 'exists:nationalities,id'],
            'delivery_airport_id'  => ['nullable', 'exists:airports,id'],

            'musaned_number'       => ['nullable', 'string', 'max:100'],
            'musaned_date'         => ['nullable', 'date'],
            'musaned_file'         => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:10240'],

            'worker_id'            => ['nullable', 'exists:workers,id'],
            'e_doc_number'         => ['nullable', 'string', 'max:100'],
            'agent_id'             => ['nullable', 'exists:agents,id'],
            'current_department'   => ['nullable', 'in:customer_service,accounts,coordination'],

            'payment_status'       => ['nullable', 'in:pending,partial,full'],
            'total_cost'           => ['nullable', 'numeric', 'min:0'],

            'arrival_date'         => ['nullable', 'date'],
            'trial_end_date'       => ['nullable', 'date'],
            'contract_end_date'    => ['nullable', 'date'],

            'notes'                => ['nullable', 'string'],
            'client_sms'           => ['nullable', 'boolean'],
            'client_rating'        => ['nullable', 'boolean'],
            'rating_image'         => ['nullable', 'file', 'mimes:jpg,jpeg,png', 'max:5120'],

            // Status update fields (handled separately)
            'update_status'        => ['nullable', 'integer', 'min:1', 'max:15'],
            'status_date'          => ['nullable', 'date'],
            'whatsapp_message'     => ['nullable', 'string', 'max:1000'],
        ];
    }
}
