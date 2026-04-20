<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TimeSlotResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'         => $this->id,
            'doctor_id'  => $this->doctor_id,
            'date'       => $this->date,
            'start_time' => $this->start_time,
            'end_time'   => $this->end_time,
            'is_booked'  => $this->is_booked,
        ];
    }
}
