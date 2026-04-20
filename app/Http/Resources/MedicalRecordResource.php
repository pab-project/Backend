<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MedicalRecordResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'             => $this->id,
            'appointment_id' => $this->appointment_id,
            'patient'        => new PatientResource($this->whenLoaded('patient')),
            'doctor'         => new DoctorResource($this->whenLoaded('doctor')),
            'diagnosis'      => $this->diagnosis,
            'treatment'      => $this->treatment,
            'medications'    => $this->medications,
            'notes'          => $this->notes,
            'created_at'     => $this->created_at?->toDateTimeString(),
            'updated_at'     => $this->updated_at?->toDateTimeString(),
        ];
    }
}
