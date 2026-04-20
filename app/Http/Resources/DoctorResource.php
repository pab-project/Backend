<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DoctorResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'             => $this->id,
            'user_id'        => $this->user_id,
            'name'           => $this->user?->name,
            'email'          => $this->user?->email,
            'specialization' => $this->specialization,
            'phone'          => $this->phone,
            'bio'            => $this->bio,
            'is_active'      => $this->user?->is_active,
            'created_at'     => $this->created_at?->toDateTimeString(),
        ];
    }
}
