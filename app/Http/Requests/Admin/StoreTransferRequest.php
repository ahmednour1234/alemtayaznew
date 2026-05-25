<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreTransferRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'from_branch_id' => ['nullable', 'exists:branches,id', 'different:to_branch_id'],
            'to_branch_id'   => ['nullable', 'exists:branches,id'],
            'amount'         => ['required', 'numeric', 'min:0.01'],
            'date'           => ['required', 'date'],
            'description'    => ['nullable', 'string'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($v) {
            if (empty($this->from_branch_id) && empty($this->to_branch_id)) {
                $v->errors()->add('from_branch_id', 'يجب تحديد فرع المصدر أو فرع الوجهة.');
            }
        });
    }
}
