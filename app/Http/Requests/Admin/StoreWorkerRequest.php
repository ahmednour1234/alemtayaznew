<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreWorkerRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'name'            => ['nullable', 'string', 'max:255'],
            'passport_number' => ['nullable', 'string', 'max:50'],
            'nationality_id'  => ['nullable', 'exists:nationalities,id'],
            'profession'      => ['nullable', 'string', 'max:100'],
            'gender'          => ['nullable', 'in:female,male'],
            'experience'      => ['nullable', 'in:none,1-3,3-5,5+'],
            'religion'        => ['nullable', 'string', 'max:50'],
            'age'             => ['nullable', 'integer', 'min:18', 'max:60'],
            'phone'           => ['nullable', 'string', 'max:30'],
            'cv'              => ['nullable', 'file', 'mimes:pdf', 'max:10240'],
            'passport_image'  => ['nullable', 'image', 'mimes:jpeg,jpg,png,webp', 'max:5120'],
            'status'          => ['nullable', 'in:available,reserved,assigned'],
            'branch_id'       => ['nullable', 'exists:branches,id'],
            'notes'           => ['nullable', 'string', 'max:2000'],
        ];
    }
}
