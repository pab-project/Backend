<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PatientResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'            => $this->id,
            'user_id'       => $this->user_id,
            'name'          => $this->user?->name,
            'email'         => $this->user?->email,
            'date_of_birth' => $this->date_of_birth,
            'gender'        => $this->gender,
            'address'       => $this->address,
            'phone'         => $this->phone,
            'is_active'     => $this->user?->is_active,
            'created_at'    => $this->created_at?->toDateTimeString(),
        ];
    }
}
