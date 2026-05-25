<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateIncomeRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'branch_id'        => ['required', 'exists:branches,id'],
            'income_type_id'   => ['required', 'exists:income_types,id'],
            'amount'           => ['required', 'numeric', 'min:0.01'],
            'date'             => ['required', 'date'],
            'payment_method'   => ['required', 'in:cash,bank_transfer,card,other'],
            'reference_number' => ['nullable', 'string', 'max:100'],
            'description'      => ['nullable', 'string'],
            'attachment'       => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
        ];
    }
}
