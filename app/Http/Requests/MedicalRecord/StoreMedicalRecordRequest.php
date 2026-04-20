<?php

namespace App\Http\Requests\MedicalRecord;

use Illuminate\Foundation\Http\FormRequest;

class StoreMedicalRecordRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'appointment_id' => 'required|exists:appointments,id',
            'diagnosis'      => 'nullable|string|max:1000',
            'treatment'      => 'nullable|string|max:1000',
            'medications'    => 'nullable|array',
            'medications.*'  => 'string',
            'notes'          => 'nullable|string|max:1000',
        ];
    }
}
