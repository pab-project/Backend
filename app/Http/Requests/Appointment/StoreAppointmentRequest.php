<?php

namespace App\Http\Requests\Appointment;

use Illuminate\Foundation\Http\FormRequest;

class StoreAppointmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'time_slot_id' => [
                'required',
                'exists:time_slots,id',
                function ($attribute, $value, $fail) {
                    $slot = \App\Models\TimeSlot::find($value);
                    if ($slot && $slot->is_booked) {
                        $fail('Jadwal ini sudah dipesan oleh pasien lain.');
                    }
                },
            ],
            'notes' => 'nullable|string|max:500',
        ];
    }
}
