<?php

namespace App\Http\Requests\Doctor;

use Illuminate\Foundation\Http\FormRequest;

class UpdateDoctorRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'specialization' => 'sometimes|required|string|max:255',
            'phone'          => 'sometimes|nullable|string|max:20',
            'bio'            => 'sometimes|nullable|string|max:1000',
        ];
    }
}
