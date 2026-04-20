<?php

namespace App\Http\Requests\Patient;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePatientRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'date_of_birth' => 'sometimes|nullable|date|before:today',
            'gender'        => 'sometimes|nullable|string|in:Laki-laki,Perempuan',
            'address'       => 'sometimes|nullable|string|max:500',
            'phone'         => 'sometimes|nullable|string|max:20',
        ];
    }
}
