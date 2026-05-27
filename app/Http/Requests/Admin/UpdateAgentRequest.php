<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAgentRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'name'           => ['required', 'string', 'max:255'],
            'phone'          => ['required', 'string', 'max:20'],
            'email'          => ['nullable', 'email', 'max:255'],
            'nationality_id' => ['nullable', 'exists:nationalities,id'],
            'document'       => ['nullable', 'file', 'mimes:pdf', 'max:10240'],
            'notes'          => ['nullable', 'string', 'max:2000'],
        ];
    }
}
